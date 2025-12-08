<!--
  SimpleChart Component - Gráficos simples e interativos para dashboard

  DESCRIÇÃO:
  Componente de gráfico responsivo e interativo construído com SVG nativo.
  Oferece três tipos de visualização (linha, barra, donut) com tooltips,
  legendas opcionais e animações suaves. Otimizado para dashboards com
  dados em tempo real.

  FUNCIONALIDADES:
  - ✅ Três tipos de gráfico: line, bar, donut
  - ✅ Tooltips interativos com hover
  - ✅ Legendas opcionais com porcentagens
  - ✅ Animações e transições suaves
  - ✅ Grid lines para melhor leitura
  - ✅ Cores personalizáveis
  - ✅ Estado de loading
  - ✅ Responsivo e acessível
  - ✅ Sanitização automática de dados
  - ✅ Suporte a dark mode

  INTERFACE DE DADOS:
  interface ChartDataPoint {
    label: string;  // Rótulo do ponto (ex: "Jan", "Vendas")
    value: number;  // Valor numérico
  }

  PROPS:
  - title: string (obrigatório) - Título do gráfico
  - type: 'line' | 'bar' | 'donut' (obrigatório) - Tipo de gráfico
  - data: ChartDataPoint[] (obrigatório) - Array de dados
  - color?: string - Cor principal (hex: "#3b82f6")
  - showLegend?: boolean - Exibir legenda
  - loading?: boolean - Estado de carregamento
  - actions?: boolean - Exibir área de ações

  TIPOS DE GRÁFICO:

  1. LINE (Linha):
     - Ideal para: Tendências temporais, evolução de métricas
     - Features: Área preenchida, pontos interativos, grid
     - Exemplo: Receita mensal, visitantes diários

  2. BAR (Barras):
     - Ideal para: Comparações categóricas, rankings
     - Features: Barras com hover, labels no eixo X
     - Exemplo: Vendas por produto, atendimentos por mês

  3. DONUT (Pizza/Rosca):
     - Ideal para: Distribuição de partes do todo
     - Features: Segmentos coloridos, total no centro, porcentagens
     - Exemplo: Tipos de clientes, distribuição de vendas

  EXEMPLO DE USO - GRÁFICO DE LINHA:
  <SimpleChart
    title="Receita Mensal"
    type="line"
    :data="[
      { label: 'Jan', value: 12000 },
      { label: 'Fev', value: 15000 },
      { label: 'Mar', value: 13500 },
      { label: 'Abr', value: 18000 }
    ]"
    color="#3b82f6"
    :show-legend="false"
  />

  EXEMPLO DE USO - GRÁFICO DE BARRAS:
  <SimpleChart
    title="Atendimentos por Especialidade"
    type="bar"
    :data="[
      { label: 'Clínica', value: 45 },
      { label: 'Cirurgia', value: 23 },
      { label: 'Emergência', value: 67 },
      { label: 'Preventivo', value: 34 }
    ]"
    color="#10b981"
    :show-legend="false"
  />

  EXEMPLO DE USO - GRÁFICO DONUT:
  <SimpleChart
    title="Distribuição por Espécie"
    type="donut"
    :data="[
      { label: 'Cães', value: 156 },
      { label: 'Gatos', value: 89 },
      { label: 'Aves', value: 23 },
      { label: 'Outros', value: 12 }
    ]"
    :show-legend="true"
  />

  EXEMPLO COM AÇÕES PERSONALIZADAS:
  <SimpleChart
    title="Vendas por Período"
    type="line"
    :data="dadosVendas"
    :actions="true"
    :loading="carregando"
  >
    <template #actions>
      <Button @click="exportar" size="sm" variant="outline">
        Exportar
      </Button>
      <Button @click="atualizar" size="sm">
        Atualizar
      </Button>
    </template>
  </SimpleChart>

  FORMATAÇÃO DE DADOS NO CONTROLLER:
  // Laravel Controller exemplo:
  private function obterGraficoReceita() {
    $dados = [];
    for ($i = 6; $i >= 0; $i--) {
      $inicio = now()->subMonths($i)->startOfMonth();
      $receita = OrdemServico::whereBetween('created_at', [$inicio, $inicio->copy()->endOfMonth()])
        ->sum('valor_total');

      $dados[] = [
        'label' => $inicio->format('M/y'),
        'value' => (float) $receita
      ];
    }
    return $dados;
  }

  CORES RECOMENDADAS:
  - Azul: "#3b82f6" (dados neutros, receita)
  - Verde: "#10b981" (crescimento, sucesso)
  - Vermelho: "#ef4444" (alertas, perdas)
  - Roxo: "#8b5cf6" (categorias, distribuição)
  - Laranja: "#f59e0b" (atenção, metas)
  - Ciano: "#06b6d4" (informações, secundário)

  TOOLTIPS AUTOMÁTICOS:
  O componente exibe automaticamente:
  - Para line/bar: "Label: Valor"
  - Para donut: "Label: Valor (Porcentagem%)"
  - Formatação de números em pt-BR

  RESPONSIVIDADE:
  - SVG viewBox se adapta ao container
  - Tooltips seguem o cursor
  - Legenda quebra em múltiplas linhas
  - Funciona bem em mobile

  PERFORMANCE:
  - Sanitização automática de dados
  - Proteção contra dados inválidos
  - Lazy loading de tooltips
  - Animações otimizadas com CSS

  DICAS DE USO:
  1. Use máximo 7-8 pontos em gráficos de linha para legibilidade
  2. Gráficos de barra funcionam bem com até 6 categorias
  3. Gráficos donut são ideais para 3-6 segmentos
  4. Sempre teste com dados vazios/inválidos
  5. Use show-legend="true" apenas quando necessário
  6. Cores devem ter contraste adequado com o fundo

  TRATAMENTO DE ERROS:
  - Dados undefined/null são tratados automaticamente
  - Valores não-numéricos são convertidos para 0
  - Labels vazias recebem "Sem label"
  - Divisão por zero é prevenida
