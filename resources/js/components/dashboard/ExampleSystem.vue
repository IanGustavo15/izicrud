<template>
  <div class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-accent">
    <div class="border-b border-sidebar-border/70 p-6 dark:border-sidebar-border">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold">🏥 Exemplo: Clínica Veterinária</h3>
          <p class="text-sm text-muted-foreground">Sistema completo gerado com make:crud</p>
        </div>
        <div class="rounded-lg bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-400">
          Sistema Pronto
        </div>
      </div>
    </div>

    <div class="p-6">
      <!-- Quick Stats -->
      <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600">142</div>
          <div class="text-xs text-muted-foreground">Donos</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">89</div>
          <div class="text-xs text-muted-foreground">Pets</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-purple-600">23</div>
          <div class="text-xs text-muted-foreground">Serviços</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-orange-600">67</div>
          <div class="text-xs text-muted-foreground">Ordens</div>
        </div>
      </div>

      <!-- Generated Models -->
      <div class="mb-6">
        <h4 class="mb-3 text-sm font-medium text-foreground">Modelos Gerados</h4>
        <div class="grid gap-2 md:grid-cols-2">
          <div
            v-for="model in models"
            :key="model.name"
            class="flex items-center gap-3 rounded-lg border border-sidebar-border/50 p-3"
          >
            <div class="rounded-full p-2" :class="model.bgColor">
              <div class="h-3 w-3 rounded-full" :class="model.dotColor"></div>
            </div>
            <div class="flex-1">
              <div class="text-sm font-medium">{{ model.name }}</div>
              <div class="text-xs text-muted-foreground">{{ model.fields }} campos</div>
            </div>
            <div class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-400">
              ✓
            </div>
          </div>
        </div>
      </div>

      <!-- Features Generated -->
      <div class="mb-6">
        <h4 class="mb-3 text-sm font-medium text-foreground">Recursos Implementados</h4>
        <div class="grid gap-2 md:grid-cols-2">
          <div
            v-for="feature in features"
            :key="feature"
            class="flex items-center gap-2 text-sm"
          >
            <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
            <span>{{ feature }}</span>
          </div>
        </div>
      </div>

      <!-- Commands Used -->
      <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50">
        <h4 class="mb-2 text-sm font-medium">Comandos Executados</h4>
        <div class="overflow-x-auto">
          <pre class="text-xs text-muted-foreground"><code>{{ commands }}</code></pre>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-4 flex gap-2">
        <button class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-blue-700">
          Ver Sistema
        </button>
        <button class="rounded-lg border border-sidebar-border/50 px-4 py-2 text-xs font-medium transition-colors hover:bg-sidebar-accent/50">
          Copiar Comandos
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const models = [
  {
    name: 'Dono',
    fields: '4',
    bgColor: 'bg-blue-100 dark:bg-blue-900/20',
    dotColor: 'bg-blue-500'
  },
  {
    name: 'Pet',
    fields: '6',
    bgColor: 'bg-green-100 dark:bg-green-900/20',
    dotColor: 'bg-green-500'
  },
  {
    name: 'Servico',
    fields: '5',
    bgColor: 'bg-purple-100 dark:bg-purple-900/20',
    dotColor: 'bg-purple-500'
  },
  {
    name: 'OrdemServico',
    fields: '7',
    bgColor: 'bg-orange-100 dark:bg-orange-900/20',
    dotColor: 'bg-orange-500'
  }
];

const features = [
  'CRUDs Completos com Views Vue',
  'Relacionamentos 1:1 (Pet → Dono)',
  'Relacionamentos 1:N (Ordem → Serviços)',
  'Soft Delete Integrado',
  'Componentes TypeScript',
  'Validação Automática',
  'Interface Rica com Modals',
  'Migrações Geradas'
];

const commands = `php artisan make:crud Dono nome:"Nome":string email:"E-mail":email
php artisan make:crud Pet nome:"Nome":string id_dono:"Dono":integer
php artisan make:crud Servico nome:"Nome":string preco:"Preço":moeda
php artisan make:crud OrdemServico servicos:"Serviços":pServico`;
</script>
