<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
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
    forceFormData: true, // สำคัญมากสำหรับฟอร์มที่มีไฟล์
    onError: (errors) => {
      console.log('Validation Errors:', errors);
    }
  });
};
</script>

<template>
  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">เขียนบทความใหม่</h1>
      <Link :href="route('posts.index')" class="text-gray-600 hover:underline">← ย้อนกลับ</Link>
    </div>

    <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-4">
      <!-- ชื่อบทความ -->
      <div>
        <label class="block text-sm font-medium mb-1">หัวข้อบทความ</label>
        <input
          v-model="form.title"
          type="text"
          class="w-full border rounded p-2 focus:ring focus:ring-blue-200 outline-none"
          required
        />
        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
      </div>

      <!-- หมวดหมู่ -->
      <div>
        <label class="block text-sm font-medium mb-1">หมวดหมู่</label>
        <select
          v-model="form.category_id"
          class="w-full border rounded p-2 focus:ring focus:ring-blue-200 outline-none"
          required
        >
          <option value="" disabled>-- กรุณาเลือกหมวดหมู่ --</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
      </div>

      <!-- อัปโหลดภาพหน้าปกและพรีวิว -->
      <div>
        <label class="block text-sm font-medium mb-1">ภาพหน้าปกบทความ</label>
        <input
          type="file"
          accept="image/*"
          @change="handleImageChange"
          class="w-full border rounded p-2"
        />
        <div v-if="imagePreview" class="mt-3">
          <p class="text-xs text-gray-500 mb-1">ตัวอย่างรูปภาพ:</p>
          <img :src="imagePreview" class="w-full max-h-64 object-cover rounded border" />
        </div>
        <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
      </div>

      <!-- เนื้อหาบทความ (Rich Text Editor) -->
      <div>
        <label class="block text-sm font-medium mb-1">เนื้อหาบทความ</label>
        <RichEditor v-model="form.content" />
        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
      </div>

      <!-- ปุ่มส่งข้อมูล -->
      <div>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
        >
          {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกบทความ' }}
        </button>
      </div>
    </form>
  </div>
</template>