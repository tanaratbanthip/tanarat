<script setup>
import { Link, router } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

defineProps({
  posts: Object,
})

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
  <BlogLayout>
    <div class="max-w-4xl mx-auto py-2">
      <!-- Header Section -->
      <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200/80">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">บล็อกของฉัน</h1>
          <p class="text-sm text-slate-500 mt-1">จัดการ แก้ไข และเขียนบทความทั้งหมดของคุณ</p>
        </div>
        <Link
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
          <!-- Thumbnail Image (ถ้ามี) -->
          <div v-if="post.image" class="w-full md:w-48 h-36 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-100">
            <img
              :src="'/storage/' + post.image"
              :alt="post.title"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Post Content -->
          <div class="flex-1 w-full flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-2.5">
                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-xs font-semibold">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                
                <!-- Action Buttons: แก้ไข / ลบ -->
                <div class="flex items-center gap-3 text-xs">
                  <Link
                    :href="route('posts.edit', post.slug || post.id)"
                    class="font-medium text-slate-500 hover:text-indigo-600 transition"
                  >
                    แก้ไข
                  </Link>
                  <span class="text-slate-200">|</span>
                  <button
                    @click="deletePost(post)"
                    class="font-medium text-rose-500 hover:text-rose-700 transition"
                  >
                    ลบ
                  </button>
                </div>
              </div>

              <!-- Post Title -->
              <Link :href="route('posts.show', post.slug || post.id)">
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 hover:text-indigo-600 transition mb-2 leading-snug">
                  {{ post.title }}
                </h2>
              </Link>

              <!-- Preview Excerpt -->
              <p class="text-slate-600 text-sm line-clamp-2 leading-relaxed">
                {{ stripTags(post.content) }}
              </p>
            </div>

            <!-- Footer Meta -->
            <div class="flex items-center justify-between pt-4 mt-3 border-t border-slate-100 text-xs text-slate-400">
              <span>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
              <Link
                :href="route('posts.show', post.slug || post.id)"
                class="font-semibold text-indigo-600 hover:text-indigo-800"
              >
                อ่านต่อ →
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination Links (ถ้ามี) -->
        <div v-if="posts.links && posts.links.length > 3" class="flex justify-center gap-1.5 pt-6">
          <Link
            v-for="(link, index) in posts.links"
            :key="index"
            :href="link.url || '#'"
            v-html="link.label"
            :class="[
              link.active ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-100',
              !link.url ? 'opacity-40 cursor-not-allowed' : ''
            ]"
            class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs transition"
          />
        </div>
      </div>
    </div>
  </BlogLayout>
</template>