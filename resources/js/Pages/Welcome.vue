<script setup>
import { Link } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

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
  <BlogLayout>
    <!-- Hero Profile Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-8 sm:p-10 mb-10 text-white shadow-xl">
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

    <!-- Category Pills -->
    <div v-if="categories && categories.length" class="mb-8">
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">หมวดหมู่:</span>
        <button
          v-for="cat in categories"
          :key="cat.id"
          class="px-3.5 py-1.5 rounded-full text-xs font-medium bg-white border border-slate-200 text-slate-600 hover:border-indigo-400 hover:text-indigo-600 transition whitespace-nowrap shadow-sm"
        >
          #{{ cat.name }}
        </button>
      </div>
    </div>

    <!-- Post Feed -->
    <div class="space-y-6">
      <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
        บทความล่าสุด
      </h2>

      <div class="grid gap-6">
        <article
          v-for="post in posts.data"
          :key="post.id"
          class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-50/50 transition-all duration-200 flex flex-col md:flex-row"
        >
          <!-- Thumbnail -->
          <div v-if="post.image" class="md:w-64 h-48 md:h-auto overflow-hidden bg-slate-100 flex-shrink-0">
            <img
              :src="'/storage/' + post.image"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
          </div>

          <!-- Content Details -->
          <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <div class="flex items-center gap-2 text-xs font-semibold mb-2">
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-md">
                  {{ post.category ? post.category.name : 'ทั่วไป' }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-400">
                  {{ new Date(post.created_at).toLocaleDateString('th-TH') }}
                </span>
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
              <Link :href="route('posts.edit', post.slug || post.id)" class="text-slate-400 hover:text-slate-600">
                แก้ไข
              </Link>
            </div>
          </div>
        </article>
      </div>
    </div>
  </BlogLayout>
</template>