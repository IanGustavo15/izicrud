<!--
  StatsCard Component - Card de estatísticas para dashboard

  DESCRIÇÃO:
  Componente de card responsivo para exibir estatísticas e métricas importantes
  no dashboard. Suporta indicadores visuais de tendência, ícones e diferentes
  variantes de cor baseadas no contexto dos dados.

  FUNCIONALIDADES:
  - ✅ Exibição de título, valor principal e subtítulo
  - ✅ Indicador de mudança/tendência com cores automáticas
  - ✅ Ícones opcionais com cores contextuais
  - ✅ Suporte a dark mode
  - ✅ Variantes de cor (default, success, warning, danger)
  - ✅ Formatação automática de números grandes
  - ✅ Layout responsivo e acessível

  PROPS:
  - title: string (obrigatório) - Título da métrica
  - value: string | number (obrigatório) - Valor principal a ser exibido
  - change?: string - Indicador de mudança (ex: "+12.5%", "-3.2%")
  - subtitle?: string - Texto explicativo adicional
  - icon?: Component - Componente de ícone (ex: TrendingUpIcon)
  - variant?: 'default' | 'success' | 'warning' | 'danger' - Tema de cores

  DETECÇÃO AUTOMÁTICA DE TENDÊNCIA:
  O componente detecta automaticamente se a mudança é positiva ou negativa:
  - Símbolos positivos: "+", "↑" → Cor verde
  - Símbolos negativos: "-", "↓" → Cor vermelha
  - Sem símbolos → Cor padrão

  VARIANTES DE COR:
  - 'default': Azul (neutro)
  - 'success': Verde (positivo)
  - 'warning': Amarelo (atenção)
  - 'danger': Vermelho (crítico)

  EXEMPLO DE USO BÁSICO:
  <StatsCard
    title="Vendas do Mês"
    value="1.234"
    change="+12.5%"
    subtitle="últimos 30 dias"
  />

  EXEMPLO COM ÍCONE E VARIANTE:
  <StatsCard
    title="Receita Total"
    :value="formatCurrency(45250)"
    change="+8.2%"
    subtitle="comparado ao mês anterior"
    :icon="DollarSignIcon"
    variant="success"
  />

  EXEMPLO COM VALOR NUMÉRICO GRANDE:
  <StatsCard
    title="Usuários Ativos"
    :value="1250000"
    change="+15.3%"
    subtitle="usuários únicos"
    :icon="UsersIcon"
    variant="default"
  />

  FORMATAÇÃO RECOMENDADA DE VALORES:
  - Números pequenos: Exibir como string "1.234"
  - Números grandes: Usar abreviações "1.2M", "45.3K"
  - Moeda: "R$ 1.234,56" ou usar formatCurrency()
  - Porcentagens: "85%", "12.5%"

  DICAS DE USO:
  1. Use variant="success" para métricas positivas
  2. Use variant="warning" para métricas que precisam atenção
  3. Use variant="danger" para métricas críticas
  4. Ícones devem ser importados e passados como componente
  5. O change é opcional mas melhora a percepção de tendência
  6. Teste sempre em dark mode para garantir contraste

  INTEGRAÇÃO COM CONTROLLER:
  // No Laravel Controller:
  return [
    'dadosEstatisticas' => [
      [
        'title' => 'Total de Vendas',
        'value' => number_format($vendas, 0, ',', '.'),
        'change' => $crescimento >= 0 ? '+' . $crescimento . '%' : $crescimento . '%',
        'subtitle' => 'vendas realizadas',
        'variant' => $crescimento >= 0 ? 'success' : 'warning'
      ]
    ]
  ];

  ACESSIBILIDADE:
  - Usa cores com contraste adequado
  - Textos são legíveis em dark/light mode
  - Estrutura semântica apropriada
  - Suporte a tecnologias assistivas
-->
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
