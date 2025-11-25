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
                        Gere Sistemas Completos
                        <span class="block text-orange-500 mt-2">em 30 Segundos</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                        Plataforma white-label que transforma ideias em aplicações profissionais.
                        <strong>Sem código complexo, sem dependências, sem limitações.</strong>
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="px-10 py-5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold text-lg rounded-xl transition-all duration-200 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1"
                        >
                            🚀 Acessar Plataforma
                        </Link>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="px-10 py-5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold text-lg rounded-xl transition-all duration-200 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1"
                            >
                                🚀 Teste Grátis por 14 Dias
                            </Link>
                            <Link
                                :href="login()"
                                class="px-10 py-5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-[#1b1b18] dark:text-[#EDEDEC] font-bold text-lg rounded-xl border-2 border-gray-200 dark:border-gray-600 transition-all duration-200"
                            >
                                Ver Demo
                            </Link>
                        </template>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-2">✅ Sem instalação</span>
                        <span class="flex items-center gap-2">⚡ Deploy automático</span>
                        <span class="flex items-center gap-2">🔒 100% seguro</span>
                        <span class="flex items-center gap-2">📱 Responsivo</span>
                    </div>
                </section>

                <!-- Demo Section -->
                <section class="mb-20">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                            Veja o Sistema em Ação
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-300">
                            Interface real gerada automaticamente em segundos
                        </p>
                    </div>

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

                    <div class="text-center">
                        <p class="text-lg text-gray-600 dark:text-gray-300 mb-4">
                            ⬆️ <strong>Esta interface foi gerada automaticamente</strong> a partir do comando:
                        </p>
                        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm max-w-2xl mx-auto">
                            php artisan make:crud Cliente nome:string email:email status:string
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="mb-20">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                            Tudo Incluído, Nada a Mais
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="feature in features" :key="feature.title"
                             class="p-6 rounded-xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                            <div class="text-4xl mb-4">{{ feature.icon }}</div>
                            <h3 class="font-bold text-lg text-[#1b1b18] dark:text-[#EDEDEC] mb-3">{{ feature.title }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ feature.description }}</p>
                        </div>
                    </div>
                </section>

                <!-- Testimonials Section -->
                <section class="mb-20">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                            Clientes Satisfeitos
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="testimonial in testimonials" :key="testimonial.name"
                             class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex mb-4">
                                <span v-for="i in testimonial.rating" :key="i" class="text-yellow-400 text-xl">⭐</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 italic">"{{ testimonial.text }}"</p>
                            <div>
                                <div class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ testimonial.name }}</div>
                                <div class="text-sm text-gray-500">{{ testimonial.company }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Final -->
                <section class="text-center bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-12 text-white">
                    <h2 class="text-4xl font-bold mb-6">
                        Pronto para Revolucionar seu Desenvolvimento?
                    </h2>
                    <p class="text-xl mb-8 opacity-90">
                        Junte-se a centenas de desenvolvedores que já economizam horas todos os dias.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="px-10 py-5 bg-white text-orange-500 font-bold text-lg rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:bg-gray-50"
                        >
                            🎯 Começar Teste Grátis
                        </Link>
                        <Link
                            :href="$page.props.auth.user ? dashboard() : login()"
                            class="px-10 py-5 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl transition-all duration-200 hover:bg-white hover:text-orange-500"
                        >
                            {{ $page.props.auth.user ? '🚀 Ir para Dashboard' : '👀 Ver Demonstração' }}
                        </Link>
                    </div>
                </section>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
