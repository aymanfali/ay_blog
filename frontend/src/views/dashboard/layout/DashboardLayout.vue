<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  SidebarProvider,
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuItem,
  SidebarMenuButton,
  SidebarMenuSub,
  SidebarFooter,
  SidebarTrigger,
  SidebarInset,
} from '@/components/ui/sidebar'
import Collapsible from '@/components/ui/collapsible/Collapsible.vue'
import CollapsibleTrigger from '@/components/ui/collapsible/CollapsibleTrigger.vue'
import CollapsibleContent from '@/components/ui/collapsible/CollapsibleContent.vue'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'
import Avatar from '@/components/ui/avatar/Avatar.vue'
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue'
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue'
import ModeToggle from '@/components/ModeToggle.vue'
import {
  ChevronRight,
  ChevronsUpDown,
  Home,
  Settings,
  Newspaper,
  LogOut,
  UserCircle,
  Globe,
  Users,
} from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb'
import BreadcrumbLink from '@/components/ui/breadcrumb/BreadcrumbLink.vue'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'
import { useI18nLocaleSwitcher } from '@/locales/useI18nLocaleSwitcher'
import { useI18n } from 'vue-i18n'
import { Separator } from '@/components/ui/separator'
import { Button } from '@/components/ui/button'
import AppLogo from '@/components/AppLogo.vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const { t } = useI18n()

const route = useRoute()
const router = useRouter()

const { currentLocale } = useI18nLocaleSwitcher()
const auth = useAuthStore()
const { user } = storeToRefs(auth)

const goToProfile = () => {
  router.push('/profile')
}

const logout = () => {
  auth.logout()
  router.push('/auth/login')
}
const iconLocaleRotate = computed(() => (currentLocale.value === 'ar' ? 'rotate-180' : ''))
const sidebarSide = computed(() => (currentLocale.value === 'ar' ? 'right' : 'left'))
const submenuIndicatorSide = computed(() =>
  currentLocale.value === 'ar' ? 'border-r border-l-0' : 'border-l border-r-0',
)
// Active route detection
const isActive = (item: { url?: string; children?: { url: string }[] }) => {
  if (item.url && route.path === item.url) return true
  if (item.children) {
    return item.children.some((child) => route.path === child.url)
  }
  return false
}

// Menu button classes with RTL/LTR support
const menuButtonClass = (active: boolean, indent = 0) =>
  cn(
    'flex items-center p-3 text-sm font-medium rounded-md transition-colors',
    currentLocale.value === 'ar' ? `pr-${indent}` : `pl-${indent}`,
    active
      ? 'bg-muted text-foreground'
      : 'text-muted-foreground hover:bg-muted hover:text-foreground',
  )

// Sidebar structure
const sidebarGroups = computed(() => [
  {
    label: t('dashboard'),
    items: [
      { title: t('dashboard'), url: '/dashboard', icon: Home },
      { title: t('users'), url: '/dashboard/users', icon: Users },
      {
        title: t('blog'),
        icon: Newspaper,
        children: [
          { title: t('posts'), url: '/dashboard/posts' },
          { title: t('categories'), url: '/dashboard/categories' },
        ],
      },
    ],
  },
  {
    label: t('settings'),
    items: [{ title: t('preferences'), url: '/settings/preferences', icon: Settings }],
  },
])

// Generate breadcrumbs dynamically based on segments
const breadcrumbs = computed(() => {
  const segments = route.path.split('/').filter(Boolean)
  const paths: { path: string; name: string }[] = []

  let accumulated = ''

  for (const segment of segments) {
    accumulated += '/' + segment

    paths.push({
      path: accumulated,
      name: t(segment), // reactive to language changes
    })
  }

  return paths
})
</script>

