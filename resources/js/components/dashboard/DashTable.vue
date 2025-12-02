<!--
  DashTable Component

  Componente de tabela reutilizável para dashboard com:
  - Paginação opcional
  - Ações customizáveis (slot)
  - Botões padrão: Editar e Excluir
  - Formatação automática de moeda, status e trends

  Exemplo de uso com botões customizados:

  <DashTable
    title="Minha Tabela"
    :columns="columns"
    :data="data"
    :actions="true"
    @edit="handleEdit"
    @delete="handleDelete"
  >
    <template #actions="{ item, index }">
      <button @click="visualizar(item)" title="Ver Detalhes">
        <EyeIcon class="h-4 w-4" />
      </button>
      <button @click="handleEdit(item)" title="Editar">
        <PencilIcon class="h-4 w-4" />
      </button>
      <button @click="handleDelete(item)" title="Excluir">
        <TrashIcon class="h-4 w-4" />
      </button>
    </template>
  </DashTable>
-->
<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="flex items-center justify-between p-6 pb-4">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <div v-if="actions" class="flex gap-2">
        <slot name="actions" />
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full table-fixed">
        <thead class="border-t border-sidebar-border/70 dark:border-sidebar-border">
          <tr class="text-left">
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-3 text-sm font-medium text-muted-foreground truncate"
              :class="getColumnWidth(column.key)"
            >
              {{ column.label }}
            </th>
            <th v-if="actions" class="w-24 px-6 py-3 text-sm font-medium text-muted-foreground text-center">
              Ações
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
          <tr
            v-for="(row, index) in paginatedData"
            :key="index"
            class="group hover:bg-sidebar-accent/50 transition-colors duration-150"
          >
            <td
              v-for="column in columns"
              :key="`${index}-${column.key}`"
              class="px-6 py-4"
              :class="getColumnWidth(column.key)"
            >
              <div v-if="column.key === 'avatar' && row[column.key]" class="flex items-center gap-3 min-w-0">
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                  {{ getInitials(row.numero || row.name || row.nome || 'OS') }}
                </div>
                <span class="font-medium truncate">{{ row.numero || row.name || row.nome }}</span>
              </div>

              <div v-else-if="column.key === 'status'" class="flex items-center gap-2 min-w-0">
                <div
                  class="h-2 w-2 rounded-full flex-shrink-0"
                  :class="getStatusColor(row[column.key])"
                ></div>
                <span class="text-sm capitalize truncate">{{ row[column.key] }}</span>
              </div>

              <div v-else-if="column.key === 'value' || column.key === 'total' || column.key === 'preco' || column.key === 'valor'" class="font-mono text-sm text-right">
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

              <div v-else class="text-sm truncate" :title="row[column.key]">
                {{ row[column.key] }}
              </div>
            </td>

            <!-- Coluna de Ações -->
            <td v-if="actions" class="w-24 px-6 py-4">
              <div class="flex items-center justify-center gap-1">
                <!-- Slot para ações customizadas -->
                <slot name="actions" :item="row" :index="index">
                  <!-- Botões padrão: Editar e Excluir -->
                  <button
                    @click="handleEdit(row)"
                    class="rounded p-1.5 text-muted-foreground hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-150"
                    title="Editar"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="handleDelete(row)"
                    class="rounded p-1.5 text-muted-foreground hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150"
                    title="Excluir"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </slot>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="paginatedData.length === 0" class="p-8 text-center text-muted-foreground">
        <svg class="mx-auto h-12 w-12 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p>Nenhum dado disponível</p>
      </div>
    </div>

    <div v-if="showPagination && totalPages > 1" class="border-t border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
      <div class="flex items-center justify-between text-sm text-muted-foreground">
        <span>Mostrando {{ showingFrom }} a {{ showingTo }} de {{ totalItems }} resultados</span>
        <div class="flex items-center gap-2">
          <button
            @click="previousPage"
            :disabled="!canGoPrevious"
            class="rounded px-3 py-1 hover:bg-sidebar-accent disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
          >
            Anterior
          </button>

          <!-- Páginas -->
          <div class="flex items-center gap-1">
            <template v-for="page in Math.min(totalPages, 5)" :key="page">
              <button
                @click="goToPage(page)"
                :class="[
                  'rounded px-2 py-1 transition-colors duration-150',
                  page === currentPage
                    ? 'bg-blue-500 text-white'
                    : 'hover:bg-sidebar-accent'
                ]"
              >
                {{ page }}
              </button>
            </template>

            <span v-if="totalPages > 5" class="px-2">...</span>

            <button
              v-if="totalPages > 5 && currentPage < totalPages - 2"
              @click="goToPage(totalPages)"
              :class="[
                'rounded px-2 py-1 transition-colors duration-150',
                totalPages === currentPage
                  ? 'bg-blue-500 text-white'
                  : 'hover:bg-sidebar-accent'
              ]"
            >
              {{ totalPages }}
            </button>
          </div>

          <button
            @click="nextPage"
            :disabled="!canGoNext"
            class="rounded px-3 py-1 hover:bg-sidebar-accent disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
          >
            Próximo
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

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
  itemsPerPage?: number;
}

