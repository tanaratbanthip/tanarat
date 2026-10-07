<script setup>
import { useForm, Link } from '@inertiajs/vue3'

// 1. รับทั้ง post และ categories ที่ส่งมาจาก Controller
const props = defineProps({
  post: Object,
  categories: Array
})

// 2. ใส่ category_id เดิมของโพสต์นี้เป็นค่าเริ่มต้น
const form = useForm({
  title: props.post.title,
  category_id: props.post.category_id,
  content: props.post.content
})

const submit = () => {
  form.put(`/posts/${props.post.id}`)
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-10 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">แก้ไขบทความ</h1>
      <Link href="/posts" class="text-sm text-gray-500 hover:underline">← ยกเลิก</Link>
    </div>

    <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded-lg shadow">
      <!-- 1. หัวข้อบทความ -->
      <div>
        <label class="block text-sm font-medium text-gray-700">หัวข้อบทความ</label>
        <input 
          v-model="form.title" 
          type="text" 
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
        />
        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
          {{ form.errors.title }}
        </div>
      </div>

      <!-- 2. Dropdown เลือกหมวดหมู่ -->
      <div>
        <label class="block text-sm font-medium text-gray-700">หมวดหมู่</label>
        <select 
          v-model="form.category_id" 
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-white"
        >
          <option value="" disabled>-- กรุณาเลือกหมวดหมู่ --</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">
          {{ form.errors.category_id }}
        </div>
      </div>

      <!-- 3. เนื้อหาบทความ -->
      <div>
        <label class="block text-sm font-medium text-gray-700">เนื้อหา</label>
        <textarea 
          v-model="form.content" 
          rows="5"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
        ></textarea>
        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">
          {{ form.errors.content }}
        </div>
      </div>

      <!-- ปุ่มอัปเดต -->
      <button 
        type="submit" 
        :disabled="form.processing"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
      >
        {{ form.processing ? 'กำลังบันทึก...' : 'อัปเดตข้อมูล' }}
      </button>
    </form>
  </div>
</template>