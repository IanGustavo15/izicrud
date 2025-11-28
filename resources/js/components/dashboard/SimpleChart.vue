<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <div v-if="actions" class="flex gap-2">
        <slot name="actions" />
      </div>
    </div>

    <div class="relative h-64 w-full">
      <!-- Gráfico de Linha Simples -->
      <svg v-if="type === 'line'" class="h-full w-full" viewBox="0 0 400 200">
        <defs>
          <linearGradient id="lineGradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" :style="`stop-color:${color};stop-opacity:0.3`" />
            <stop offset="100%" :style="`stop-color:${color};stop-opacity:0`" />
          </linearGradient>
        </defs>

        <!-- Grid lines -->
        <g stroke="#e5e7eb" stroke-width="0.5" opacity="0.5">
          <line v-for="i in 5" :key="`h-${i}`" :y1="i * 40" :y2="i * 40" x1="0" x2="400" />
          <line v-for="i in 8" :key="`v-${i}`" :x1="i * 50" :x2="i * 50" y1="0" y2="200" />
        </g>

        <!-- Area -->
        <path :d="areaPath" :fill="'url(#lineGradient)'" />

        <!-- Line -->
        <path :d="linePath" :stroke="color" stroke-width="2" fill="none" />

        <!-- Points -->
        <circle
          v-for="(point, index) in chartData"
          :key="index"
          :cx="(index * (400 / (chartData.length - 1)))"
          :cy="200 - (point.value / maxValue * 180 + 10)"
          r="3"
          :fill="color"
        />
      </svg>

      <!-- Gráfico de Barras Simples -->
      <svg v-else-if="type === 'bar'" class="h-full w-full" viewBox="0 0 400 200">
        <!-- Grid lines -->
        <g stroke="#e5e7eb" stroke-width="0.5" opacity="0.5">
          <line v-for="i in 5" :key="`h-${i}`" :y1="i * 40" :y2="i * 40" x1="0" x2="400" />
        </g>

        <!-- Bars -->
        <rect
          v-for="(point, index) in chartData"
          :key="index"
          :x="index * (400 / chartData.length) + 10"
          :y="200 - (point.value / maxValue * 180 + 10)"
          :width="(400 / chartData.length) - 20"
          :height="point.value / maxValue * 180 + 10"
          :fill="color"
          opacity="0.8"
          rx="2"
        />

        <!-- Labels -->
        <text
          v-for="(point, index) in chartData"
          :key="`label-${index}`"
          :x="index * (400 / chartData.length) + (400 / chartData.length) / 2"
          y="195"
          text-anchor="middle"
          class="text-xs fill-current text-muted-foreground"
        >
          {{ point.label }}
        </text>
      </svg>

      <!-- Gráfico de Pizza/Donut Simples -->
      <svg v-else-if="type === 'donut'" class="h-full w-full" viewBox="0 0 200 200">
        <g transform="translate(100, 100)">
          <circle r="80" fill="none" :stroke="color" stroke-width="20" opacity="0.1" />
          <circle
            r="80"
            fill="none"
            :stroke="color"
            stroke-width="20"
            :stroke-dasharray="`${percentage * 5.03} 503`"
            stroke-dashoffset="125.75"
            transform="rotate(-90)"
          />
          <text text-anchor="middle" dy="0.3em" class="text-2xl font-bold fill-current">
            {{ Math.round(percentage) }}%
          </text>
        </g>
      </svg>

      <!-- Loading state -->
      <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-black/50">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
      </div>
    </div>

    <!-- Legend -->
    <div v-if="showLegend && chartData.length > 1" class="mt-4 flex flex-wrap gap-4 text-sm">
      <div
        v-for="(point, index) in chartData"
        :key="`legend-${index}`"
        class="flex items-center gap-2"
      >
        <div class="h-3 w-3 rounded-full" :style="`background-color: ${getLegendColor(index)}`"></div>
        <span class="text-muted-foreground">{{ point.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface ChartDataPoint {
  label: string;
  value: number;
}

interface Props {
  title: string;
  type: 'line' | 'bar' | 'donut';
  data: ChartDataPoint[];
  color?: string;
  showLegend?: boolean;
  loading?: boolean;
  actions?: boolean;
  percentage?: number; // Para gráfico donut
}

const props = withDefaults(defineProps<Props>(), {
  color: '#3b82f6',
  showLegend: false,
  loading: false,
  actions: false,
  percentage: 75
});

const chartData = computed(() => props.data);
const maxValue = computed(() => Math.max(...props.data.map(d => d.value)));

const linePath = computed(() => {
  if (props.data.length === 0) return '';

  const points = props.data.map((point, index) => {
    const x = index * (400 / (props.data.length - 1));
    const y = 200 - (point.value / maxValue.value * 180 + 10);
    return `${x},${y}`;
  });

  return `M ${points.join(' L ')}`;
});

const areaPath = computed(() => {
  if (props.data.length === 0) return '';

  const points = props.data.map((point, index) => {
    const x = index * (400 / (props.data.length - 1));
    const y = 200 - (point.value / maxValue.value * 180 + 10);
    return `${x},${y}`;
  });

  return `M 0,200 L ${points.join(' L ')} L ${(props.data.length - 1) * (400 / (props.data.length - 1))},200 Z`;
});

const getLegendColor = (index: number) => {
  const colors = ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6', '#06b6d4'];
  return colors[index % colors.length];
};
</script>
