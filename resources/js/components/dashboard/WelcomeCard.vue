<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-foreground">{{ greeting }}</h2>
        <p class="text-muted-foreground">{{ subtitle }}</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-muted-foreground">{{ currentDate }}</p>
        <p class="text-sm text-muted-foreground">{{ currentTime }}</p>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
      <!-- Quick Actions -->
      <div class="space-y-4">
        <h3 class="font-medium text-foreground">Ações Rápidas</h3>
        <div class="grid gap-2">
          <button
            v-for="action in quickActions"
            :key="action.label"
            class="flex items-center gap-3 rounded-lg p-3 text-left transition-colors hover:bg-sidebar-accent/50 border border-sidebar-border/50"
            @click="action.action"
          >
            <div class="rounded-full p-2" :class="action.bgColor">
              <svg class="h-4 w-4" :class="action.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="action.icon === 'plus'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                <path v-else-if="action.icon === 'book'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-medium">{{ action.label }}</p>
              <p class="text-sm text-muted-foreground">{{ action.description }}</p>
            </div>
          </button>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="space-y-4">
        <h3 class="font-medium text-foreground">Atividade Recente</h3>
        <div class="space-y-3">
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-center gap-3 rounded-lg p-3 border border-sidebar-border/50"
          >
            <div class="h-2 w-2 rounded-full" :class="activity.statusColor"></div>
            <div class="flex-1">
              <p class="text-sm font-medium">{{ activity.title }}</p>
              <p class="text-xs text-muted-foreground">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tips -->
    <div class="mt-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
      <div class="flex items-start gap-3">
        <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-900/50">
          <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h4 class="font-medium text-blue-900 dark:text-blue-100">{{ currentTip.title }}</h4>
          <p class="text-sm text-blue-700 dark:text-blue-200">{{ currentTip.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

interface QuickAction {
  label: string;
  description: string;
  icon: string;
  bgColor: string;
  iconColor: string;
  action: () => void;
}

interface Activity {
  id: number;
  title: string;
  time: string;
  statusColor: string;
}

interface Tip {
  title: string;
  description: string;
}

const props = defineProps<{
  userName?: string;
}>();

const currentTime = ref('');
const currentDate = ref('');

const greeting = computed(() => {
  const hour = new Date().getHours();
  const name = props.userName || 'Desenvolvedor';

  if (hour < 12) {
    return `Bom dia, ${name}! ☀️`;
  } else if (hour < 18) {
    return `Boa tarde, ${name}! 🌤️`;
  } else {
    return `Boa noite, ${name}! 🌙`;
  }
});

const subtitle = computed(() => {
  const hour = new Date().getHours();

  if (hour < 12) {
    return 'Pronto para começar um novo dia produtivo?';
  } else if (hour < 18) {
    return 'Continue com o excelente trabalho!';
  } else {
    return 'Hora de finalizar as tarefas do dia.';
  }
});

const quickActions: QuickAction[] = [
  {
    label: 'Criar CRUD',
    description: 'Gerar um novo CRUD completo',
    icon: 'plus',
    bgColor: 'bg-green-100 dark:bg-green-900/20',
    iconColor: 'text-green-600 dark:text-green-400',
    action: () => console.log('Criar CRUD')
  },
  {
    label: 'Ver Documentação',
    description: 'Consultar guias e exemplos',
    icon: 'book',
    bgColor: 'bg-blue-100 dark:bg-blue-900/20',
    iconColor: 'text-blue-600 dark:text-blue-400',
    action: () => console.log('Documentação')
  },
  {
    label: 'Configurar Sistema',
    description: 'Ajustar configurações gerais',
    icon: 'cog',
    bgColor: 'bg-purple-100 dark:bg-purple-900/20',
    iconColor: 'text-purple-600 dark:text-purple-400',
    action: () => console.log('Configurações')
  }
];

const recentActivity: Activity[] = [
  {
    id: 1,
    title: 'Sistema inicializado com sucesso',
    time: 'Agora há pouco',
    statusColor: 'bg-green-500'
  },
  {
    id: 2,
    title: 'CRUD Generator carregado',
    time: 'Há 1 minuto',
    statusColor: 'bg-blue-500'
  },
  {
    id: 3,
    title: 'Dashboard pronto para uso',
    time: 'Há 2 minutos',
    statusColor: 'bg-green-500'
  }
];

const tips: Tip[] = [
  {
    title: 'Dica do Dia',
    description: 'Use o comando "php artisan make:crud" para criar CRUDs completos rapidamente!'
  },
  {
    title: 'Produtividade',
    description: 'Relacionamentos pivot são criados automaticamente com a sintaxe pModelo.'
  },
  {
    title: 'Interface Rica',
    description: 'Todos os formulários incluem componentes Vue dinâmicos para melhor UX.'
  }
];

const currentTip = computed(() => {
  const index = Math.floor(Date.now() / (1000 * 60 * 5)) % tips.length; // Muda a cada 5 minutos
  return tips[index];
});

const updateTime = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('pt-BR');
  currentDate.value = now.toLocaleDateString('pt-BR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

let timeInterval: number;

onMounted(() => {
  updateTime();
  timeInterval = setInterval(updateTime, 1000);
});

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});
</script>
