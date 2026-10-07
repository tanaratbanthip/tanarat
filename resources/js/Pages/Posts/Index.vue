<script setup>
import { Link, router } from '@inertiajs/vue3' // นำเข้า router เพิ่ม

defineProps({
  //posts: Array
  posts: Object // เปลี่ยนประเภทจาก Array เป็น Object เพราะ paginate() คืนค่ามาเป็น Object
})

// ฟังก์ชันสำหรับสั่งลบข้อมูล
const deletePost = (id) => {
  // กล่องถามยืนยันก่อนลบ ป้องกันการกดโดนโดยไม่ตั้งใจ
  if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบทความนี้?')) {
    router.delete(`/posts/${id}`)
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">บล็อกของฉัน</h1>
      <Link 
        href="/posts/create" 
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium"
      >
        + เขียนบทความใหม่
      </Link>
    </div>

    <div class="space-y-4">
      <div v-if="posts.data.length === 0" class="text-gray-500 text-center py-8">
        ยังไม่มีบทความ ลองกดเขียนบทความใหม่ดูสิ!
      </div>

      <article v-for="post in posts.data" :key="post.id" class="p-6 bg-white rounded-lg shadow">
        <div class="flex justify-between items-start">
        <div>
            <!-- แสดงป้ายหมวดหมู่ -->
            <span v-if="post.category" class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2.5 py-0.5 rounded font-semibold mb-2">
              {{ post.category.name }}
            </span>
            <h2 class="text-xl font-semibold">{{ post.title }}</h2>
          </div>
          
          <!-- กลุ่มปุ่มจัดการ: แก้ไข และ ลบ -->
          <div class="flex items-center space-x-3">
            <Link 
              :href="`/posts/${post.id}/edit`" 
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              แก้ไข
            </Link>

            <button 
              @click="deletePost(post.id)" 
              class="text-red-600 hover:text-red-800 text-sm font-medium"
            >
              ลบ
            </button>
          </div>
        </div>
        <p class="mt-2 text-gray-600 whitespace-pre-line">{{ post.content }}</p>
      </article>
    </div>

    <!-- แถบปุ่ม Pagination สำหรับเปลี่ยนหน้า -->
    <div v-if="posts.links.length > 3" class="mt-8 flex justify-center space-x-1">
      <template v-for="(link, index) in posts.links" :key="index">
        <!-- กรณีปุ่มเป็นหน้าปัจจุบัน หรือเป็นปุ่มที่กดไม่ได้ (link.url เป็น null) -->
        <span 
          v-if="!link.url" 
          v-html="link.label" 
          class="px-4 py-2 text-sm text-gray-400 border rounded-md cursor-not-allowed"
        />

        <!-- ปุ่มที่สามารถคลิกเปลี่ยนหน้าได้ -->
        <Link 
          v-else 
          :href="link.url" 
          v-html="link.label" 
          class="px-4 py-2 text-sm border rounded-md transition duration-150"
          :class="{
            'bg-indigo-600 text-white border-indigo-600 font-bold': link.active,
            'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active
          }"
        />
      </template>
    </div>

  </div>
</template>