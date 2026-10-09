<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3' // นำเข้า Head
import BlogLayout from '@/Layouts/BlogLayout.vue'

const props = defineProps({
  post: Object,
  relatedPosts: Array,
  isBookmarked: Boolean,
})

// สรุปข้อความสั้น 150 ตัวอักษรสำหรับ Meta Description
const plainExcerpt = computed(() => {
  if (!props.post.content) return ''
  const div = document.createElement('div')
  div.innerHTML = props.post.content
  const text = div.textContent || div.innerText || ''
  return text.length > 150 ? text.substring(0, 150) + '...' : text
})

// สร้าง URL เต็มของรูปภาพปก
const fullImageUrl = computed(() => {
  return props.post.image ? `${window.location.origin}/storage/${props.post.image}` : ''
})

const page = usePage()
const currentUser = computed(() => page.props.auth?.user)
const isAuthor = computed(() => currentUser.value && currentUser.value.id === props.post.user_id)

// ระบบ Bookmark
const isTogglingBookmark = ref(false)
const toggleBookmark = () => {
  if (!currentUser.value) {
    router.get(route('login'))
    return
  }

  isTogglingBookmark.value = true
  router.post(route('posts.bookmark', props.post.slug || props.post.id), {}, {
    preserveScroll: true,
    onFinish: () => (isTogglingBookmark.value = false),
  })
}

// ระบบ Share
const copied = ref(false)
const copyUrl = () => {
  navigator.clipboard.writeText(window.location.href)
  copied.value = true
  setTimeout(() => (copied.value = false), 2500)
}

const shareUrl = computed(() => encodeURIComponent(window.location.href))
const shareTitle = computed(() => encodeURIComponent(props.post.title))

const shareFacebook = () => window.open(`https://www.facebook.com/sharer/sharer.php?u=${shareUrl.value}`, '_blank')
const shareTwitter = () => window.open(`https://twitter.com/intent/tweet?url=${shareUrl.value}&text=${shareTitle.value}`, '_blank')
const shareLine = () => window.open(`https://social-plugins.line.me/lineit/share?url=${shareUrl.value}`, '_blank')

// ฟอร์มส่งความคิดเห็น
const form = useForm({
  author_name: '',
  content: '',
})