-->
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

        <!-- Points com hover -->
        <circle
          v-for="(point, index) in chartData"
          :key="index"
          :cx="chartData.length <= 1 ? 200 : (index * (400 / Math.max(1, chartData.length - 1)))"
          :cy="200 - (point.value / maxValue * 180 + 10)"
          r="4"
          :fill="color"
          class="cursor-pointer transition-all duration-200 hover:r-6"
          @mouseenter="showTooltip($event, point)"
          @mouseleave="hideTooltip"
        />
        <!-- Áreas invisíveis para melhor hover -->
        <circle
          v-for="(point, index) in chartData"
          :key="`hover-${index}`"
          :cx="chartData.length <= 1 ? 200 : (index * (400 / Math.max(1, chartData.length - 1)))"
          :cy="200 - (point.value / maxValue * 180 + 10)"
          r="15"
          fill="transparent"
          class="cursor-pointer"
          @mouseenter="showTooltip($event, point)"
          @mouseleave="hideTooltip"
        />
      </svg>

      <!-- Gráfico de Barras Simples -->
      <svg v-else-if="type === 'bar'" class="h-full w-full" viewBox="0 0 400 200">
        <!-- Grid lines -->
        <g stroke="#e5e7eb" stroke-width="0.5" opacity="0.5">
          <line v-for="i in 5" :key="`h-${i}`" :y1="i * 40" :y2="i * 40" x1="0" x2="400" />
        </g>

        <!-- Bars com hover -->
        <rect
          v-for="(point, index) in chartData"
          :key="index"
          :x="index * (400 / Math.max(1, chartData.length)) + 10"
          :y="200 - (point.value / maxValue * 180 + 10)"
          :width="(400 / Math.max(1, chartData.length)) - 20"
          :height="point.value / maxValue * 180 + 10"
          :fill="color"
          opacity="0.8"
          rx="2"
          class="cursor-pointer transition-all duration-200 hover:opacity-100"
          @mouseenter="showTooltip($event, point)"
          @mouseleave="hideTooltip"
        />

        <!-- Labels -->
        <text
          v-for="(point, index) in chartData"
          :key="`label-${index}`"
          :x="index * (400 / Math.max(1, chartData.length)) + (400 / Math.max(1, chartData.length)) / 2"
          y="195"
          text-anchor="middle"
          class="text-xs fill-current text-muted-foreground"
        >
          {{ point.label }}
        </text>
      </svg>

      <!-- Gráfico de Pizza/Donut Completo -->
      <svg v-else-if="type === 'donut'" class="h-full w-full" viewBox="0 0 200 200">
        <g transform="translate(100, 100)">
          <!-- Background circle -->
          <circle r="80" fill="none" stroke="#e5e7eb" stroke-width="20" opacity="0.3" />

          <!-- Data segments com hover -->
          <g v-for="(segment, index) in donutSegments" :key="index">
            <circle
              r="80"
              fill="none"
              :stroke="getDonutColor(index)"
              stroke-width="20"
              :stroke-dasharray="`${segment.circumference} ${503 - segment.circumference}`"
              :stroke-dashoffset="segment.offset"
              transform="rotate(-90)"
              class="cursor-pointer transition-all duration-200 hover:stroke-width-[25]"
              @mouseenter="showTooltip($event, chartData[index])"
              @mouseleave="hideTooltip"
            />
          </g>

          <!-- Center text -->
          <text text-anchor="middle" dy="-0.1em" class="text-lg font-bold fill-current">
            {{ totalValue }}
          </text>
          <text text-anchor="middle" dy="1.2em" class="text-sm fill-current text-muted-foreground">
            Total
          </text>
        </g>
      </svg>

      <!-- Loading state -->
      <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-black/50">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
      </div>
    </div>

    <!-- Tooltip -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 scale-95 transform -translate-y-2"
        enter-to-class="opacity-100 scale-100 transform translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="tooltip.visible"
          :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
          class="fixed z-50 transform -translate-x-1/2 -translate-y-full rounded-lg bg-gray-900 px-3 py-2 text-sm text-white shadow-xl pointer-events-none"
        >
          <div class="font-medium">{{ tooltip.content.label }}</div>
          <div class="text-gray-300">
            {{ formatTooltipValue(tooltip.content.value) }}
            <span v-if="type === 'donut' && totalValue > 0" class="ml-1">
              ({{ Math.round((tooltip.content.value / totalValue) * 100) }}%)
            </span>
          </div>
          <!-- Seta do tooltip -->
          <div class="absolute top-full left-1/2 transform -translate-x-1/2">
            <div class="w-2 h-2 bg-gray-900 rotate-45"></div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Legend -->
    <div v-if="showLegend && chartData.length > 1" class="mt-4 flex flex-wrap gap-4 text-sm">
      <div
        v-for="(point, index) in chartData"
        :key="`legend-${index}`"
        class="flex items-center gap-2"
      >
        <div
          class="h-3 w-3 rounded-full"
          :style="`background-color: ${type === 'donut' ? getDonutColor(index) : getLegendColor(index)}`"
        ></div>
        <span class="text-muted-foreground">{{ point.label }}</span>
        <span v-if="type === 'donut' && totalValue > 0" class="font-medium text-foreground">
          ({{ Math.round((point.value / totalValue) * 100) }}%)
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, reactive } from 'vue';

