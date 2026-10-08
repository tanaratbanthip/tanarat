<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  posts: Object,
  categories: Array,
})

const stripTags = (html) => {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <header class="bg-white border-b sticky top-0 z-10">
      <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-bold text-gray-900 tracking-tight">MyNotes.</Link>
        <div class="flex items-center gap-4">
          <Link :href="route('posts.index')" class="text-sm text-gray-600 hover:text-gray-900">จัดการบล็อก</Link>
          <Link
            :href="route('posts.create')"
            class="px-3.5 py-1.5 bg-gray-900 hover:bg-black text-white rounded-lg text-sm font-medium transition"
          >
            + เขียนบทความ
          </Link>
        </div>
      </div>
    </header>

    <main class="max-w-4xl mx-auto py-10 px-4 space-y-10">
      <!-- Profile Header -->
      <div class="flex items-center gap-4 bg-white p-6 rounded-2xl border shadow-sm">
        <div class="w-14 h-14 bg-indigo-600 text-white flex items-center justify-center text-xl font-bold rounded-full">
          D
        </div>
        <div>
          <h1 class="text-lg font-bold text-gray-900">บันทึกของฉัน</h1>
          <p class="text-sm text-gray-500">แบ่งปันสิ่งที่ได้เรียนรู้ เรื่องราวเทคโนโลยี และแนวคิดการทำงาน</p>
        </div>
      </div>

      <!-- Categories Filter Tags -->
      <div v-if="categories && categories.length" class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-1">หมวดหมู่:</span>
        <span
          v-for="cat in categories"
          :key="cat.id"
          class="px-3 py-1 bg-white border border-gray-200 text-gray-600 rounded-full text-xs hover:border-gray-400 cursor-pointer transition"
        >
          #{{ cat.name }}
        </span>
      </div>

      <!-- Posts List -->
      <div class="space-y-6">
        <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider">บทความล่าสุด</h2>

        <article
          v-for="post in posts.data"
          :key="post.id"
          class="bg-white border rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6 hover:border-gray-300 transition"
        >
          <img
            v-if="post.image"
            :src="'/storage/' + post.image"
            :alt="post.title"
            class="w-full md:w-52 h-36 object-cover rounded-xl border flex-shrink-0"
          />

          <div class="flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md font-medium">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                <span>•</span>
                <span>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
              </div>

              <Link :href="route('posts.show', post.slug || post.id)">
                <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600 transition mb-2">
                  {{ post.title }}
                </h3>
              </Link>

              <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">
                {{ stripTags(post.content) }}
              </p>
            </div>

            <div class="flex items-center gap-4 mt-4 pt-3 border-t text-xs">
              <Link :href="route('posts.show', post.slug || post.id)" class="text-blue-600 font-medium hover:underline">
                อ่านต่อ →
              </Link>
              <Link :href="route('posts.edit', post.slug || post.id)" class="text-gray-400 hover:text-gray-600">
                แก้ไข
              </Link>
            </div>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>