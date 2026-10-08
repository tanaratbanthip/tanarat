<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  post: Object,
})
</script>

<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <!-- แถบเมนูด้านบน -->
    <div class="flex justify-between items-center text-sm text-gray-500 mb-8">
      <Link :href="route('posts.index')" class="hover:underline">← กลับสู่หน้าแรก</Link>
      <Link :href="route('posts.edit', post.id)" class="text-blue-600 hover:underline">แก้ไขบทความนี้</Link>
    </div>

    <!-- หมวดหมู่และวันที่ -->
    <div class="flex items-center gap-3 text-sm mb-3">
      <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full font-medium">
        {{ post.category ? post.category.name : 'ทั่วไป' }}
      </span>
      <span class="text-gray-400">•</span>
      <span class="text-gray-500">
        {{ new Date(post.created_at).toLocaleDateString('th-TH') }}
      </span>
    </div>

    <!-- หัวข้อบทความ -->
    <h1 class="text-3xl font-extrabold text-gray-900 mb-6 leading-tight">
      {{ post.title }}
    </h1>

    <!-- รูปภาพหน้าปกบทความ -->
    <div v-if="post.image" class="mb-8">
      <img 
        :src="'/storage/' + post.image" 
        :alt="post.title" 
        class="w-full max-h-[420px] object-cover rounded-2xl shadow-md border"
      />
    </div>

    <!-- เนื้อหาบทความ แปลง HTML จาก Rich Editor -->
    <div class="prose max-w-none text-gray-800 leading-relaxed space-y-4" v-html="post.content"></div>
  </div>
</template>