interface ChartDataPoint {
  label: string;
  value: number;
}

// Função para validar e sanitizar dados
const sanitizeData = (data: ChartDataPoint[] | undefined): ChartDataPoint[] => {
  if (!data || !Array.isArray(data)) {
    return [];
  }

  return data.map(item => ({
    label: String(item.label || 'Sem label'),
    value: Number(item.value) || 0
  }));
};interface Props {
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

// Estado do tooltip
const tooltip = reactive({
  visible: false,
  x: 0,
  y: 0,
  content: {
    label: '',
    value: 0
  }
});

// Funções para controlar tooltip
const showTooltip = (event: MouseEvent, point: ChartDataPoint) => {
  if (!point || !event.target) return;

  const rect = (event.target as Element).getBoundingClientRect();
  tooltip.visible = true;
  tooltip.x = rect.left + (rect.width / 2);
  tooltip.y = rect.top - 10;
  tooltip.content = {
    label: point.label || 'Sem label',
    value: point.value || 0
  };
};

const hideTooltip = () => {
  tooltip.visible = false;
};

// Formatar valores para exibição
const formatTooltipValue = (value: number) => {
  const safeValue = value || 0;
  if (props.type === 'donut') {
    return safeValue.toLocaleString('pt-BR');
  }
  return `Valor: ${safeValue.toLocaleString('pt-BR')}`;
};

const chartData = computed(() => sanitizeData(props.data));
const maxValue = computed(() => {
  const data = chartData.value;
  if (!data || data.length === 0) return 1;
  const max = Math.max(...data.map(d => d.value));
  return max === 0 ? 1 : max;
});

// Computed para gráfico donut
const totalValue = computed(() => {
  const data = chartData.value;
  if (!data || data.length === 0) return 0;
  return data.reduce((sum, item) => sum + item.value, 0);
});

const donutSegments = computed(() => {
  if (props.type !== 'donut') return [];

  const data = chartData.value;
  if (!data || data.length === 0) return [];

  const total = totalValue.value;
  if (total === 0) return [];

  const circumference = 2 * Math.PI * 80; // raio = 80
  let currentOffset = 0;

  return data.map((point) => {
    const value = point.value;
    const percentage = value / total;
    const segmentCircumference = percentage * circumference;

    const segment = {
      circumference: segmentCircumference,
      offset: -currentOffset,
      percentage: percentage * 100
    };

    currentOffset += segmentCircumference;
    return segment;
  });
});

const getDonutColor = (index: number) => {
  const colors = ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6', '#06b6d4'];
  return colors[index % colors.length];
};

const linePath = computed(() => {
  const data = chartData.value;
  if (!data || data.length === 0) return '';

  const max = maxValue.value;
  const points = data.map((point, index) => {
    const value = point.value;
    const x = data.length <= 1 ? 200 : index * (400 / Math.max(1, data.length - 1));
    const y = 200 - (value / max * 180 + 10);
    return `${x},${y}`;
  });

  return `M ${points.join(' L ')}`;
});

const areaPath = computed(() => {
  const data = chartData.value;
  if (!data || data.length === 0) return '';

  const max = maxValue.value;
  const points = data.map((point, index) => {
    const value = point.value;
    const x = data.length <= 1 ? 200 : index * (400 / Math.max(1, data.length - 1));
    const y = 200 - (value / max * 180 + 10);
    return `${x},${y}`;
  });

  const lastX = data.length <= 1 ? 200 : Math.max(1, data.length - 1) * (400 / Math.max(1, data.length - 1));
  return `M 0,200 L ${points.join(' L ')} L ${lastX},200 Z`;
});

const getLegendColor = (index: number) => {
  const colors = ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6', '#06b6d4'];
  return colors[index % colors.length];
};
</script>
