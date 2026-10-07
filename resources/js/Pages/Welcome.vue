<script setup>
import { Link, Head } from '@inertiajs/vue3'

defineProps({
  posts: Object,
  categories: Array
})
</script>

<template>
  <Head title="หน้าแรก - บล็อกส่วนตัว" />

  <div class="min-h-screen bg-[#FDFDFC] text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
    <!-- แถบเมนูด้านบน (Navigation) -->
    <header class="border-b border-slate-100 bg-white/80 backdrop-blur sticky top-0 z-10">
      <div class="max-w-3xl mx-auto px-6 h-16 flex items-center justify-between">
        <Link href="/" class="font-bold text-lg tracking-tight hover:text-indigo-600 transition">
          MyNotes.
        </Link>
        <nav class="flex items-center space-x-6 text-sm font-medium text-slate-600">
          <Link href="/" class="text-indigo-600">หน้าแรก</Link>
          <Link href="/posts/create" class="px-3 py-1.5 bg-slate-900 text-white rounded-full text-xs hover:bg-slate-700 transition">
            + เขียนบทความ
          </Link>
        </nav>
      </div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-12">
      <!-- ส่วนแนะนำตัวสั้นๆ (Bio / Hero Section) -->
      <section class="mb-14 pb-10 border-b border-slate-100">
        <div class="flex items-center space-x-4 mb-4">
          <!-- รูปโปรไฟล์ Avatar จำลอง -->
          <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold text-xl shadow-inner">
            D
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-900">บันทึกของฉัน</h1>
            <p class="text-sm text-slate-500">แบ่งปันสิ่งที่ได้เรียนรู้ เรื่องราวเทคโนโลยี และแนวคิดการทำงาน</p>
          </div>
        </div>

        <!-- รายการแถบหมวดหมู่สำหรับดูภาพรวม -->
        <div class="flex flex-wrap gap-2 pt-2">
          <span class="text-xs font-semibold text-slate-400 self-center mr-1">หมวดหมู่:</span>
          <span 
            v-for="category in categories" 
            :key="category.id"
            class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium"
          >
            #{{ category.name }}
          </span>
        </div>
      </section>

      <!-- รายการบทความ (Article Feed) -->
      <section class="space-y-10">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">บทความล่าสุด</h2>

        <div v-if="posts.data.length === 0" class="text-center py-12 text-slate-400 text-sm">
          ยังไม่มีบทความในขณะนี้
        </div>

<article 
          v-for="post in posts.data" 
          :key="post.id" 
          class="group border-b border-slate-100 pb-8 last:border-0"
        >
          <!-- วันที่และป้ายหมวดหมู่ -->
          <div class="flex items-center space-x-2 text-xs text-slate-400 mb-2">
            <span v-if="post.category" class="font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
              {{ post.category.name }}
            </span>
            <span>•</span>
            <time>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</time>
          </div>

          <!-- ใส่แท็ก Link ครอบหัวข้อบทความ เพื่อให้คลิกได้ -->
          <h3 class="text-lg font-semibold text-slate-900 leading-snug mb-2">
            <Link :href="`/posts/${post.id}`" class="group-hover:text-indigo-600 transition">
              {{ post.title }}
            </Link>
          </h3>

          <!-- เนื้อหาโดยย่อ -->
          <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">
            {{ post.content }}
          </p>

          <!-- ลิงก์เครื่องมือจัดการบทความเล็กๆ -->
          <div class="mt-4 flex items-center space-x-3 text-xs text-slate-400">
            <Link :href="`/posts/${post.id}`" class="text-indigo-600 hover:underline">อ่านต่อ →</Link>
            <span>•</span>
            <Link :href="`/posts/${post.id}/edit`" class="hover:text-slate-700">แก้ไข</Link>
          </div>
        </article>
      </section>

      <!-- แถบเปลี่ยนหน้า (Pagination) -->
      <div v-if="posts.links.length > 3" class="mt-12 flex justify-center space-x-1">
        <template v-for="(link, index) in posts.links" :key="index">
          <span 
            v-if="!link.url" 
            v-html="link.label" 
            class="px-3 py-1.5 text-xs text-slate-300 border border-slate-100 rounded-md cursor-not-allowed"
          />
          <Link 
            v-else 
            :href="link.url" 
            v-html="link.label" 
            class="px-3 py-1.5 text-xs border rounded-md transition"
            :class="{
              'bg-slate-900 text-white border-slate-900 font-semibold': link.active,
              'bg-white text-slate-600 hover:bg-slate-50 border-slate-200': !link.active
            }"
          />
        </template>
      </div>
    </main>

    <!-- ท้ายหน้า (Footer) -->
    <footer class="border-t border-slate-100 py-8 text-center text-xs text-slate-400">
      <p>© {{ new Date().getFullYear() }} MyNotes. พัฒนาด้วย Laravel & Vue.js</p>
    </footer>
  </div>
</template>