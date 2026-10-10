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
    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-40 transition-colors">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"> <!-- ขยายความกว้างสูงสุดเป็น max-w-6xl -->
        <div class="flex justify-between h-16 items-center gap-4">

          <!-- Logo & Brand -->
          <div class="flex items-center gap-4 lg:gap-8 min-w-0">
            <Link :href="route('home')" class="flex items-center gap-2.5 group min-w-0">
              <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-lg shadow-sm shadow-indigo-200 dark:shadow-none group-hover:scale-105 transition-transform flex-shrink-0">
                T
              </span>
              <!-- ซ่อนข้อความแบรนด์เมื่อหน้าจอแคบมาก (md) แล้วแสดงเต็มในจอใหญ่ (lg) -->
              <span class="hidden lg:block text-xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 dark:from-white dark:via-slate-200 dark:to-indigo-300 bg-clip-text text-transparent truncate">
                The Thinking Canvas<span class="text-indigo-600 dark:text-indigo-400">.</span>
              </span>
            </Link>

            <!-- Desktop Navigation Links (เพิ่ม whitespace-nowrap และ flex-shrink-0) -->
            <nav class="hidden md:flex items-center space-x-1 flex-shrink-0 overflow-x-auto scrollbar-none">
              <Link
                :href="route('home')"
                :class="route().current('home') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="px-3 py-2 rounded-lg text-sm transition-colors whitespace-nowrap"
              >
                หน้าแรก
              </Link>
              <Link
                :href="route('posts.index')"
                :class="route().current('posts.*') && !route().current('posts.create') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="px-3 py-2 rounded-lg text-sm transition-colors whitespace-nowrap"
              >
                รวมบทความ
              </Link>

              <!-- ซ่อนเมนูบุ๊กมาร์กไว้ใน Dropdown มือถือแทน หากพื้นที่หน้าจอเหลือน้อย -->
              <Link
                v-if="user"
                :href="route('bookmarks.index')"
                :class="route().current('bookmarks.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="hidden lg:block px-3 py-2 rounded-lg text-sm transition-colors whitespace-nowrap"
              >
                ที่บันทึกไว้
              </Link>

              <template v-if="user && user.role === 'admin'">
                <Link
                  :href="route('dashboard')"
                  :class="route().current('dashboard') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                  class="px-3 py-2 rounded-lg text-sm transition-colors whitespace-nowrap"
                >
                  แดชบอร์ด
                </Link>
                <Link
                  :href="route('categories.index')"
                  :class="route().current('categories.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                  class="px-3 py-2 rounded-lg text-sm transition-colors whitespace-nowrap"
                >
                  หมวดหมู่
                </Link>
              </template>
            </nav>
          </div>

          <!-- Right Action Controls (กระชับและแตะง่ายบนมือถือ) -->
          <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
            <!-- ปุ่มสลับ Dark Mode -->
            <button
              @click="toggleDarkMode"
              class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition"
              aria-label="Toggle Theme"
            >
              <svg v-if="isDark" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <svg v-else class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
            </button>

            <!-- Desktop Only: เขียนบทความ & ข้อมูลผู้ใช้ -->
            <template v-if="user">
              <Link
                :href="route('posts.create')"
                class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 dark:shadow-none transition"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>เขียนบทความ</span>
              </Link>

              <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                <Link
                  :href="route('profile.edit')"
                  class="flex items-center gap-2 p-1 pl-1.5 pr-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
<div class="w-9 h-9 rounded-full overflow-hidden bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 flex items-center justify-center text-xs font-bold border border-slate-200 dark:border-slate-700 flex-shrink-0">
  <img
    v-if="user.avatar"
    :src="user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar"
    :alt="user.name"
    class="w-full h-full object-cover"
  />
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

            <!-- Desktop Only: Login / Register -->
            <template v-else>
              <div class="hidden sm:flex items-center gap-2">
                <Link
                  :href="route('login')"
                  class="px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                >
                  เข้าสู่ระบบ
                </Link>
                <Link
                  :href="route('register')"
                  class="px-3.5 py-2 text-xs sm:text-sm font-semibold text-white bg-slate-900 dark:bg-indigo-600 hover:bg-black dark:hover:bg-indigo-700 rounded-xl transition"
                >
                  สมัครสมาชิก
                </Link>
              </div>
            </template>

            <!-- Hamburger Button สำหรับมือถือ (แตะง่ายด้วยขนาด 40x40px) -->
            <button
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="md:hidden w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 transition"
              aria-label="Toggle Menu"
            >
              <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
              <svg v-else class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Backdrop Overlay เมื่อเปิดเมนูมือถือ -->
      <transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isMobileMenuOpen"
          class="fixed inset-0 top-16 bg-slate-900/40 backdrop-blur-sm z-30 md:hidden"
          @click="isMobileMenuOpen = false"
        ></div>
      </transition>

      <!-- Mobile Menu (Drawer เลื่อนลงมาอย่างนุ่มนวล พร้อมการ์ดผู้ใช้และปุ่มฟังก์ชัน) -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="-translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-2 opacity-0"
      >
      <!-- โค้ดเดิมใน BlogLayout.vue (บริเวณ Mobile Menu) -->
