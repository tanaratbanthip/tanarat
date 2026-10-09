<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)
const isMobileMenuOpen = ref(false)

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success') // 'success' หรือ 'error'
let toastTimer = null

// เฝ้าดูว่ามีข้อความ Flash ถูกส่งมาจาก Laravel หรือไม่
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
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
    <!-- Navbar Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 transition-all">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          
          <div class="flex items-center gap-8">
            <Link :href="route('home')" class="flex items-center gap-2 group">
              <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-lg shadow-sm shadow-indigo-200 group-hover:scale-105 transition-transform">
                M
              </span>
              <span class="text-xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 bg-clip-text text-transparent">
                MyNotes<span class="text-indigo-600">.</span>
              </span>
            </Link>

            <nav class="hidden md:flex items-center space-x-1">
              <Link
                :href="route('home')"
                :class="route().current('home') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                หน้าแรก
              </Link>
              <Link
                :href="route('posts.index')"
                :class="route().current('posts.*') && !route().current('posts.create') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
                class="px-3.5 py-2 rounded-lg text-sm transition-colors"
              >
                รวมบทความ
              </Link>
            </nav>
          </div>

          <div class="flex items-center gap-3">
            <template v-if="user">
              <Link
                :href="route('posts.create')"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 transition"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>เขียนบทความ</span>
              </Link>

              <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200">
                <span class="text-xs font-semibold text-slate-700 px-2 py-1 bg-slate-100 rounded-lg">
                  {{ user.name }}
                </span>
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="text-xs font-medium text-slate-500 hover:text-rose-600 transition p-1.5 rounded-md hover:bg-slate-100"
                >
                  ออกจากระบบ
                </Link>
              </div>
            </template>

            <template v-else>
              <Link
                :href="route('login')"
                class="px-3.5 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 transition"
              >
                เข้าสู่ระบบ
              </Link>
              <Link
                :href="route('register')"
                class="px-3.5 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-black rounded-xl transition"
              >
                สมัครสมาชิก
              </Link>
            </template>

            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-show="isMobileMenuOpen" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-2">
        <Link :href="route('home')" @click="isMobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600">
          หน้าแรก
        </Link>
        <Link :href="route('posts.index')" @click="isMobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600">
          รวมบทความ
        </Link>
        <div v-if="user" class="pt-2 border-t border-slate-100 flex items-center justify-between px-3">
          <span class="text-sm font-bold text-slate-800">{{ user.name }}</span>
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
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
      <p>© 2026 MyNotes Blog. All rights reserved.</p>
    </footer>

    <!-- Toast Notification (เด้งขึ้นมุมขวาล่าง) -->
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
        class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border bg-white text-sm max-w-sm"
        :class="toastType === 'success' ? 'border-emerald-100 text-slate-800' : 'border-rose-100 text-slate-800'"
      >
        <!-- ไอคอน Success -->
        <span
          v-if="toastType === 'success'"
          class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
        </span>
        <!-- ไอคอน Error -->
        <span
          v-else
          class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </span>

        <p class="font-medium text-xs sm:text-sm flex-1">{{ toastMessage }}</p>

        <button @click="showToast = false" class="text-slate-400 hover:text-slate-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition>
  </div>
</template>