<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)
const isMobileMenuOpen = ref(false)

// สถานะ Dark Mode
const isDark = ref(false)

onMounted(() => {
  isDark.value = document.documentElement.classList.contains('dark')
})

const toggleDarkMode = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.theme = 'dark'
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.theme = 'light'
  }
}

// ระบบ Toast Notification
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
let toastTimer = null

watch(
  flash,
  (newFlash) => {
    if (newFlash?.success) {
      toastMessage.value = newFlash.success
      toastType.value = 'success'
      triggerToast()
    } else if (newFlash?.error) {
      toastMessage.value = newFlash.error
      toastType.value = 'error'
      triggerToast()
    }
  },
  { deep: true }
)

const triggerToast = () => {
  showToast.value = true
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    showToast.value = false
  }, 4000)
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col font-sans transition-colors duration-200 selection:bg-indigo-500 selection:text-white">
    <!-- Navbar Header -->
    <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 transition-colors">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          
          <!-- Logo -->
          <div class="flex items-center gap-8">
            <Link :href="route('home')" class="flex items-center gap-2 group">
              <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-lg shadow-sm shadow-indigo-200 dark:shadow-none group-hover:scale-105 transition-transform">
                T
              </span>
              <span class="text-xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 dark:from-white dark:via-slate-200 dark:to-indigo-300 bg-clip-text text-transparent">
                The Thinking Canvas<span class="text-indigo-600 dark:text-indigo-400">.</span>
              </span>
            </Link>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1">
              <Link
                :href="route('home')"
                :class="route().current('home') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                หน้าแรก
              </Link>
              <Link
                :href="route('posts.index')"
                :class="route().current('posts.*') && !route().current('posts.create') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                รวมบทความ
              </Link>
              <Link
                v-if="user"
                :href="route('bookmarks.index')"
                :class="route().current('bookmarks.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                ที่บันทึกไว้
              </Link>

              <template v-if="user && user.role === 'admin'">
                <Link
                  :href="route('dashboard')"
                  :class="route().current('dashboard') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                  class="px-3.5 py-2 rounded-lg text-sm transition-colors"
                >
                  แดชบอร์ด
                </Link>
                <Link
                  :href="route('categories.index')"
                  :class="route().current('categories.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                  class="px-3.5 py-2 rounded-lg text-sm transition-colors"
                >
                  หมวดหมู่
                </Link>
              </template>
            </nav>
          </div>

          <!-- Right Action Controls -->
          <div class="flex items-center gap-2.5 sm:gap-3">
            <!-- ปุ่มสลับ Dark Mode -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
              aria-label="Toggle Theme"
            >
              <!-- Sun Icon -->
              <svg v-if="isDark" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <!-- Moon Icon -->
              <svg v-else class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <template v-if="user">
              <Link
                :href="route('posts.create')"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 dark:shadow-none transition"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">เขียนบทความ</span>
              </Link>

              <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                <Link
                  :href="route('profile.edit')"
                  class="flex items-center gap-2 p-1 pl-1.5 pr-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                  <div class="w-7 h-7 rounded-full overflow-hidden bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 flex items-center justify-center text-xs font-bold border border-slate-200 dark:border-slate-700 flex-shrink-0">
                    <img v-if="user.avatar" :src="'/storage/' + user.avatar" :alt="user.name" class="w-full h-full object-cover" />
                    <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ user.name }}</span>
                </Link>
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-rose-600 transition p-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                >
                  ออก
                </Link>
              </div>
            </template>

            <template v-else>
              <Link
                :href="route('login')"
                class="px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
              >
                เข้าสู่ระบบ
              </Link>
              <Link
                :href="route('register')"
                class="px-3.5 py-2 text-xs sm:text-sm font-semibold text-white bg-slate-900 dark:bg-indigo-600 hover:bg-black dark:hover:bg-indigo-700 rounded-xl transition"
              >
                สมัครสมาชิก
              </Link>
            </template>

            <!-- Hamburger Button -->
            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-show="isMobileMenuOpen" class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-4 space-y-1">
        <Link
          :href="route('home')"
          @click="isMobileMenuOpen = false"
          :class="route().current('home') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          หน้าแรก
        </Link>
        <Link
          :href="route('posts.index')"
          @click="isMobileMenuOpen = false"
          :class="route().current('posts.*') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          รวมบทความ
        </Link>
        <Link
          v-if="user"
          :href="route('bookmarks.index')"
          @click="isMobileMenuOpen = false"
          :class="route().current('bookmarks.*') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          บทความที่บันทึกไว้
        </Link>

        <template v-if="user && user.role === 'admin'">
          <Link
            :href="route('dashboard')"
            @click="isMobileMenuOpen = false"
            class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 dark:text-slate-300"
          >
            แดชบอร์ด
          </Link>
          <Link
            :href="route('categories.index')"
            @click="isMobileMenuOpen = false"
            class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 dark:text-slate-300"
          >
            จัดการหมวดหมู่
          </Link>
        </template>

        <div v-if="user" class="pt-3 mt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between px-3">
          <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false" class="text-sm font-bold text-slate-800 dark:text-slate-100">
            {{ user.name }}
          </Link>
          <Link :href="route('logout')" method="post" as="button" class="text-xs text-rose-500 font-semibold">
            ออกจากระบบ
          </Link>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-400 dark:text-slate-500 transition-colors">
      <div class="flex items-center justify-center gap-4 mb-2">
        <a href="/sitemap.xml" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Sitemap (XML)</a>
        <span>•</span>
        <a href="/feed" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
          </svg>
          RSS Feed
        </a>
      </div>
      <p>© 2026 The Thinking Canvas Blog. All rights reserved.</p>
    </footer>

    <!-- Toast Notification -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showToast"
        class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border bg-white dark:bg-slate-800 text-sm max-w-sm"
        :class="toastType === 'success' ? 'border-emerald-100 dark:border-emerald-900/50 text-slate-800 dark:text-slate-100' : 'border-rose-100 dark:border-rose-900/50 text-slate-800 dark:text-slate-100'"
      >
        <span
          v-if="toastType === 'success'"
          class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
        </span>
        <span
          v-else
          class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </span>

        <p class="font-medium text-xs sm:text-sm flex-1">{{ toastMessage }}</p>
        <button @click="showToast = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
    </transition>
  </div>
</template>