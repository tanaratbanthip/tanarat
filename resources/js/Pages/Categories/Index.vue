<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import BlogLayout from '@/Layouts/BlogLayout.vue'

defineProps({
  categories: Array,
})

// สถานะ Modal เพิ่ม/แก้ไข
const showModal = ref(false)
const isEditing = ref(false)
const editingCategoryId = ref(null)

const form = useForm({
  name: '',
})

const openCreateModal = () => {
  isEditing.value = false
  editingCategoryId.value = null
  form.reset()
  showModal.value = true
}

const openEditModal = (cat) => {
  isEditing.value = true
  editingCategoryId.value = cat.id
  form.name = cat.name
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('categories.update', editingCategoryId.value), {
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('categories.store'), {
      onSuccess: () => closeModal(),
    })
  }
}

// Modal ยืนยันการลบ
const showDeleteModal = ref(false)
const categoryToDelete = ref(null)

const openDeleteModal = (cat) => {
  categoryToDelete.value = cat
  showDeleteModal.value = true
}

const confirmDelete = () => {
  if (!categoryToDelete.value) return
  router.delete(route('categories.destroy', categoryToDelete.value.id), {
    onSuccess: () => (showDeleteModal.value = false),
  })
}
</script>

<template>
  <BlogLayout>
    <div class="max-w-4xl mx-auto py-2 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between pb-6 border-b border-slate-200/80">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">จัดการหมวดหมู่</h1>
          <p class="text-sm text-slate-500 mt-1">เพิ่ม แก้ไขชื่อ และควบคุมหมวดหมู่บทความทั้งหมด</p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>เพิ่มหมวดหมู่ใหม่</span>
        </button>
      </div>

      <!-- Categories Table -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-6 py-4">ชื่อหมวดหมู่</th>
              <th class="px-6 py-4">จำนวนบทความ</th>
              <th class="px-6 py-4 text-right">การจัดการ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50/80 transition">
              <td class="px-6 py-4">
                <span class="font-bold text-slate-800">#{{ cat.name }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">
                  {{ cat.posts_count }} บทความ
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-3 text-xs">
                <button
                  @click="openEditModal(cat)"
                  class="font-medium text-slate-500 hover:text-indigo-600 transition"
                >
                  แก้ไข
                </button>
                <span class="text-slate-200">|</span>
                <button
                  @click="openDeleteModal(cat)"
                  class="font-medium text-rose-500 hover:text-rose-700 transition"
                >
                  ลบ
                </button>
              </td>
            </tr>
            <tr v-if="categories.length === 0">
              <td colspan="3" class="px-6 py-8 text-center text-xs text-slate-400">
                ยังไม่มีหมวดหมู่ในระบบ
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit Category Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeModal"></div>
      <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <h3 class="text-lg font-bold text-slate-900">
          {{ isEditing ? 'แก้ไขชื่อหมวดหมู่' : 'สร้างหมวดหมู่ใหม่' }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">ชื่อหมวดหมู่</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="เช่น เทคโนโลยี, การเงิน, ท่องเที่ยว"
              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition"
              required
            />
            <div v-if="form.errors.name" class="text-rose-500 text-xs mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-100 transition"
            >
              ยกเลิก
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>
      <div class="relative bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </div>
        <div>
          <h4 class="text-base font-bold text-slate-900">ยืนยันการลบหมวดหมู่?</h4>
          <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            คุณต้องการลบหมวดหมู่ <span class="font-bold text-slate-800">"{{ categoryToDelete?.name }}"</span> ใช่หรือไม่?
          </p>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
          <button
            @click="showDeleteModal = false"
            class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100"
          >
            ยกเลิก
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700"
          >
            ยืนยันลบ
          </button>
        </div>
      </div>
    </div>
  </BlogLayout>
</template>