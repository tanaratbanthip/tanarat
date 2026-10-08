<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import RichEditor from '@/Components/RichEditor.vue'

const props = defineProps({
  post: Object,
  categories: Array,
})

const imagePreview = ref(props.post.image ? `/storage/${props.post.image}` : null)

// ใน Laravel การอัปเดตไฟล์แบบ multipart ต้องส่งเป็น POST แล้วพ่วง _method: 'PUT'
const form = useForm({
  _method: 'PUT',
  title: props.post.title,
  category_id: props.post.category_id,
  content: props.post.content,
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
  form.post(route('posts.update', props.post.slug || props.post.id), {
    forceFormData: true,
  })
}
</script>

<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">แก้ไขบทความ</h1>
      <Link :href="route('posts.index')" class="text-sm text-gray-500 hover:text-gray-700">← ยกเลิก</Link>
    </div>

    <form @submit.prevent="submit" class="bg-white p-6 rounded-2xl shadow-sm border space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">หัวข้อบทความ</label>
        <input
          v-model="form.title"
          type="text"
          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
          required
        />
        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
        <select
          v-model="form.category_id"
          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
          required
        >
          <option value="" disabled>-- กรุณาเลือกหมวดหมู่ --</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">เปลี่ยนภาพหน้าปก</label>
        <input
          type="file"
          accept="image/*"
          @change="handleImageChange"
          class="w-full border border-gray-300 rounded-lg p-2 text-sm"
        />
        <div v-if="imagePreview" class="mt-3">
          <p class="text-xs text-gray-500 mb-1">รูปหน้าปกปัจจุบัน / ตัวอย่างใหม่:</p>
          <img :src="imagePreview" class="w-full max-h-56 object-cover rounded-lg border" />
        </div>
        <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">เนื้อหาบทความ</label>
        <RichEditor v-model="form.content" />
        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
      </div>

      <div class="pt-2">
        <button
          type="submit"
          :disabled="form.processing"
          class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition"
        >
          {{ form.processing ? 'กำลังบันทึก...' : 'อัปเดตข้อมูล' }}
        </button>
      </div>
    </form>
  </div>
</template>