const props = withDefaults(defineProps<Props>(), {
  actions: false,
  showPagination: false,
  itemsPerPage: 5
});

// Emit para ações
const emit = defineEmits(['edit', 'delete']);

// Estado da paginação
const currentPage = ref(1);

// Computed properties para paginação
const totalPages = computed(() => Math.ceil(props.data.length / props.itemsPerPage));
const totalItems = computed(() => props.data.length);

const paginatedData = computed(() => {
  if (!props.showPagination) return props.data;

  const start = (currentPage.value - 1) * props.itemsPerPage;
  const end = start + props.itemsPerPage;
  return props.data.slice(start, end);
});

const showingFrom = computed(() => {
  if (props.data.length === 0) return 0;
  return (currentPage.value - 1) * props.itemsPerPage + 1;
});

const showingTo = computed(() => {
  const to = currentPage.value * props.itemsPerPage;
  return Math.min(to, props.data.length);
});

const canGoPrevious = computed(() => currentPage.value > 1);
const canGoNext = computed(() => currentPage.value < totalPages.value);

// Funções de navegação
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const previousPage = () => {
  if (canGoPrevious.value) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (canGoNext.value) {
    currentPage.value++;
  }
};

// Funções de ações
const handleEdit = (item: Record<string, any>) => {
  emit('edit', item);
};

const handleDelete = (item: Record<string, any>) => {
  emit('delete', item);
};

// Funções auxiliares
const getColumnWidth = (key: string) => {
  const widthMap: Record<string, string> = {
    avatar: 'w-1/3', // Coluna principal (nome/título)
    numero: 'w-1/3',
    nome: 'w-1/3',
    name: 'w-1/3',
    pet: 'w-1/4',
    cliente: 'w-1/4',
    servico: 'w-1/4',
    categoria: 'w-1/5',
    status: 'w-20',
    data: 'w-24',
    valor: 'w-24',
    value: 'w-24',
    total: 'w-24',
    preco: 'w-24',
    agendamentos: 'w-20',
    trend: 'w-16'
  };

  return widthMap[key] || 'w-auto';
};

const getInitials = (name: string) => {
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const getStatusColor = (status: string) => {
  const statusColors: Record<string, string> = {
    ativo: 'bg-green-500',
    inativo: 'bg-red-500',
    pendente: 'bg-yellow-500',
    online: 'bg-green-500',
    offline: 'bg-red-500'
  };

  return statusColors[status?.toLowerCase()] || 'bg-gray-500';
};

const formatCurrency = (value: number | string) => {
  const numValue = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(numValue || 0);
};

const getTrendColor = (trend: number) => {
  return trend >= 0
    ? 'text-green-600 dark:text-green-400'
    : 'text-red-600 dark:text-red-400';
};
</script>
