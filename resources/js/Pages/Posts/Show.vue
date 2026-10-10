<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

const props = defineProps({
  post: Object,
  relatedPosts: Array,
  isBookmarked: Boolean,
  isLiked: Boolean,
  likesCount: Number,
})

const page = usePage()
const currentUser = computed(() => page.props.auth?.user)
const isAuthor = computed(() => currentUser.value && currentUser.value.id === props.post.user_id)

// 1. แถบความคืบหน้าการอ่าน (Reading Progress Bar)
const readingProgress = ref(0)
const updateProgress = () => {
  const scrollTop = window.scrollY || document.documentElement.scrollTop
  const docHeight = document.documentElement.scrollHeight - window.innerHeight
  if (docHeight > 0) {
    readingProgress.value = Math.min(100, Math.max(0, (scrollTop / docHeight) * 100))
  }
}

// 2. ระบบสารบัญอัตโนมัติ (Table of Contents - TOC)
const headings = ref([])
const contentRef = ref(null)

const generateToc = () => {
  if (!contentRef.value) return
  const elements = contentRef.value.querySelectorAll('h2, h3')
  const list = []
  elements.forEach((el, index) => {
    const id = `section-${index + 1}`
    el.id = id
    list.push({
      id,
      text: el.innerText,
      level: el.tagName.toLowerCase(),
    })
  })
  headings.value = list
}

const scrollToHeading = (id) => {
  const element = document.getElementById(id)
  if (element) {
    const navHeight = 80
    const elementPosition = element.getBoundingClientRect().top + window.scrollY
    window.scrollTo({
      top: elementPosition - navHeight,
      behavior: 'smooth',
    })
  }
}

onMounted(() => {
  window.addEventListener('scroll', updateProgress, { passive: true })
  nextTick(() => {
    generateToc()
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateProgress)
})

// 3. ระบบกดถูกใจ (Like Reaction)
const isLiking = ref(false)
const toggleLike = () => {
  isLiking.value = true
  router.post(
    route('posts.like', props.post.slug || props.post.id),
    {},
    {
      preserveScroll: true,
      onFinish: () => (isLiking.value = false),
    }
  )
}

// ระบบ Bookmark
const isTogglingBookmark = ref(false)
const toggleBookmark = () => {
  if (!currentUser.value) return router.get(route('login'))
  isTogglingBookmark.value = true
  router.post(
    route('posts.bookmark', props.post.slug || props.post.id),
    {},
    {
      preserveScroll: true,
      onFinish: () => (isTogglingBookmark.value = false),
    }
  )
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

// Comments & Replies
const replyingTo = ref(null)
const form = useForm({
  author_name: '',
  content: '',
  parent_id: null,
})

const startReply = (comment) => {
  replyingTo.value = comment
  form.parent_id = comment.id
  document.getElementById('comment-box')?.scrollIntoView({ behavior: 'smooth' })
}

const cancelReply = () => {
  replyingTo.value = null
  form.parent_id = null
}

const submitComment = () => {
  form.post(route('comments.store', props.post.slug || props.post.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('content')
      cancelReply()
    },
  })
}
</script>

<template>
  <Head>
    <title>{{ post.title }} - MyNotes</title>
  </Head>

  <BlogLayout>
    <!-- Reading Progress Bar ด้านบนสุด -->
    <div
      class="fixed top-0 left-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 z-50 transition-all duration-75"
      :style="{ width: `${readingProgress}%` }"
    ></div>

    <div class="max-w-4xl mx-auto py-2">
      <!-- Top Action Navigation -->
      <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200/80 dark:border-slate-800 text-sm">
        <Link
          :href="route('home')"
          class="inline-flex items-center gap-2 font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          ย้อนกลับ
        </Link>

        <div class="flex items-center gap-2.5">
          <!-- Like Button -->
          <button
            @click="toggleLike"
            :disabled="isLiking"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition active:scale-95"
            :class="isLiked
              ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400'
              : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
            title="ถูกใจบทความนี้"
          >
            <svg class="w-4 h-4" :fill="isLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span>{{ likesCount }}</span>
          </button>

          <!-- Bookmark Button -->
          <button
            @click="toggleBookmark"
            :disabled="isTogglingBookmark"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
            :class="isBookmarked
              ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400'
              : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
          >
            <svg class="w-4 h-4" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <span class="hidden sm:inline">{{ isBookmarked ? 'บันทึกแล้ว' : 'บันทึกไว้อ่าน' }}</span>
          </button>

          <!-- Edit Button -->
          <Link
            v-if="isAuthor"
            :href="route('posts.edit', post.slug || post.id)"
            class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-medium"
          >
            แก้ไข
          </Link>
        </div>
      </div>

      <!-- Article Header -->
      <header class="mb-8">
        <div class="flex flex-wrap items-center gap-2 mb-4 text-xs font-medium text-slate-400 dark:text-slate-500">
          <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-full font-semibold">
            {{ post.category ? post.category.name : 'ทั่วไป' }}
          </span>
          <span>•</span>
          <span>{{ new Date(post.created_at).toLocaleDateString('th-TH') }}</span>
          <span>•</span>
          <span class="text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50/60 dark:bg-indigo-950/40 px-2 py-0.5 rounded-md">
            อ่านประมาณ {{ post.reading_time }} นาที
          </span>
          <span>•</span>
          <span>{{ post.views || 0 }} วิว</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-slate-900 dark:text-white leading-tight tracking-tight mb-6">
          {{ post.title }}
        </h1>

        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm">
          <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-sm text-indigo-600 dark:text-indigo-400 flex-shrink-0">
            <img
  v-if="post.user?.avatar"
  :src="post.user.avatar.startsWith('http') ? post.user.avatar : '/storage/' + post.user.avatar"
  class="w-full h-full object-cover"
/>
            <span v-else>{{ post.user ? post.user.name.charAt(0).toUpperCase() : 'A' }}</span>
          </div>
          <div>
            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ post.user ? post.user.name : 'ผู้เขียนนิรนาม' }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">ผู้เขียนบทความ</p>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
