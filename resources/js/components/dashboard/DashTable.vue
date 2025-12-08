<!--
  DashTable Component - Tabela responsiva para dashboard com gerenciamento avançado de colunas

  DESCRIÇÃO:
  Componente de tabela reutilizável otimizado para dashboards com controle granular sobre layout,
  responsividade e comportamento das colunas. Ideal para espaços limitados e layouts dinâmicos.

  FUNCIONALIDADES:
  - ✅ Sistema avançado de largura de colunas (xs, sm, md, lg, xl, auto, full, custom)
  - ✅ Alinhamento configurável por coluna (left, center, right)
  - ✅ Responsividade inteligente com sistema de prioridades
  - ✅ Truncamento automático de texto longo
  - ✅ Paginação opcional
  - ✅ Ações customizáveis por linha (editar, excluir, etc.)
  - ✅ Formatação automática para valores monetários, status e trends
  - ✅ Avatares automáticos baseados em iniciais

  PROPRIEDADES DAS COLUNAS:

  interface Column {
    key: string;           // Chave do campo no objeto de dados
    label: string;         // Texto exibido no cabeçalho
    width?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full' | 'auto' | string;
    align?: 'left' | 'center' | 'right';
    priority?: 1-5;        // 1=alta (sempre visível), 5=baixa (oculta em mobile)
    truncate?: boolean;    // true=trunca texto longo, false=quebra linha
    minWidth?: string;     // Ex: "100px", "8rem"
    maxWidth?: string;     // Ex: "200px", "16rem"
  }

  LARGURAS DISPONÍVEIS:
  - 'xs': 48px (w-12) - Ideal para ícones, ações, IDs
  - 'sm': 80px (w-20) - Ideal para datas, valores pequenos
  - 'md': 128px (w-32) - Ideal para nomes, títulos curtos
  - 'lg': 192px (w-48) - Ideal para descrições, textos médios
  - 'xl': 256px (w-64) - Ideal para textos longos
  - 'full': 100% (w-full) - Ocupa todo espaço restante
  - 'auto': Automático (w-auto) - Ajuste baseado no conteúdo
  - string: Valor customizado - Ex: "150px", "w-36", "12rem"

  SISTEMA DE PRIORIDADES (RESPONSIVIDADE):
  - priority: 1 - Sempre visível (alta prioridade)
  - priority: 2 - Oculta em telas xs (< 640px)
  - priority: 3 - Oculta em telas sm (< 768px)
  - priority: 4 - Oculta em telas md (< 1024px)
  - priority: 5 - Oculta em telas lg (< 1280px)

  FORMATAÇÃO AUTOMÁTICA:
  O componente detecta automaticamente campos especiais e aplica formatação:
  - Campos monetários: 'value', 'total', 'preco', 'valor' → R$ 1.234,56
  - Status: 'status' → Badge colorido com indicador
  - Trends: 'trend' → Seta e porcentagem colorida
  - Avatares: 'avatar' → Círculo com iniciais

  EXEMPLO DE USO BÁSICO:

  <DashTable
    title="Lista de Produtos"
    :columns="[
      { key: 'id', label: '#', width: 'xs', align: 'center', priority: 1 },
      { key: 'nome', label: 'Nome', width: 'lg', align: 'left', priority: 1 },
      { key: 'categoria', label: 'Categoria', width: 'md', align: 'left', priority: 3 },
      { key: 'preco', label: 'Preço', width: 'sm', align: 'right', priority: 2 },
      { key: 'status', label: 'Status', width: 'xs', align: 'center', priority: 4 }
    ]"
    :data="produtos"
    :actions="true"
    :show-pagination="true"
    :items-per-page="10"
    @edit="editarItem"
    @delete="excluirItem"
  />

  EXEMPLO AVANÇADO COM AÇÕES CUSTOMIZADAS:

  <DashTable
    title="Ordens de Serviço"
    :columns="colunas"
    :data="ordens"
    :actions="true"
    @edit="editarOrdem"
    @delete="excluirOrdem"
  >
    <template #actions="{ item, index }">
      <button @click="visualizar(item)" title="Ver Detalhes">
        <EyeIcon class="h-4 w-4" />
      </button>
      <button @click="imprimir(item)" title="Imprimir">
        <PrinterIcon class="h-4 w-4" />
      </button>
      <button @click="editarOrdem(item)" title="Editar">
        <PencilIcon class="h-4 w-4" />
      </button>
    </template>
  </DashTable>

  PROPS PRINCIPAIS:
  - title: string - Título da tabela
  - columns: Column[] - Configuração das colunas
  - data: Record<string, any>[] - Array de objetos com dados
  - actions: boolean - Exibe coluna de ações
  - showPagination: boolean - Ativa paginação
  - itemsPerPage: number - Itens por página

  EVENTOS:
  - @edit: (item: Record<string, any>) - Emitido ao clicar em editar
  - @delete: (item: Record<string, any>) - Emitido ao clicar em excluir

  SLOTS:
  - #actions: { item, index } - Personaliza botões de ação por linha

  DICAS DE USO:
  1. Use priority para criar layouts responsivos inteligentes
  2. Combine width + priority para otimizar espaço em dashboards
  3. Use truncate: false apenas quando necessário (afeta performance)
  4. Prefira 'auto' para colunas com conteúdo variável
  5. Use alinhamento 'right' para valores numéricos
  6. Teste sempre em diferentes tamanhos de tela

  EXEMPLO NO CONTROLLER (Laravel):
  return [
    'columns' => [
      ['key' => 'id', 'label' => '#', 'width' => 'xs', 'align' => 'center', 'priority' => 1],
      ['key' => 'nome', 'label' => 'Nome', 'width' => 'lg', 'align' => 'left', 'priority' => 1, 'truncate' => true],
      ['key' => 'preco', 'label' => 'Preço', 'width' => 'sm', 'align' => 'right', 'priority' => 2],
      ['key' => 'status', 'label' => 'Status', 'width' => 'xs', 'align' => 'center', 'priority' => 3]
    ],
    'data' => $items->toArray()
  ];
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
            <table class="w-full">
                <thead class="border-t border-sidebar-border/70 dark:border-sidebar-border">
                    <tr class="text-left">
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'px-3 py-3 text-sm font-medium text-muted-foreground',
                                getColumnWidth(column),
                                getColumnAlign(column),
                                getColumnPriority(column)
                            ]"
                            :style="getColumnStyle(column)"
                            :title="`Width: ${column.width}, Align: ${column.align}, Priority: ${column.priority}`"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="actions" class="px-3 py-3 text-sm font-medium text-muted-foreground text-center w-24">
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
                            :class="[
                                'px-3 py-4',
                                getColumnWidth(column),
                                getColumnAlign(column),
                                getColumnPriority(column)
                            ]"
                            :style="getColumnStyle(column)"
                            :title="`Width: ${column.width}, Align: ${column.align}`"
                        >
                            <div v-if="column.key === 'avatar' && row[column.key]" :class="[
                                'flex items-center gap-3 min-w-0',
                                column.align === 'center' ? 'justify-center' : '',
                                column.align === 'right' ? 'justify-end' : ''
                            ]">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                                    {{ getInitials(row.numero || row.name || row.nome || 'OS') }}
                                </div>
                                <span class="font-medium truncate">{{ row.numero || row.name || row.nome }}</span>
                            </div>

                            <div v-else-if="column.key === 'status'" :class="[
                                'flex items-center gap-2 min-w-0',
                                column.align === 'center' ? 'justify-center' : '',
                                column.align === 'right' ? 'justify-end' : ''
                            ]">
                                <div
                                    class="h-2 w-2 rounded-full flex-shrink-0"
                                    :class="getStatusColor(row[column.key])"
                                ></div>
                                <span class="text-sm capitalize whitespace-nowrap">{{ row[column.key] }}</span>
                            </div>

                            <div v-else-if="column.key === 'value' || column.key === 'total' || column.key === 'preco' || column.key === 'valor'" :class="[
                                'font-mono text-sm whitespace-nowrap',
                                getColumnAlign(column)
                            ]">
                                {{ formatCurrency(row[column.key]) }}
                            </div>

                            <div v-else-if="column.key === 'trend'" :class="[
                                'flex items-center gap-1',
                                column.align === 'center' ? 'justify-center' : '',
                                column.align === 'right' ? 'justify-end' : ''
                            ]">
                                <svg
                                    class="h-4 w-4 flex-shrink-0"
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
                                <span class="text-sm whitespace-nowrap" :class="getTrendColor(row[column.key])">
                                    {{ Math.abs(row[column.key]) }}%
                                </span>
                            </div>

                            <div v-else :class="[
                                'text-sm',
                                shouldTruncate(column) ? 'truncate' : '',
                                column.maxWidth ? 'max-w-0' : ''
                            ]" :title="shouldTruncate(column) ? row[column.key] : undefined">
                                {{ row[column.key] }}
                            </div>
                        </td>

                        <!-- Coluna de Ações -->
                        <td v-if="actions" class="px-3 py-4 w-24">
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
import { computed, ref, watch } from 'vue';

