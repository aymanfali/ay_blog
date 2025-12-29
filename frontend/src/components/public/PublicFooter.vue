<script setup lang="ts">
import { useRouter } from 'vue-router'
import { Github, Twitter, Linkedin } from 'lucide-vue-next'
import AppLogo from '../AppLogo.vue'

const router = useRouter()

// Footer navigation sections
const footerLinks = [
  {
    title: 'Company',
    links: [
      { name: 'About Us', path: '/about' },
      { name: 'Careers', path: '/careers' },
      { name: 'Contact', path: '/contact' },
    ],
  },
  {
    title: 'Resources',
    links: [
      { name: 'Blog', path: '/blog' },
      { name: 'Docs', path: '/docs' },
      { name: 'Help Center', path: '/help' },
    ],
  },
  {
    title: 'Legal',
    links: [
      { name: 'Privacy Policy', path: '/privacy' },
      { name: 'Terms of Service', path: '/terms' },
    ],
  },
]

const navigate = (path: string) => {
  router.push(path)
}
</script>

<template>
  <footer class="border-t border-muted">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Logo & Description -->
        <div>
          <router-link to="/" class="text-2xl font-bold"> <AppLogo :show-name="true"/> </router-link>
          <p class="mt-2 text-sm">
            Building modern web applications with clean design and best practices.
          </p>

          <!-- Social Icons -->
          <div class="flex mt-4 gap-3">
            <a href="https://github.com" target="_blank" aria-label="GitHub" class="">
              <Github class="w-5 h-5" />
            </a>
            <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="">
              <Twitter class="w-5 h-5" />
            </a>
            <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn" class="">
              <Linkedin class="w-5 h-5" />
            </a>
          </div>
        </div>

        <!-- Footer Links -->
        <div v-for="section in footerLinks" :key="section.title">
          <h3 class="text-sm font-semibold tracking-wider uppercase">
            {{ section.title }}
          </h3>
          <ul class="mt-4 space-y-2">
            <li v-for="link in section.links" :key="link.name">
              <button class="text-sm transition" @click="navigate(link.path)">
                {{ link.name }}
              </button>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Legal -->
      <div
        class="mt-12 border-t border-muted pt-6 text-sm flex flex-col sm:flex-row justify-between items-center"
      >
        <span>© {{ new Date().getFullYear() }} MyBrand. All rights reserved.</span>
        <span class="mt-2 sm:mt-0">Made by Ayman F. Ali</span>
      </div>
    </div>
  </footer>
</template>