<div v-if="rel.image" class="h-28 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-800">
                <img
                  :src="rel.image.startsWith('http') ? rel.image : '/storage/' + rel.image"
                  :alt="rel.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>
      </div>

      <!-- สารบัญอัตโนมัติ (Table of Contents - TOC) -->
      <div v-if="headings.length > 1" class="mb-10 p-5 sm:p-6 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
        <div class="flex items-center gap-2 text-sm font-bold text-slate-800 dark:text-slate-200">
          <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
          </svg>
          สารบัญเนื้อหา (Table of Contents)
        </div>
        <ul class="space-y-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
          <li
            v-for="(heading, i) in headings"
            :key="i"
            :class="heading.level === 'h3' ? 'pl-4 text-slate-500 dark:text-slate-400' : 'font-medium'"
          >
            <button
              @click="scrollToHeading(heading.id)"
              class="hover:text-indigo-600 dark:hover:text-indigo-400 transition text-left cursor-pointer"
            >
              • {{ heading.text }}
            </button>
          </li>
        </ul>
      </div>

      <!-- เนื้อหาบทความ (Article Content) -->
      <section
        ref="contentRef"
        class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-loose text-base sm:text-lg space-y-6"
        v-html="post.content"
      ></section>

      <!-- Bottom Reaction & Social Share Bar -->
      <div class="mt-12 py-6 border-y border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <button
            @click="toggleLike"
            class="flex items-center gap-2 px-4 py-2 rounded-2xl border transition active:scale-95 shadow-sm text-xs font-bold"
            :class="isLiked
              ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 shadow-rose-100 dark:shadow-none'
              : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
          >
            <svg class="w-5 h-5" :fill="isLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span>{{ isLiked ? 'ถูกใจแล้ว' : 'ส่งหัวใจ' }} ({{ likesCount }})</span>
          </button>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 mr-1">แชร์:</span>
          <button @click="copyUrl" class="px-3 py-2 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            {{ copied ? 'คัดลอกแล้ว!' : 'คัดลอกลิงก์' }}
          </button>
          <button @click="shareFacebook" class="px-3 py-2 rounded-xl text-xs font-semibold bg-[#1877F2]/10 text-[#1877F2]">Facebook</button>
          <button @click="shareTwitter" class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-900/10 dark:bg-white/10 text-slate-900 dark:text-white">X</button>
          <button @click="shareLine" class="px-3 py-2 rounded-xl text-xs font-semibold bg-[#06C755]/10 text-[#06C755]">LINE</button>
        </div>
      </div>

      <!-- Related Posts -->
      <section v-if="relatedPosts && relatedPosts.length > 0" class="mt-14 space-y-5">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
          บทความที่เกี่ยวข้องในหมวดนี้
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <Link
            v-for="rel in relatedPosts"
            :key="rel.id"
            :href="route('posts.show', rel.slug || rel.id)"
            class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 hover:border-indigo-300 dark:hover:border-indigo-600 transition flex flex-col justify-between"
          >
            <div>
              <div v-if="rel.image" class="h-28 rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-800">
                <img :src="'/storage/' + rel.image" :alt="rel.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              </div>
              <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md">
                {{ rel.category ? rel.category.name : 'ทั่วไป' }}
              </span>
              <h4 class="font-bold text-slate-900 dark:text-white text-sm mt-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition line-clamp-2 leading-snug">
                {{ rel.title }}
              </h4>
            </div>
            <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
              {{ new Date(rel.created_at).toLocaleDateString('th-TH') }}
            </span>
          </Link>
        </div>
      </section>

      <!-- Comments Section -->
      <section id="comment-box" class="mt-14 space-y-8 border-t border-slate-200/80 dark:border-slate-800 pt-10">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
          ความคิดเห็น
          <span class="text-sm font-normal text-slate-400 dark:text-slate-500">({{ post.comments ? post.comments.length : 0 }})</span>
        </h3>

        <!-- Form -->
        <form @submit.prevent="submitComment" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-sm space-y-4">
          <div v-if="replyingTo" class="flex items-center justify-between px-3.5 py-2 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/40 rounded-xl text-xs text-indigo-700 dark:text-indigo-300">
            <span>กำลังตอบกลับความคิดเห็นของคุณ: <strong>{{ replyingTo.user ? replyingTo.user.name : replyingTo.author_name }}</strong></span>
            <button type="button" @click="cancelReply" class="text-indigo-500 hover:text-indigo-700 font-bold ml-2">✕ ยกเลิก</button>
          </div>

          <div v-if="!currentUser">
            <input
              v-model="form.author_name"
              type="text"
              placeholder="ระบุชื่อของคุณ..."
              class="w-full px-3.5 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white rounded-xl outline-none focus:border-indigo-500"
              required
            />
          </div>

          <textarea
            v-model="form.content"
            rows="3"
            :placeholder="replyingTo ? 'เขียนข้อความตอบกลับ...' : 'ร่วมแบ่งปันความคิดเห็น...'"
            class="w-full px-3.5 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white rounded-xl outline-none focus:border-indigo-500"
            required
          ></textarea>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm transition disabled:opacity-50"
            >
              {{ form.processing ? 'กำลังส่ง...' : (replyingTo ? 'ส่งคำตอบกลับ' : 'แสดงความคิดเห็น') }}
            </button>
          </div>
        </form>

        <!-- Comments List -->
        <div class="space-y-4">
          <div
            v-for="comment in post.comments"
            :key="comment.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800/80 p-5 shadow-sm space-y-3"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-200 flex-shrink-0">
                  <img
                    v-if="comment.user?.avatar"
                    :src="comment.user.avatar.startsWith('http') ? comment.user.avatar : '/storage/' + comment.user.avatar"
                    class="w-full h-full object-cover"
                  />
                  <span v-else>{{ (comment.user ? comment.user.name : comment.author_name).charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                  <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ comment.user ? comment.user.name : comment.author_name }}</h4>
                  <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ new Date(comment.created_at).toLocaleDateString('th-TH') }}</p>
                </div>
              </div>
              <button
                @click="startReply(comment)"
                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline transition"
              >
                ตอบกลับ
              </button>
            </div>

            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line pl-11">
              {{ comment.content }}
            </p>

            <!-- Nested Replies -->
            <div v-if="comment.replies && comment.replies.length > 0" class="pl-11 pt-2 space-y-3">
              <div
                v-for="reply in comment.replies"
                :key="reply.id"
                class="bg-slate-50 dark:bg-slate-950/60 rounded-xl p-3.5 border border-slate-100 dark:border-slate-800/60 space-y-1.5"
              >
                <div class="flex items-center gap-2">
