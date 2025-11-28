<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-sm dark:border-sidebar-border dark:from-blue-900/20 dark:to-indigo-900/20">
    <div class="text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600">
        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
      </div>

      <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
        {{ title }}
      </h3>

      <p class="text-gray-600 dark:text-gray-300 mb-4">
        {{ description }}
      </p>

      <div class="space-y-2">
        <div
          v-for="feature in features"
          :key="feature"
          class="flex items-center justify-center gap-2 text-sm text-gray-700 dark:text-gray-300"
        >
          <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
          <span>{{ feature }}</span>
        </div>
      </div>

      <button
        class="mt-4 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-2 text-sm font-medium text-white transition-all hover:from-blue-600 hover:to-indigo-700 hover:shadow-lg"
        @click="$emit('action')"
      >
        {{ buttonText }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description: string;
  features: string[];
  buttonText: string;
}

const props = defineProps<Props>();

defineEmits<{
  action: [];
}>();
</script>