interface Column {
    key: string;
    label: string;
    width?: 'auto' | 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full' | string; // Largura da coluna
    align?: 'left' | 'center' | 'right'; // Alinhamento do conteúdo
    minWidth?: string; // Largura mínima
    maxWidth?: string; // Largura máxima
    priority?: number; // Prioridade para esconder em telas pequenas (1=alta, 5=baixa)
    truncate?: boolean; // Se deve truncar texto longo
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

// Watch para resetar paginação quando dados mudam
watch(() => props.data, () => {
    currentPage.value = 1;
}, { deep: true });

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

// Funções utilitárias para largura e alinhamento
const getColumnWidth = (column: Column): string => {
    if (column.width === 'xs') return 'w-12';
    if (column.width === 'sm') return 'w-20';
    if (column.width === 'md') return 'w-32';
    if (column.width === 'lg') return 'w-48';
    if (column.width === 'xl') return 'w-64';
    if (column.width === 'full') return 'w-full';
    if (column.width === 'auto') return 'w-auto';
    if (typeof column.width === 'string' && column.width.startsWith('w-')) {
        return column.width;
    }
    // Para larguras CSS customizadas, não retornar classe Tailwind
    if (typeof column.width === 'string' && (column.width.includes('px') || column.width.includes('rem') || column.width.includes('%'))) {
        return '';
    }
    return 'w-auto'; // padrão
};

const getColumnStyle = (column: Column): Record<string, string> => {
    const style: Record<string, string> = {};

    if (column.minWidth) style.minWidth = column.minWidth;
    if (column.maxWidth) style.maxWidth = column.maxWidth;

    // Aplicar largura customizada apenas se não for uma classe Tailwind
    if (typeof column.width === 'string' &&
        !column.width.startsWith('w-') &&
        !['xs', 'sm', 'md', 'lg', 'xl', 'full', 'auto'].includes(column.width)) {
        style.width = column.width;
    }

    return style;
};

const getColumnAlign = (column: Column): string => {
    if (column.align === 'center') return 'text-center';
    if (column.align === 'right') return 'text-right';
    return 'text-left'; // padrão
};

const getColumnPriority = (column: Column): string => {
    if (!column.priority) return '';

    // Esconder colunas com prioridade baixa em telas pequenas
    if (column.priority === 5) return 'hidden xl:table-cell';
    if (column.priority === 4) return 'hidden lg:table-cell';
    if (column.priority === 3) return 'hidden md:table-cell';
    if (column.priority === 2) return 'hidden sm:table-cell';

    return ''; // prioridade 1 ou undefined = sempre visível
};

const shouldTruncate = (column: Column): boolean => {
    return column.truncate !== false; // padrão é true
};

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