<template>
  <SidebarProvider>
    <Sidebar collapsible="icon" :side="sidebarSide">
      <!-- Sidebar Header -->
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <RouterLink to="/dashboard">
              <SidebarMenuButton size="lg">
                <AppLogo />
              </SidebarMenuButton>
            </RouterLink>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <!-- Sidebar Content -->
      <SidebarContent>
        <template v-for="group in sidebarGroups" :key="group.label">
          <SidebarGroup>
            <SidebarGroupLabel>{{ group.label }}</SidebarGroupLabel>
            <SidebarGroupContent>
              <SidebarMenu>
                <template v-for="item in group.items" :key="item.title">
                  <!-- Non-collapsible -->
                  <SidebarMenuItem v-if="!item.children">
                    <SidebarMenuButton as-child>
                      <router-link :to="item.url" :class="menuButtonClass(isActive(item))">
                        <component :is="item.icon" class="w-4 h-4 me-2" />
                        <span>{{ item.title }}</span>
                      </router-link>
                    </SidebarMenuButton>
                  </SidebarMenuItem>

                  <!-- Collapsible -->
                  <SidebarMenuItem v-else>
                    <Collapsible default-open>
                      <CollapsibleTrigger as-child>
                        <SidebarMenuButton
                          :class="menuButtonClass(isActive(item))"
                          class="w-full justify-between"
                        >
                          <div class="flex items-center">
                            <component :is="item.icon" class="w-4 h-4 me-2" />
                            <span>{{ item.title }}</span>
                          </div>
                          <ChevronRight
                            class="w-4 h-4 transition-transform"
                            :class="iconLocaleRotate"
                          />
                        </SidebarMenuButton>
                      </CollapsibleTrigger>
                      <CollapsibleContent>
                        <SidebarMenuSub :class="submenuIndicatorSide">
                          <SidebarMenuItem v-for="child in item.children" :key="child.title">
                            <SidebarMenuButton as-child>
                              <router-link
                                :to="child.url"
                                :class="menuButtonClass(isActive(child), 8)"
                              >
                                <span>{{ child.title }}</span>
                              </router-link>
                            </SidebarMenuButton>
                          </SidebarMenuItem>
                        </SidebarMenuSub>
                      </CollapsibleContent>
                    </Collapsible>
                  </SidebarMenuItem>
                </template>
              </SidebarMenu>
            </SidebarGroupContent>
          </SidebarGroup>
        </template>
      </SidebarContent>

      <!-- Sidebar Footer -->
      <SidebarFooter>
        <SidebarMenu>
          <SidebarMenuItem>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <SidebarMenuButton>
                  <div class="flex gap-3 items-center w-full">
                    <Avatar
                      class="h-8 w-8 transition-all duration-300 group-data-[state=collapsed]:h-6 group-data-[state=collapsed]:w-6"
                    >
                      <AvatarImage src="{{" user?.avatarUrl }} />
                      <AvatarFallback>{{
                        user?.name
                          .split(' ')
                          .map((n) => n[0])
                          .join('')
                          .toUpperCase()
                      }}</AvatarFallback>
                    </Avatar>
                    <span> {{ user?.name }}</span>
                  </div>
                  <ChevronsUpDown class="me-auto" />
                </SidebarMenuButton>
              </DropdownMenuTrigger>
              <DropdownMenuContent side="top" style="width: var(--reka-popper-anchor-width)">
                <DropdownMenuItem @click="goToProfile"
                  ><UserCircle class="me-3" /><span>{{ $t('profile') }}</span></DropdownMenuItem
                >
                <DropdownMenuItem @click="logout" class="flex"
                  ><LogOut class="me-3" /> {{ $t('sign_out') }}</DropdownMenuItem
                >
              </DropdownMenuContent>
            </DropdownMenu>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarFooter>
    </Sidebar>

    <!-- Main Content -->
    <SidebarInset>
      <header
        class="flex flex-col md:flex-row justify-between shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon] border-b-muted border-b m-3 p-3"
      >
        <div class="flex h-10 items-center gap-3 px-4 py-2 w-full">
          <SidebarTrigger />

          <Separator orientation="vertical" />
          <!-- Dynamic Breadcrumbs -->
          <Breadcrumb>
            <BreadcrumbList>
              <template v-for="(crumb, index) in breadcrumbs" :key="crumb.path">
                <BreadcrumbItem>
                  <BreadcrumbLink v-if="index < breadcrumbs.length - 1" as-child>
                    <router-link :to="crumb.path">
                      {{ crumb.name }}
                    </router-link>
                  </BreadcrumbLink>

                  <BreadcrumbPage v-else>
                    {{ crumb.name }}
                  </BreadcrumbPage>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
              </template>
            </BreadcrumbList>
          </Breadcrumb>
        </div>
        <div class="flex gap-3 mx-5">
          <Button variant="outline"
            ><RouterLink to="/" class="flex gap-3 items-center"
              ><Globe /> <span class="hidden md:block">{{ $t('visit_website') }}</span></RouterLink
            ></Button
          >
          <LanguageSwitcher />
          <ModeToggle />
        </div>
      </header>
      <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
        <router-view />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