const submitComment = () => {
  form.post(route('comments.store', props.post.slug || props.post.id), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <!-- กำหนด Dynamic SEO และ Social Meta Tags -->
  <Head>
    <title>{{ post.title }} - MyNotes</title>
    <meta name="description" :content="plainExcerpt" />
    <meta property="og:title" :content="post.title" />
    <meta property="og:description" :content="plainExcerpt" />
    <meta v-if="fullImageUrl" property="og:image" :content="fullImageUrl" />
    <meta name="twitter:title" :content="post.title" />
    <meta name="twitter:description" :content="plainExcerpt" />
    <meta v-if="fullImageUrl" name="twitter:image" :content="fullImageUrl" />
  </Head>
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
          <!-- ปุ่ม Bookmark -->
          <button
            @click="toggleBookmark"
            :disabled="isTogglingBookmark"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
            :class="isBookmarked 
              ? 'bg-amber-50 border-amber-200 text-amber-600 hover:bg-amber-100' 
              : 'border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300'"
          >
            <svg
              class="w-4 h-4"
              :fill="isBookmarked ? 'currentColor' : 'none'"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <span>{{ isBookmarked ? 'บันทึกแล้ว' : 'บันทึกไว้อ่าน' }}</span>
          </button>

          <Link
            v-if="isAuthor"
            :href="route('posts.edit', post.slug || post.id)"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 font-medium text-xs transition"
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
        <div class="flex flex-wrap items-center gap-2 mb-4 text-xs font-medium text-slate-400">
          <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full font-semibold">
            {{ post.category ? post.category.name : 'ทั่วไป' }}
          </span>
          <span>•</span>
          <span>{{ new Date(post.created_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
          <span>•</span>
          <!-- เวลาอ่านโดยประมาณ -->
          <span class="flex items-center gap-1 text-indigo-600 font-semibold bg-indigo-50/60 px-2 py-0.5 rounded-md">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            อ่านประมาณ {{ post.reading_time }} นาที
          </span>
          <span>•</span>
          <!-- ยอดวิว -->
          <span class="flex items-center gap-1 text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ post.views || 0 }} ครั้ง
          </span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-slate-900 leading-snug sm:leading-tight tracking-tight mb-6">
          {{ post.title }}
        </h1>

        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm">
          <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
            {{ post.user ? post.user.name.charAt(0).toUpperCase() : 'A' }}
          </div>
          <div>
            <p class="text-sm font-bold text-slate-800 leading-tight">
              {{ post.user ? post.user.name : 'ผู้เขียนนิรนาม' }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">ผู้เขียนบทความ</p>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <div v-if="post.image" class="mb-10 overflow-hidden rounded-3xl border border-slate-200/80 shadow-md">
        <img :src="'/storage/' + post.image" :alt="post.title" class="w-full max-h-[460px] object-cover" />
      </div>

      <!-- Article Body -->
      <section
        class="prose prose-slate max-w-none text-slate-700 leading-loose text-base sm:text-lg space-y-6 break-words"
        v-html="post.content"
      ></section>

      <!-- Social Share -->
      <div class="mt-12 py-6 border-y border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="text-sm font-bold text-slate-700">แบ่งปันบทความนี้:</span>
        <div class="flex items-center gap-2">
          <button
            @click="copyUrl"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border transition"
            :class="copied ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            {{ copied ? 'คัดลอกแล้ว!' : 'คัดลอกลิงก์' }}
          </button>
          <button @click="shareFacebook" class="px-3 py-2 rounded-xl text-xs font-semibold bg-[#1877F2]/10 text-[#1877F2] hover:bg-[#1877F2]/20 transition">Facebook</button>
          <button @click="shareTwitter" class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-900/10 text-slate-900 hover:bg-slate-900/20 transition">X</button>
          <button @click="shareLine" class="px-3 py-2 rounded-xl text-xs font-semibold bg-[#06C755]/10 text-[#06C755] hover:bg-[#06C755]/20 transition">LINE</button>
        </div>
      </div>

      <!-- Related Posts (บทความที่เกี่ยวข้องในหมวดเดียวกัน) -->
      <section v-if="relatedPosts && relatedPosts.length > 0" class="mt-14 space-y-5">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
          บทความที่เกี่ยวข้องในหมวดนี้
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <Link
            v-for="rel in relatedPosts"
            :key="rel.id"
            :href="route('posts.show', rel.slug || rel.id)"
            class="group bg-white rounded-2xl border border-slate-200 p-4 hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between"
          >
            <div>
              <div v-if="rel.image" class="h-28 rounded-xl overflow-hidden mb-3 bg-slate-100">
                <img :src="'/storage/' + rel.image" :alt="rel.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              </div>
              <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                {{ rel.category ? rel.category.name : 'ทั่วไป' }}
              </span>
              <h4 class="font-bold text-slate-900 text-sm mt-2 group-hover:text-indigo-600 transition line-clamp-2 leading-snug">
                {{ rel.title }}
              </h4>
            </div>
            <span class="text-[11px] text-slate-400 mt-3 pt-2 border-t border-slate-100">
              {{ new Date(rel.created_at).toLocaleDateString('th-TH') }}
            </span>
          </Link>
        </div>
      </section>

      <!-- Comments Section -->
      <section class="mt-14 space-y-8">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
          ความคิดเห็น
          <span class="text-sm font-normal text-slate-400">({{ post.comments ? post.comments.length : 0 }})</span>
        </h3>

        <form @submit.prevent="submitComment" class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm space-y-4">
          <div v-if="!currentUser">
            <label class="block text-xs font-semibold text-slate-600 mb-1">ชื่อของคุณ</label>
            <input
              v-model="form.author_name"
              type="text"
              placeholder="ระบุชื่อเพื่อแสดงในความคิดเห็น..."
              class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">ข้อความความคิดเห็น</label>
            <textarea
              v-model="form.content"
              rows="3"
              placeholder="ร่วมแบ่งปันมุมมองของคุณต่อบทความนี้..."
              class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition"
              required
            ></textarea>
            <div v-if="form.errors.content" class="text-rose-500 text-xs mt-1">{{ form.errors.content }}</div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 transition disabled:opacity-50"
            >
              {{ form.processing ? 'กำลังส่ง...' : 'แสดงความคิดเห็น' }}
            </button>
          </div>
        </form>

        <div class="space-y-4">
          <div
            v-for="comment in post.comments"
            :key="comment.id"
            class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm space-y-2"
          >
            <div class="flex items-center justify-between text-xs text-slate-400">
              <span class="font-bold text-slate-800 text-sm">{{ comment.user ? comment.user.name : comment.author_name }}</span>
              <span>{{ new Date(comment.created_at).toLocaleDateString('th-TH') }}</span>
            </div>
            <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ comment.content }}</p>
          </div>

          <p v-if="!post.comments || post.comments.length === 0" class="text-center text-xs text-slate-400 py-6">
            ยังไม่มีความคิดเห็น เป็นคนแรกที่แสดงความคิดเห็นในบทความนี้!
          </p>
        </div>
      </section>

    </article>
  </BlogLayout>
</template>

<style>
.prose p { margin-bottom: 1.5rem; }
.prose h2, .prose h3 { color: #0f172a; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
</style>