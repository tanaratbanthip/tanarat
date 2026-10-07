<script setup>
import { Link, Head } from '@inertiajs/vue3'

defineProps({
  post: Object
})
</script>

<template>
  <Head :title="post.title" />

  <div class="min-h-screen bg-[#FDFDFC] text-slate-800 antialiased">
    <!-- แถบด้านบน -->
    <header class="border-b border-slate-100 bg-white/80 backdrop-blur sticky top-0 z-10">
      <div class="max-w-3xl mx-auto px-6 h-16 flex items-center justify-between">
        <Link href="/" class="text-sm text-slate-500 hover:text-slate-900 transition flex items-center space-x-1">
          <span>← กลับสู่หน้าแรก</span>
        </Link>
        <div class="flex items-center space-x-3 text-xs">
          <Link :href="`/posts/${post.id}/edit`" class="text-indigo-600 hover:underline">
            แก้ไขบทความนี้
          </Link>
        </div>
      </div>
    </header>

    <!-- เนื้อหาบทความ -->
    <main class="max-w-2xl mx-auto px-6 py-14">
      <article>
        <!-- หมวดหมู่และวันที่ -->
        <div class="flex items-center space-x-2 text-xs text-slate-400 mb-4">
          <span v-if="post.category" class="font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
            {{ post.category.name }}
          </span>
          <span>•</span>
          <time>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</time>
        </div>

        <!-- ชื่อเรื่อง -->
        <h1 class="text-3xl font-bold text-slate-900 leading-tight mb-8">
          {{ post.title }}
        </h1>

        <!-- เนื้อหาฉบับเต็ม -->
        <div class="text-slate-700 text-base leading-relaxed whitespace-pre-line space-y-4">
          {{ post.content }}
        </div>
      </article>
    </main>
  </div>
</template>