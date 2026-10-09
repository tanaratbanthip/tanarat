<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineProps({
  posts: Object,
})

const page = usePage()
const currentUser = computed(() => page.props.auth?.user)

// สถานะควบคุม Modal ลบบทความ
const showDeleteModal = ref(false)
const postToDelete = ref(null)
const isDeleting = ref(false)

const openDeleteModal = (post) => {
  postToDelete.value = post
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  postToDelete.value = null
}

const confirmDelete = () => {
  if (!postToDelete.value) return

  isDeleting.value = true
  router.delete(route('posts.destroy', postToDelete.value.slug || postToDelete.value.id), {
    onSuccess: () => closeDeleteModal(),
    onFinish: () => (isDeleting.value = false),
  })
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
    <div class="max-w-4xl mx-auto py-2">
      <!-- Header -->
      <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200/80">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">บล็อกของฉัน</h1>
          <p class="text-sm text-slate-500 mt-1">จัดการ แก้ไข และเขียนบทความทั้งหมดของคุณ</p>
        </div>
        <Link
          v-if="currentUser"
          :href="route('posts.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 transition-all duration-150"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>เขียนบทความใหม่</span>
        </Link>
      </div>

      <!-- Post List Cards -->
      <div class="space-y-5">
        <div
          v-for="post in posts.data"
          :key="post.id"
          class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 transition-all flex flex-col md:flex-row gap-6 items-start"
        >
          <div v-if="post.image" class="w-full md:w-48 h-36 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-100">
            <img :src="'/storage/' + post.image" :alt="post.title" class="w-full h-full object-cover" />
          </div>

          <div class="flex-1 w-full flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-2.5">
                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-xs font-semibold">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                
                <div v-if="currentUser && currentUser.id === post.user_id" class="flex items-center gap-3 text-xs">
                  <Link :href="route('posts.edit', post.slug || post.id)" class="font-medium text-slate-500 hover:text-indigo-600 transition">
                    แก้ไข
                  </Link>
                  <span class="text-slate-200">|</span>
                  <button @click="openDeleteModal(post)" class="font-medium text-rose-500 hover:text-rose-700 transition">
                    ลบ
                  </button>
                </div>
              </div>

              <Link :href="route('posts.show', post.slug || post.id)">
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 hover:text-indigo-600 transition mb-2 leading-snug">
                  {{ post.title }}
                </h2>
              </Link>

              <p class="text-slate-600 text-sm line-clamp-2 leading-relaxed">
                {{ stripTags(post.content) }}
              </p>
            </div>

            <div class="flex items-center justify-between pt-4 mt-3 border-t border-slate-100 text-xs text-slate-400">
              <div class="flex items-center gap-2">
                <span>{{ post.user ? post.user.name : 'ผู้เขียนทั่วไป' }}</span>
                <span>•</span>
                <span>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
              </div>
              <Link :href="route('posts.show', post.slug || post.id)" class="font-semibold text-indigo-600 hover:text-indigo-800">
                อ่านต่อ →
              </Link>
            </div>
          </div>
        </div>

        <!-- คอมโพเนนต์ Pagination -->
        <Pagination :links="posts.links" />
      </div>
    </div>

    <!-- Confirmation Modal สำหรับการลบ -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeDeleteModal"></div>

      <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </div>

        <div>
          <h3 class="text-lg font-bold text-slate-900">ยืนยันการลบบทความ?</h3>
          <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
            คุณแน่ใจหรือไม่ว่าต้องการลบ <span class="font-semibold text-slate-800">"{{ postToDelete?.title }}"</span> ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้
          </p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button
            @click="closeDeleteModal"
            class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition"
          >
            ยกเลิก
          </button>
          <button
            @click="confirmDelete"
            :disabled="isDeleting"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 shadow-sm shadow-rose-200 transition disabled:opacity-50"
          >
            {{ isDeleting ? 'กำลังลบ...' : 'ยืนยันลบบทความ' }}
          </button>
        </div>
      </div>
    </div>
  </BlogLayout>
</template>