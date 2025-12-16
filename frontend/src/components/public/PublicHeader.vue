<script setup lang="ts">
import {
  NavigationMenu,
  NavigationMenuList,
  NavigationMenuItem,
  NavigationMenuTrigger,
  NavigationMenuContent,
  NavigationMenuLink,
} from '@/components/ui/navigation-menu'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import LanguageSwitcher from '../LanguageSwitcher.vue'
import ModeToggle from '../ModeToggle.vue'
import AppLogo from '../AppLogo.vue'
import { Button } from '../ui/button'
import { LogIn, MenuIcon, XIcon, ChevronDown } from 'lucide-vue-next'

const isMobileMenuOpen = ref(false)
const openSubmenu = ref<string | null>(null) // track open submenu

const router = useRouter()
const navLinks = [
  { name: 'Home', path: '/' },
  { name: 'About', path: '/about' },
  {
    name: 'Blog',
    path: '/blog',
    submenu: [
      { name: 'Latest Posts', path: '/blog/latest' },
      { name: 'Categories', path: '/blog/categories' },
    ],
  },
  { name: 'Contact', path: '/contact' },
]

const navigate = (path: string) => {
  router.push(path)
  isMobileMenuOpen.value = false // close mobile menu
  openSubmenu.value = null // close any open submenu
}

// Toggle mobile submenu
const toggleSubmenu = (name: string) => {
  openSubmenu.value = openSubmenu.value === name ? null : name
}
</script>

<template>
  <header class="fixed top-0 w-full shadow-md z-50 border-b border-b-muted bg-background">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Desktop layout -->
      <div class="hidden md:flex justify-between items-center h-16">
        <router-link to="/"> <AppLogo :show-name="true" /> </router-link>
        <NavigationMenu>
          <NavigationMenuList>
            <NavigationMenuItem v-for="link in navLinks" :key="link.name">
              <template v-if="link.submenu">
                <NavigationMenuTrigger>{{ link.name }}</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <div class="grid gap-2 p-2 w-2xs">
                    <NavigationMenuLink
                      v-for="sub in link.submenu"
                      :key="sub.name"
                      @click="navigate(sub.path)"
                    >
                      {{ sub.name }}
                    </NavigationMenuLink>
                  </div>
                </NavigationMenuContent>
              </template>
              <template v-else>
                <NavigationMenuLink @click="navigate(link.path)">
                  {{ link.name }}
                </NavigationMenuLink>
              </template>
            </NavigationMenuItem>
          </NavigationMenuList>
        </NavigationMenu>

        <div class="flex gap-2 items-center">
          <LanguageSwitcher />
          <ModeToggle />
          <router-link to="auth/login">
            <Button variant="outline">
              <LogIn class="md:me-2" /> <span class="hidden md:block">Login</span>
            </Button>
          </router-link>
        </div>
      </div>

      <!-- Mobile layout -->
      <div class="flex md:hidden justify-between items-center h-16 relative">
        <Button
          variant="outline"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
          class="p-2 rounded-md focus:outline-none focus:ring-2"
        >
          <span v-if="!isMobileMenuOpen"><MenuIcon /></span>
          <span v-else><XIcon /></span>
        </Button>

        <!-- Centered logo -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
          <router-link to="/"> <AppLogo /> </router-link>
        </div>

        <router-link to="auth/login">
          <Button variant="outline">
            <LogIn />
            <span class="hidden md:block">Login</span>
          </Button>
        </router-link>
      </div>
    </div>

    <!-- Mobile menu -->
    <transition name="slide-fade">
      <nav
        v-if="isMobileMenuOpen"
        class="md:hidden border-t border-muted max-h-screen overflow-y-auto"
      >
        <div class="px-2 pt-2 pb-3 space-y-1">
          <div v-for="link in navLinks" :key="link.name">
            <!-- Top-level link -->
            <button
              class="w-full flex justify-between items-center px-3 py-2 rounded-md font-medium transition"
              @click="link.submenu ? toggleSubmenu(link.name) : navigate(link.path)"
            >
              <span>{{ link.name }}</span>
              <ChevronDown
                v-if="link.submenu"
                :class="openSubmenu === link.name ? 'rotate-180' : ''"
                class="transition-transform"
              />
            </button>

            <!-- Mobile submenu -->
            <transition name="accordion">
              <div
                v-if="link.submenu && openSubmenu === link.name"
                class="pl-4 border-l border-muted space-y-1"
              >
                <button
                  v-for="sub in link.submenu"
                  :key="sub.name"
                  class="w-full text-left px-3 py-2 rounded-md font-medium transition text-sm"
                  @click="navigate(sub.path)"
                >
                  {{ sub.name }}
                </button>
              </div>
            </transition>
          </div>

          <!-- Mobile actions -->
          <div class="mt-3 flex justify-center gap-3">
            <LanguageSwitcher />
            <ModeToggle />
          </div>
        </div>
      </nav>
    </transition>
  </header>
</template>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.accordion-enter-active,
.accordion-leave-active {
  transition:
    max-height 0.3s ease,
    opacity 0.3s ease,
    padding 0.3s ease;
}
.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
  padding-top: 0;
  padding-bottom: 0;
}
.accordion-enter-to,
.accordion-leave-from {
  max-height: 500px;
  opacity: 1;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}
</style>
