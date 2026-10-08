<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [StarterKit],
  editorProps: {
    attributes: {
      class: 'prose max-w-none focus:outline-none min-h-[160px] p-3 border rounded-b-md bg-white',
    },
  },
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML())
  },
})

watch(() => props.modelValue, (value) => {
  if (editor.value && editor.value.getHTML() !== value) {
    editor.value.commands.setContent(value, false)
  }
})
</script>

<template>
  <div v-if="editor" class="border rounded-md overflow-hidden">
    <!-- Toolbar เมนูปุ่มกด -->
    <div class="flex flex-wrap gap-2 p-2 bg-gray-100 border-b text-sm">
      <button
        type="button"
        @click="editor.chain().focus().toggleBold().run()"
        :class="{ 'bg-gray-300 font-bold': editor.isActive('bold') }"
        class="px-2 py-1 border rounded hover:bg-gray-200"
      >
        หนา
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleItalic().run()"
        :class="{ 'bg-gray-300 italic': editor.isActive('italic') }"
        class="px-2 py-1 border rounded hover:bg-gray-200"
      >
        เอียง
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="{ 'bg-gray-300': editor.isActive('heading', { level: 2 }) }"
        class="px-2 py-1 border rounded hover:bg-gray-200"
      >
        หัวข้อ H2
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="{ 'bg-gray-300': editor.isActive('bulletList') }"
        class="px-2 py-1 border rounded hover:bg-gray-200"
      >
        รายการจุด
      </button>
    </div>
    <EditorContent :editor="editor" />
  </div>
</template>