<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const isMobileMenuOpen = ref(false)
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
    <!-- Navbar Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 transition-all">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          
          <!-- Logo & Brand -->
          <div class="flex items-center gap-8">
            <Link :href="route('home')" class="flex items-center gap-2 group">
              <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-lg shadow-sm shadow-indigo-200 group-hover:scale-105 transition-transform">
                M
              </span>
              <span class="text-xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 bg-clip-text text-transparent">
                MyNotes<span class="text-indigo-600">.</span>
              </span>
            </Link>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1">
              <Link
                :href="route('home')"
                :class="[
                  route().current('home')
                    ? 'text-indigo-600 bg-indigo-50/70 font-semibold'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'
                ]"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                หน้าแรก
              </Link>
              <Link
                :href="route('posts.index')"
                :class="[
                  route().current('posts.*') && !route().current('posts.create')
                    ? 'text-indigo-600 bg-indigo-50/70 font-semibold'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'
                ]"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                จัดการบทความ
              </Link>
            </nav>
          </div>

          <!-- Action Button & Mobile Hamburger -->
          <div class="flex items-center gap-3">
            <Link
              :href="route('posts.create')"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 transition-all duration-150"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>เขียนบทความ</span>
            </Link>

            <!-- Mobile Hamburger Button -->
            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition"
              aria-label="Toggle Menu"
            >
              <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Drawer -->
      <div v-show="isMobileMenuOpen" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-1">
        <Link
          :href="route('home')"
          @click="isMobileMenuOpen = false"
          :class="route().current('home') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          หน้าแรก
        </Link>
        <Link
          :href="route('posts.index')"
          @click="isMobileMenuOpen = false"
          :class="route().current('posts.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          จัดการบทความ
        </Link>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
      <p>© 2026 MyNotes Blog. พัฒนาด้วย Laravel 11 + Vue 3 (Inertia.js)</p>
    </footer>
  </div>
</template>