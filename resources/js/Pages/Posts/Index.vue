<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  posts: Object,
})

// ฟังก์ชันตัดแท็ก HTML ออกเพื่อให้แสดงเป็นข้อความสรุปสั้นๆ อย่างสะอาดตา
const stripTags = (html) => {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}

const deletePost = (post) => {
  if (confirm(`คุณต้องการลบบทความ "${post.title}" ใช่หรือไม่?`)) {
    router.delete(route('posts.destroy', post.slug || post.id))
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-extrabold text-gray-900">บล็อกของฉัน</h1>
      <Link
        :href="route('posts.create')"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition"
      >
        + เขียนบทความใหม่
      </Link>
    </div>

    <div class="space-y-6">
      <div
        v-for="post in posts.data"
        :key="post.id"
        class="bg-white border rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6 items-start"
      >
        <!-- ภาพ Thumbnail (ถ้ามี) -->
        <img
          v-if="post.image"
          :src="'/storage/' + post.image"
          :alt="post.title"
          class="w-full md:w-44 h-32 object-cover rounded-xl border flex-shrink-0"
        />

        <div class="flex-1 w-full">
          <div class="flex justify-between items-center mb-2">
            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
              {{ post.category ? post.category.name : 'ทั่วไป' }}
            </span>
            <div class="space-x-3 text-xs">
              <Link :href="route('posts.edit', post.slug || post.id)" class="text-blue-600 hover:underline">แก้ไข</Link>
              <button @click="deletePost(post)" class="text-red-500 hover:underline">ลบ</button>
            </div>
          </div>

          <Link :href="route('posts.show', post.slug || post.id)">
            <h2 class="text-xl font-bold text-gray-900 hover:text-blue-600 transition mb-2">
              {{ post.title }}
            </h2>
          </Link>

          <!-- ตัดแท็ก HTML ออกและจำกัด 2 บรรทัด -->
          <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">
            {{ stripTags(post.content) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>