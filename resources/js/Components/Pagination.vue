<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: Array,
})
</script>

<template>
  <nav v-if="links && links.length > 3" class="flex flex-wrap items-center justify-center gap-1.5 pt-8">
    <template v-for="(link, index) in links" :key="index">
      <div
        v-if="!link.url"
        v-html="link.label"
        class="px-3.5 py-2 text-xs font-medium text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed select-none"
      />
      <Link
        v-else
        :href="link.url"
        v-html="link.label"
        :class="[
          link.active
            ? 'bg-indigo-600 text-white font-bold border-indigo-600 shadow-sm shadow-indigo-200'
            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-indigo-600'
        ]"
        class="px-3.5 py-2 text-xs font-medium border rounded-xl transition duration-150"
      />
    </template>
  </nav>
</template>