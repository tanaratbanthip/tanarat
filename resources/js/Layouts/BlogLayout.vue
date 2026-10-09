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
    :class="route().current('home') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
    class="px-3.5 py-2 rounded-lg text-sm transition-colors"
  >
    หน้าแรก
  </Link>
  <Link
    :href="route('posts.index')"
    :class="route().current('posts.index') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
    class="px-3.5 py-2 rounded-lg text-sm transition-colors"
  >
    รวมบทความ
  </Link>
  <Link
    v-if="user"
    :href="route('bookmarks.index')"
    :class="route().current('bookmarks.*') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
    class="px-3.5 py-2 rounded-lg text-sm transition-colors"
  >
    ที่บันทึกไว้
  </Link>
  <!-- เพิ่มเมนูแดชบอร์ดและหมวดหมู่ -->
  <Link
    v-if="user"
    :href="route('dashboard')"
    :class="route().current('dashboard') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
    class="px-3.5 py-2 rounded-lg text-sm transition-colors"
  >
    แดชบอร์ด
  </Link>
  <Link
    v-if="user"
    :href="route('categories.index')"
    :class="route().current('categories.*') ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'"
    class="px-3.5 py-2 rounded-lg text-sm transition-colors"
  >
    หมวดหมู่
  </Link>
</nav>
          </div>

          <!-- User / Auth Action Controls -->
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

            <!-- Hamburger Button สำหรับมือถือ -->
            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="md:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition"
              aria-label="Toggle Navigation"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu (Drawer) -->
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
          รวมบทความ
        </Link>

        <!-- เพิ่มเมนูบทความที่บันทึกไว้ในจอมือถือ -->
        <Link
          v-if="user"
          :href="route('bookmarks.index')"
          @click="isMobileMenuOpen = false"
          :class="route().current('bookmarks.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600'"
          class="block px-3 py-2 rounded-lg text-base font-medium"
        >
          บทความที่บันทึกไว้
        </Link>
<Link
    v-if="user"
    :href="route('dashboard')"
    @click="isMobileMenuOpen = false"
    :class="route().current('dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600'"
    class="block px-3 py-2 rounded-lg text-base font-medium"
  >
    แดชบอร์ด
  </Link>
  <Link
    v-if="user"
    :href="route('categories.index')"
    @click="isMobileMenuOpen = false"
    :class="route().current('categories.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-600'"
    class="block px-3 py-2 rounded-lg text-base font-medium"
  >
    จัดการหมวดหมู่
  </Link>


        <div v-if="user" class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between px-3">
          <span class="text-sm font-bold text-slate-800">{{ user.name }}</span>
          <Link :href="route('logout')" method="post" as="button" class="text-xs text-rose-500 font-semibold hover:underline">
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
        class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border bg-white text-sm max-w-sm"
        :class="toastType === 'success' ? 'border-emerald-100 text-slate-800' : 'border-rose-100 text-slate-800'"
      >
        <span
          v-if="toastType === 'success'"
          class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
        </span>
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
    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
      <div class="flex items-center justify-center gap-4 mb-2">
        <a href="/sitemap.xml" target="_blank" class="hover:text-indigo-600 transition">Sitemap (XML)</a>
        <span>•</span>
        <a href="/feed" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
          </svg>
          RSS Feed
        </a>
      </div>
      <p>© 2026 MyNotes Blog. All rights reserved.</p>
    </footer>
  </div>
</template>