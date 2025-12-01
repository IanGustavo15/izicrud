<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="flex items-center justify-between p-6 pb-4">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <div v-if="actions" class="flex gap-2">
        <slot name="actions" />
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="border-t border-sidebar-border/70 dark:border-sidebar-border">
          <tr class="text-left">
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-3 text-sm font-medium text-muted-foreground"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
          <tr
            v-for="(row, index) in data"
            :key="index"
            class="group hover:bg-sidebar-accent/50"
          >
            <td
              v-for="column in columns"
              :key="`${index}-${column.key}`"
              class="px-6 py-4"
            >
              <div v-if="column.key === 'avatar' && row[column.key]" class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-medium">
                  {{ getInitials(row.name || row.nome || 'U') }}
                </div>
                <span class="font-medium">{{ row.name || row.nome }}</span>
              </div>

              <div v-else-if="column.key === 'status'" class="flex items-center gap-2">
                <div
                  class="h-2 w-2 rounded-full"
                  :class="getStatusColor(row[column.key])"
                ></div>
                <span class="text-sm">{{ row[column.key] }}</span>
              </div>

              <div v-else-if="column.key === 'value' || column.key === 'total' || column.key === 'preco'" class="font-mono text-sm">
                {{ formatCurrency(row[column.key]) }}
              </div>

              <div v-else-if="column.key === 'trend'" class="flex items-center gap-1">
                <svg
                  class="h-4 w-4"
                  :class="getTrendColor(row[column.key])"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    v-if="row[column.key] >= 0"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                  />
                  <path
                    v-else
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                  />
                </svg>
                <span class="text-sm" :class="getTrendColor(row[column.key])">
                  {{ Math.abs(row[column.key]) }}%
                </span>
              </div>

              <div v-else-if="column.key === 'actions'" class="flex items-center gap-2">
                <button class="rounded p-1 text-muted-foreground hover:text-foreground hover:bg-sidebar-accent">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button class="rounded p-1 text-muted-foreground hover:text-foreground hover:bg-sidebar-accent">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
              </div>

              <div v-else class="text-sm">
                {{ row[column.key] }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="data.length === 0" class="p-8 text-center text-muted-foreground">
        <svg class="mx-auto h-12 w-12 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p>Nenhum dado disponível</p>
      </div>
    </div>

    <div v-if="showPagination" class="border-t border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
      <div class="flex items-center justify-between text-sm text-muted-foreground">
        <span>Mostrando {{ data.length }} resultados</span>
        <div class="flex items-center gap-2">
          <button class="rounded px-2 py-1 hover:bg-sidebar-accent disabled:opacity-50" disabled>
            Anterior
          </button>
          <span class="px-2">1</span>
          <button class="rounded px-2 py-1 hover:bg-sidebar-accent disabled:opacity-50" disabled>
            Próximo
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Column {
  key: string;
  label: string;
}

interface Props {
  title: string;
  columns: Column[];
  data: Record<string, any>[];
  actions?: boolean;
  showPagination?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  actions: false,
  showPagination: false
});

const getInitials = (name: string) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getStatusColor = (status: string) => {
  const colors = {
    'ativo': 'bg-green-500',
    'inativo': 'bg-red-500',
    'em_aberto': 'bg-yellow-500',
    'em_andamento': 'bg-blue-500',
    'finalizado': 'bg-green-500',
    'cancelado': 'bg-red-500',
  };
  return colors[status as keyof typeof colors] || 'bg-gray-500';
};

const formatCurrency = (value: number) => {
  if (typeof value !== 'number') return value;
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

const getTrendColor = (trend: number) => {
  return trend >= 0
    ? 'text-green-600 dark:text-green-400'
    : 'text-red-600 dark:text-red-400';
};
</script>
