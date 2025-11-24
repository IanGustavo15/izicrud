<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-muted-foreground">{{ title }}</p>
        <div class="flex items-baseline gap-2">
          <p class="text-2xl font-bold">{{ value }}</p>
          <span v-if="change" :class="changeClass" class="text-xs font-medium">
            {{ change }}
          </span>
        </div>
        <p v-if="subtitle" class="text-xs text-muted-foreground mt-1">{{ subtitle }}</p>
      </div>
      <div v-if="icon" class="rounded-full p-2" :class="iconBgClass">
        <component :is="icon" class="h-4 w-4" :class="iconClass" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  title: string;
  value: string | number;
  change?: string;
  subtitle?: string;
  icon?: any;
  variant?: 'default' | 'success' | 'warning' | 'danger';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default'
});

const changeClass = computed(() => {
  if (!props.change) return '';
  const isPositive = props.change.includes('+') || props.change.includes('↑');
  return isPositive
    ? 'text-green-600 dark:text-green-400'
    : 'text-red-600 dark:text-red-400';
});

const iconBgClass = computed(() => {
  const variants = {
    default: 'bg-blue-100 dark:bg-blue-900/20',
    success: 'bg-green-100 dark:bg-green-900/20',
    warning: 'bg-yellow-100 dark:bg-yellow-900/20',
    danger: 'bg-red-100 dark:bg-red-900/20'
  };
  return variants[props.variant];
});

const iconClass = computed(() => {
  const variants = {
    default: 'text-blue-600 dark:text-blue-400',
    success: 'text-green-600 dark:text-green-400',
    warning: 'text-yellow-600 dark:text-yellow-400',
    danger: 'text-red-600 dark:text-red-400'
  };
  return variants[props.variant];
});
</script>