<div class="w-6 h-6 rounded-full overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-700 dark:text-slate-200 flex-shrink-0">
                    <img
                      v-if="reply.user?.avatar"
                      :src="reply.user.avatar.startsWith('http') ? reply.user.avatar : '/storage/' + reply.user.avatar"
                      class="w-full h-full object-cover"
                    />
                    <span v-else>{{ (reply.user ? reply.user.name : reply.author_name).charAt(0).toUpperCase() }}</span>
                  </div>
                  <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ reply.user ? reply.user.name : reply.author_name }}</span>
                  <span class="text-[10px] text-slate-400 dark:text-slate-500">• {{ new Date(reply.created_at).toLocaleDateString('th-TH') }}</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 pl-8 leading-relaxed whitespace-pre-line">
                  {{ reply.content }}
                </p>
              </div>
            </div>
          </div>

          <p v-if="!post.comments || post.comments.length === 0" class="text-center text-xs text-slate-400 dark:text-slate-500 py-6">
            ยังไม่มีความคิดเห็น เป็นคนแรกที่ร่วมแสดงความคิดเห็นในบทความนี้!
          </p>
        </div>
      </section>
    </div>
  </BlogLayout>
</template>

<style>
.prose p { margin-bottom: 1.5rem; }
.prose h2, .prose h3 { font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
</style>
