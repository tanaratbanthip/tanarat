<script setup>
import { Link } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineProps({
  posts: Object,
})

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
      <div class="pb-6 mb-8 border-b border-slate-200/80">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">บทความที่บันทึกไว้</h1>
        <p class="text-sm text-slate-500 mt-1">คลังบทความที่คุณบันทึกไว้อ่านในภายหลัง</p>
      </div>

      <div v-if="posts.data.length === 0" class="bg-white border border-slate-200 rounded-3xl p-12 text-center space-y-3">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 mx-auto flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">ยังไม่มีบทความที่บันทึกไว้</h3>
        <p class="text-xs text-slate-400">คุณสามารถกดปุ่ม "บันทึกไว้อ่าน" ขณะอ่านบทความเพื่อเก็บไว้อ่านที่นี่ได้</p>
        <div class="pt-2">
          <Link :href="route('home')" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition">
            ไปที่หน้าแรก
          </Link>
        </div>
      </div>

      <div v-else class="space-y-5">
        <div
          v-for="post in posts.data"
          :key="post.id"
          class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:border-slate-300 transition-all flex flex-col md:flex-row gap-6 items-start"
        >
          <div v-if="post.image" class="w-full md:w-48 h-36 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-100">
            <img :src="'/storage/' + post.image" :alt="post.title" class="w-full h-full object-cover" />
          </div>

          <div class="flex-1 w-full flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-slate-400">
                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-md">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                <span>•</span>
                <span>{{ post.user ? post.user.name : 'ผู้เขียนทั่วไป' }}</span>
                <span>•</span>
                <span>อ่านประมาณ {{ post.reading_time }} นาที</span>
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

            <div class="flex items-center justify-between pt-4 mt-3 border-t border-slate-100 text-xs">
              <span class="text-slate-400">{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
              <Link :href="route('posts.show', post.slug || post.id)" class="font-semibold text-indigo-600 hover:text-indigo-800">
                อ่านต่อ →
              </Link>
            </div>
          </div>
        </div>

        <Pagination :links="posts.links" />
      </div>
    </div>
  </BlogLayout>
</template>