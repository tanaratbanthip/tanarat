<script setup>
import { ref, watch, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  posts: Object,
  categories: Array,
  filters: Object,
})

const page = usePage()
const currentUser = computed(() => page.props.auth?.user)

// ค่าสถานะการค้นหาและกรอง
const search = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const sort = ref(props.filters?.sort || 'latest')

let debounceTimeout = null

// ฟังก์ชันส่งคำขอ Query ไปยัง Backend
const applyFilters = () => {
  router.get(
    route('home'),
    {
      search: search.value || undefined,
      category: selectedCategory.value || undefined,
      sort: sort.value !== 'latest' ? sort.value : undefined,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}

// Watch ค้นหาแบบ Debounce 300ms
watch(search, () => {
  if (debounceTimeout) clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
})

// Watch หมวดหมู่และลำดับการจัดเรียง
watch([selectedCategory, sort], () => {
  applyFilters()
})

// คลิกเลือก/ยกเลิกหมวดหมู่
const toggleCategory = (categoryId) => {
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = ''
  } else {
    selectedCategory.value = categoryId
  }
}

// ล้างตัวกรองทั้งหมด
const resetFilters = () => {
  search.value = ''
  selectedCategory.value = ''
  sort.value = 'latest'
}

const stripTags = (html) => {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}
</script>

<template>
  <BlogLayout>
    <!-- Hero Profile Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-8 sm:p-10 mb-8 text-white shadow-xl">
      <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
        <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-extrabold shadow-inner text-indigo-200">
          T
        </div>
        <div class="space-y-2">
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Tanarat's Space</h1>
          <p class="text-indigo-200 text-sm max-w-lg leading-relaxed">
            พื้นที่จดบันทึก แลกเปลี่ยนความรู้ด้านการพัฒนาเว็บ เทคโนโลยี และข้อคิดในการทำงาน
          </p>
        </div>
      </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 mb-8 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <!-- ช่องค้นหาแบบเรียลไทม์ -->
        <div class="relative flex-1 w-full">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input
            v-model="search"
            type="text"
            placeholder="ค้นหาชื่อเรื่อง หรือเนื้อหาบทความ..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition"
          />
        </div>

        <!-- ตัวเลือกจัดเรียงลำดับ -->
        <div class="w-full sm:w-auto flex items-center gap-2">
          <select
            v-model="sort"
            class="w-full sm:w-auto py-2.5 px-3.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-white focus:border-indigo-500 outline-none cursor-pointer"
          >
            <option value="latest">เรียงจาก: ล่าสุด</option>
            <option value="oldest">เรียงจาก: เก่าสุด</option>
          </select>

          <!-- ปุ่มล้างตัวกรอง -->
          <button
            v-if="search || selectedCategory || sort !== 'latest'"
            @click="resetFilters"
            class="px-3 py-2.5 rounded-xl text-xs font-medium text-rose-500 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition whitespace-nowrap"
          >
            ล้างตัวกรอง
          </button>
        </div>
      </div>

      <!-- ปุ่มหมวดหมู่แบบ Interactive Pills -->
      <div v-if="categories && categories.length" class="flex items-center gap-2 overflow-x-auto pt-1 pb-1 scrollbar-none">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap mr-1">หมวดหมู่:</span>
        <button
          @click="selectedCategory = ''"
          :class="[
            !selectedCategory
              ? 'bg-indigo-600 text-white font-semibold shadow-sm shadow-indigo-200 border-indigo-600'
              : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200'
          ]"
          class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition whitespace-nowrap"
        >
          ทั้งหมด
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="toggleCategory(cat.id)"
          :class="[
            selectedCategory == cat.id
              ? 'bg-indigo-600 text-white font-semibold shadow-sm shadow-indigo-200 border-indigo-600'
              : 'bg-white text-slate-600 hover:border-indigo-300 hover:text-indigo-600 border-slate-200'
          ]"
          class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition whitespace-nowrap shadow-sm"
        >
          #{{ cat.name }}
        </button>
      </div>
    </div>

    <!-- Post Feed -->
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
          บทความ
          <span class="text-xs font-normal text-slate-400">({{ posts.total }} รายการ)</span>
        </h2>
      </div>

      <!-- กรณีไม่พบข้อมูล -->
      <div
        v-if="posts.data.length === 0"
        class="bg-white border border-slate-200 rounded-3xl p-12 text-center space-y-3"
      >
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">ไม่พบบทความที่คุณค้นหา</h3>
        <p class="text-xs text-slate-400">ลองเปลี่ยนคำค้นหา หรือเลือกหมวดหมู่อื่นดูใหม่อีกครั้ง</p>
      </div>

      <!-- รายการบทความ -->
      <div v-else class="grid gap-6">
        <article
          v-for="post in posts.data"
          :key="post.id"
          class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-50/50 transition-all duration-200 flex flex-col md:flex-row"
        >
          <div v-if="post.image" class="md:w-64 h-48 md:h-auto overflow-hidden bg-slate-100 flex-shrink-0">
            <img
              :src="'/storage/' + post.image"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
          </div>

          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <div class="flex items-center gap-2 text-xs font-semibold mb-2 text-slate-400">
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-md">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                <span>•</span>
                <span>{{ post.user ? post.user.name : 'ผู้เขียนทั่วไป' }}</span>
                <span>•</span>
                <span>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
              </div>

              <Link :href="route('posts.show', post.slug || post.id)">
                <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition leading-snug">
                  {{ post.title }}
                </h3>
              </Link>

              <p class="mt-2 text-slate-600 text-sm line-clamp-2 leading-relaxed">
                {{ stripTags(post.content) }}
              </p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs">
              <Link
                :href="route('posts.show', post.slug || post.id)"
                class="font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group-hover:translate-x-1 transition-transform"
              >
                อ่านฉบับเต็ม →
              </Link>
              <Link
                v-if="currentUser && currentUser.id === post.user_id"
                :href="route('posts.edit', post.slug || post.id)"
                class="text-slate-400 hover:text-slate-600"
              >
                แก้ไข
              </Link>
            </div>
          </div>
        </article>
      </div>

      <!-- ปุ่ม Pagination เปลี่ยนหน้า -->
      <Pagination :links="posts.links" />
    </div>
  </BlogLayout>
</template>