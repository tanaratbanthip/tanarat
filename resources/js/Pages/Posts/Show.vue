<script setup>
import { Link } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

defineProps({
  post: Object,
})
</script>

<template>
  <BlogLayout>
    <article class="max-w-3xl mx-auto py-4">
      
      <!-- Top Actions Bar -->
      <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200/80 text-sm">
        <Link
          :href="route('home')"
          class="inline-flex items-center gap-2 font-medium text-slate-500 hover:text-indigo-600 transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          ย้อนกลับ
        </Link>

        <div class="flex items-center gap-3">
          <Link
            :href="route('posts.edit', post.slug || post.id)"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 font-medium text-xs transition"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            แก้ไขบทความ
          </Link>
        </div>
      </div>

      <!-- Article Header -->
      <header class="mb-10">
        <!-- Category & Date Badge -->
        <div class="flex items-center gap-2.5 mb-4 text-xs font-medium">
          <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full font-semibold">
            {{ post.category ? post.category.name : 'ทั่วไป' }}
          </span>
          <span class="text-slate-300">•</span>
          <span class="text-slate-400">
            {{ new Date(post.created_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }) }}
          </span>
        </div>

        <!-- Post Title -->
        <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-slate-900 leading-snug sm:leading-tight tracking-tight mb-6">
          {{ post.title }}
        </h1>

        <!-- Author Meta Card -->
        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm">
          <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
            T
          </div>
          <div>
            <p class="text-sm font-bold text-slate-800 leading-tight">Tanarat</p>
            <p class="text-xs text-slate-400 mt-0.5">ผู้เขียนบทความ</p>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <div v-if="post.image" class="mb-10 overflow-hidden rounded-3xl border border-slate-200/80 shadow-md">
        <img
          :src="'/storage/' + post.image"
          :alt="post.title"
          class="w-full max-h-[460px] object-cover"
        />
      </div>

      <!-- Article Body (HTML Rendered) -->
      <section
        class="prose prose-slate max-w-none text-slate-700 leading-loose text-base sm:text-lg space-y-6 break-words"
        v-html="post.content"
      ></section>

      <!-- Article Footer / Author End Note -->
      <div class="mt-14 pt-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
        <p>เผยแพร่ผ่านแพลตฟอร์ม MyNotes Blog</p>
        <Link
          :href="route('home')"
          class="font-semibold text-indigo-600 hover:text-indigo-800"
        >
          ← อ่านบทความอื่นเพิ่มเติม
        </Link>
      </div>

    </article>
  </BlogLayout>
</template>

<style>
/* จัดการระยะห่างของแท็ก HTML ภายใน prose ให้เป็นระเบียบ */
.prose p {
  margin-bottom: 1.5rem;
}
.prose h2, .prose h3 {
  color: #0f172a;
  font-weight: 700;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.prose ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 1.5rem;
}
.prose li {
  margin-bottom: 0.5rem;
}
</style>