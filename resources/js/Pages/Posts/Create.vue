<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'
import RichEditor from '@/Components/RichEditor.vue'

const props = defineProps({
  categories: Array,
})

const imagePreview = ref(null)

const form = useForm({
  title: '',
  category_id: '',
  content: '',
  image: null,
})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  form.post(route('posts.store'), {
    forceFormData: true,
  })
}
</script>

<template>
  <BlogLayout>
    <div class="max-w-3xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">เขียนบทความใหม่</h1>
          <p class="text-sm text-slate-500">แบ่งปันข้อมูลและเรื่องราวลงในบล็อกของคุณ</p>
        </div>
        <Link :href="route('posts.index')" class="text-sm text-slate-500 hover:text-slate-800">
          ← ยกเลิก
        </Link>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Title -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">หัวข้อบทความ</label>
          <input
            v-model="form.title"
            type="text"
            placeholder="พิมพ์ชื่อหัวข้อที่น่าสนใจ..."
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition text-base"
            required
          />
          <div v-if="form.errors.title" class="text-rose-500 text-xs mt-1.5 font-medium">{{ form.errors.title }}</div>
        </div>

        <!-- Category -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">หมวดหมู่</label>
          <select
            v-model="form.category_id"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition text-sm bg-white"
            required
          >
            <option value="" disabled>-- เลือกหมวดหมู่ที่เกี่ยวข้อง --</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <div v-if="form.errors.category_id" class="text-rose-500 text-xs mt-1.5 font-medium">{{ form.errors.category_id }}</div>
        </div>

        <!-- Featured Image -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">ภาพหน้าปกบทความ</label>
          <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-indigo-400 transition bg-slate-50/50">
            <input
              type="file"
              accept="image/*"
              @change="handleImageChange"
              class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer"
            />
            <div v-if="imagePreview" class="mt-4">
              <img :src="imagePreview" class="w-full max-h-64 object-cover rounded-xl border border-slate-200 shadow-sm" />
            </div>
          </div>
          <div v-if="form.errors.image" class="text-rose-500 text-xs mt-1.5 font-medium">{{ form.errors.image }}</div>
        </div>

        <!-- Rich Text Editor -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-2">เนื้อหาบทความ</label>
          <RichEditor v-model="form.content" />
          <div v-if="form.errors.content" class="text-rose-500 text-xs mt-1.5 font-medium">{{ form.errors.content }}</div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
          <Link :href="route('posts.index')" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
            ยกเลิก
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-sm shadow-indigo-200 transition-all duration-150 disabled:opacity-50"
          >
            {{ form.processing ? 'กำลังบันทึก...' : 'เผยแพร่บทความ' }}
          </button>
        </div>
      </form>
    </div>
  </BlogLayout>
</template>