<div class="w-9 h-9 rounded-full overflow-hidden bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 flex items-center justify-center text-xs font-bold border border-slate-200 dark:border-slate-700 flex-shrink-0">
  <img v-if="user.avatar" :src="'/storage/' + user.avatar" :alt="user.name" class="w-full h-full object-cover" />
  <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
</div>
        <div
          v-if="isMobileMenuOpen"
          class="relative z-40 md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-6 space-y-3 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto"
        >
          <!-- การ์ดเขียนบทความบนมือถือ (เห็นชัดเจนแตะสะดวก) -->
          <Link
            v-if="user"
            :href="route('posts.create')"
            @click="isMobileMenuOpen = false"
            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-semibold text-sm text-white bg-indigo-600 hover:bg-indigo-700 active:scale-98 shadow-sm transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>เขียนบทความใหม่</span>
          </Link>

          <!-- รายการเมนูหลัก -->
          <div class="space-y-1">
            <Link
              :href="route('home')"
              @click="isMobileMenuOpen = false"
              :class="route().current('home') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-base font-medium transition"
            >
              <span>🏠</span>
              <span>หน้าแรก</span>
            </Link>
            <Link
              :href="route('posts.index')"
              @click="isMobileMenuOpen = false"
              :class="route().current('posts.*') && !route().current('posts.create') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-base font-medium transition"
            >
              <span>📚</span>
              <span>รวมบทความ</span>
            </Link>
            <Link
              v-if="user"
              :href="route('bookmarks.index')"
              @click="isMobileMenuOpen = false"
              :class="route().current('bookmarks.*') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-base font-medium transition"
            >
              <span>🔖</span>
              <span>บทความที่บันทึกไว้</span>
            </Link>

            <!-- หมวดผู้ดูแลระบบ -->
            <template v-if="user && user.role === 'admin'">
              <div class="pt-2 pb-1 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                ผู้ดูแลระบบ
              </div>
              <Link
                :href="route('dashboard')"
                @click="isMobileMenuOpen = false"
                :class="route().current('dashboard') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-base font-medium transition"
              >
                <span>📊</span>
                <span>แดชบอร์ด</span>
              </Link>
              <Link
                :href="route('categories.index')"
                @click="isMobileMenuOpen = false"
                :class="route().current('categories.*') ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-base font-medium transition"
              >
                <span>🏷️</span>
                <span>จัดการหมวดหมู่</span>
              </Link>
            </template>
          </div>

          <!-- กล่องข้อมูลสมาชิก / ปุ่มล็อกอินสำหรับมือถือ -->
          <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
            <template v-if="user">
              <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/50">
                <Link
                  :href="route('profile.edit')"
                  @click="isMobileMenuOpen = false"
                  class="flex items-center gap-2.5 min-w-0"
                >
                  <div class="w-9 h-9 rounded-full overflow-hidden bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 flex items-center justify-center text-xs font-bold border border-slate-200 dark:border-slate-700 flex-shrink-0">
                    <img v-if="user.avatar" :src="'/storage/' + user.avatar" :alt="user.name" class="w-full h-full object-cover" />
                    <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ user.name }}</p>
                    <p class="text-[11px] text-slate-400">แตะเพื่อแก้ไขโปรไฟล์</p>
                  </div>
                </Link>
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="px-3 py-1.5 text-xs text-rose-500 font-semibold hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition"
                >
                  ออกจากระบบ
                </Link>
              </div>
            </template>

            <template v-else>
              <div class="grid grid-cols-2 gap-2 pt-1">
                <Link
                  :href="route('login')"
                  @click="isMobileMenuOpen = false"
                  class="text-center py-2.5 rounded-xl text-sm font-semibold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 active:bg-slate-100 dark:active:bg-slate-800"
                >
                  เข้าสู่ระบบ
                </Link>
                <Link
                  :href="route('register')"
                  @click="isMobileMenuOpen = false"
                  class="text-center py-2.5 rounded-xl text-sm font-semibold bg-slate-900 dark:bg-indigo-600 text-white active:bg-black"
                >
                  สมัครสมาชิก
                </Link>
              </div>
            </template>
          </div>
        </div>
      </transition>
    </header>

    <!-- Main Content (ปรับ Padding ให้อ่านสบายบนมือถือ) -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-400 dark:text-slate-500 transition-colors">
      <div class="flex items-center justify-center gap-4 mb-2">
        <a href="/sitemap.xml" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 py-1 transition">Sitemap (XML)</a>
        <span>•</span>
        <a href="/feed" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 py-1 transition flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
          </svg>
          RSS Feed
        </a>
      </div>
      <p>© 2026 The Thinking Canvas Blog. All rights reserved.</p>
    </footer>

    <!-- Toast Notification (จัดให้แสดงเต็มความกว้างพร้อมเว้นขอบบนมือถือ ไม่ตกจอ) -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showToast"
        class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border bg-white dark:bg-slate-800 text-sm sm:max-w-sm"
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

        <p class="font-medium text-xs sm:text-sm flex-1 leading-snug">{{ toastMessage }}</p>
        <button @click="showToast = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
    </transition>
  </div>
</template>
