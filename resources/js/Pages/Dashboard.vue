<script setup>
import { Link } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

defineProps({
  stats: Object,
  topPosts: Array,
  recentComments: Array,
})
</script>

<template>
  <BlogLayout>
    <div class="max-w-5xl mx-auto py-2 space-y-8">
      
      <!-- Top Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">ภาพรวมแดชบอร์ด</h1>
          <p class="text-sm text-slate-500 mt-1">สถิติการใช้งานและการมีส่วนร่วมของผู้อ่านในเว็บบล็อก</p>
        </div>
        <div class="flex items-center gap-2">
          <Link
            :href="route('categories.index')"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition"
          >
            จัดการหมวดหมู่
          </Link>
          <Link
            :href="route('posts.create')"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition"
          >
            + เขียนบทความใหม่
          </Link>
        </div>
      </div>

      <!-- Stat Metric Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Posts -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-400">บทความทั้งหมด</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ stats.total_posts }}</p>
          </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-400">ยอดเข้าอ่านรวม</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ stats.total_views }}</p>
          </div>
        </div>

        <!-- Total Comments -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-400">ความคิดเห็น</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ stats.total_comments }}</p>
          </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-400">หมวดหมู่ทั้งหมด</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ stats.total_categories }}</p>
          </div>
        </div>
      </div>

      <!-- Main Analytics Grids -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Top 5 Most Viewed Posts (2 Columns wide) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-violet-600"></span>
              5 อันดับบทความยอดวิวสูงสุด
            </h2>
            <Link :href="route('posts.index')" class="text-xs font-medium text-indigo-600 hover:underline">
              ดูทั้งหมด →
            </Link>
          </div>

          <div v-if="topPosts.length === 0" class="text-center py-8 text-xs text-slate-400">
            ยังไม่มีข้อมูลบทความ
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="(post, index) in topPosts"
              :key="post.id"
              class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100"
            >
              <div class="flex items-center gap-3.5 min-w-0 pr-4">
                <span
                  class="w-6 h-6 rounded-lg text-xs font-bold flex items-center justify-center flex-shrink-0"
                  :class="[
                    index === 0 ? 'bg-amber-100 text-amber-700' :
                    index === 1 ? 'bg-slate-200 text-slate-700' :
                    index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500'
                  ]"
                >
                  {{ index + 1 }}
                </span>
                <div class="min-w-0">
                  <Link :href="route('posts.show', post.slug || post.id)" class="text-sm font-semibold text-slate-800 hover:text-indigo-600 transition truncate block">
                    {{ post.title }}
                  </Link>
                  <span class="text-[11px] text-slate-400">
                    {{ post.category ? post.category.name : 'ทั่วไป' }}
                  </span>
                </div>
              </div>

              <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-violet-50 text-violet-700 font-bold text-xs whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>{{ post.views }} ครั้ง</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Comments (1 Column wide) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
          <div class="pb-3 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
              ความคิดเห็นล่าสุด
            </h2>
          </div>

          <div v-if="recentComments.length === 0" class="text-center py-8 text-xs text-slate-400">
            ยังไม่มีความคิดเห็น
          </div>

          <div v-else class="space-y-3.5">
            <div
              v-for="comment in recentComments"
              :key="comment.id"
              class="p-3 bg-slate-50 rounded-xl space-y-1.5 border border-slate-100"
            >
              <div class="flex items-center justify-between text-[11px] text-slate-400">
                <span class="font-bold text-slate-700">
                  {{ comment.user ? comment.user.name : comment.author_name }}
                </span>
                <span>{{ new Date(comment.created_at).toLocaleDateString('th-TH') }}</span>
              </div>
              <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                "{{ comment.content }}"
              </p>
              <div class="text-[11px] text-indigo-600 font-medium truncate pt-1">
                บน: {{ comment.post ? comment.post.title : 'บทความ' }}
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </BlogLayout>
</template>