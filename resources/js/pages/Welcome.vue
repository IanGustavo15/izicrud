<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        title: 'CRUD Generator Avançado',
        description: 'Crie modelos, controladores, views Vue, migrações e rotas com um único comando.',
        icon: '⚡'
    },
    {
        title: 'Relacionamentos Inteligentes',
        description: 'Suporte automático para relacionamentos 1:1 e 1:N com interface rica.',
        icon: '🔗'
    },
    {
        title: 'Components Vue Dinâmicos',
        description: 'Interface interativa com modais, dropdowns e componentes TypeScript.',
        icon: '🎨'
    },
    {
        title: 'Soft Delete Integrado',
        description: 'Sistema completo de exclusão lógica com filtros automáticos.',
        icon: '🗑️'
    }
];

const stats = [
    { value: '30s', label: 'Para criar um CRUD completo' },
    { value: '100%', label: 'TypeScript e Vue 3' },
    { value: '0', label: 'Dependências extras' },
    { value: '∞', label: 'Possibilidades de customização' }
];

const testimonials = [
    { name: 'Carlos Silva', company: 'TechStart', text: 'Reduziu nosso tempo de desenvolvimento em 80%. Incrível!', rating: 5 },
    { name: 'Ana Costa', company: 'DevAgency', text: 'Interface intuitiva e código limpo. Recomendo!', rating: 5 },
    { name: 'João Santos', company: 'WebCorp', text: 'Melhor ferramenta para criar sistemas rapidamente.', rating: 5 }
];

const mockData = {
    customers: [
        { id: 1, name: 'Empresa Alpha', email: 'contato@alpha.com', status: 'Ativo', created: '15/11/2024' },
        { id: 2, name: 'Beta Solutions', email: 'info@beta.com', status: 'Pendente', created: '12/11/2024' },
        { id: 3, name: 'Gamma Tech', email: 'hello@gamma.com', status: 'Ativo', created: '10/11/2024' }
    ],
    stats: { total: 1247, active: 892, pending: 355, growth: '+12%' }
};
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="w-full max-w-7xl px-6 py-12">
                <!-- Hero Section -->
                <section class="text-center mb-20">
                    <h1 class="text-5xl lg:text-7xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">
                        LaraVue IziCrude
                    </h1>
                </section>

                <!-- Demo Section -->
                <section class="mb-20">

                    <!-- Mock Dashboard -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard - Sistema de Clientes</h3>
                            <div class="flex gap-3">
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ mockData.stats.total }}</div>
                                <div class="text-sm text-blue-800 dark:text-blue-300">Total de Clientes</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ mockData.stats.active }}</div>
                                <div class="text-sm text-green-800 dark:text-green-300">Clientes Ativos</div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ mockData.stats.pending }}</div>
                                <div class="text-sm text-yellow-800 dark:text-yellow-300">Pendentes</div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ mockData.stats.growth }}</div>
                                <div class="text-sm text-purple-800 dark:text-purple-300">Crescimento</div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Nome</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Criado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="customer in mockData.customers" :key="customer.id" class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">#{{ customer.id }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ customer.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ customer.email }}</td>
                                        <td class="px-4 py-3">
                                            <span :class="customer.status === 'Ativo' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300'"
                                                  class="px-2 py-1 text-xs rounded-full font-medium">{{ customer.status }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ customer.created }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>
</template>
