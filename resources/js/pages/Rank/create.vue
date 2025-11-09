<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ref } from 'vue';
import { FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem, SelectGroup, SelectLabel } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Ranks', href: '/rank' },
];

const headerTitle = 'Ranks';
const headerDescription = 'Gerencie seus ranks aqui.';

const props = defineProps<{
    item?: { id: number; id_simulado: number; id_users: number; pontuacao_final: number; posicao_rank: number; classificacao: boolean };
    sidebarNavItems: { title: string; href: string }[];
    id_simuladoOptions: { value: number; label: string }[];
    id_usersOptions: { value: number; label: string }[];
    
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_simulado: props.item?.id_simulado.toString() || 0,
    id_users: props.item?.id_users.toString() || 0,
    pontuacao_final: props.item?.pontuacao_final.toString() || 0,
    posicao_rank: props.item?.posicao_rank.toString() || 0,
    classificacao: props.item?.classificacao.toString() || false
});

const formErrors = ref<Record<string, string[]>>({});
const descricaoMaxLength = 255;

function showAlert(message: string, variant: 'success' | 'warning' | 'destructive' = 'success'): void {
    alertMessage.value = message;
    alertVariant.value = variant;
    showAlertState.value = true;
    setTimeout(() => showAlertState.value = false, 3000);
}

function submitForm() {
    if (isEditing.value) {
        form.put(`/rank/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Rank atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o rank: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/rank', {
            onSuccess: () => {
                showAlert('Rank criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o rank: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Rank' : 'Criar Rank'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Rank' : 'Criar Novo Rank'" description="Gerencie os detalhes do rank" />
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Alert v-if="showAlertState" class="mb-4" :class="{
                'bg-green-100 border-green-500 text-green-900': alertVariant === 'success',
                'bg-yellow-100 border-yellow-500 text-yellow-900': alertVariant === 'warning',
                'bg-red-100 border-red-500 text-red-900': alertVariant === 'destructive',
            }">
                <AlertTitle>Ação Realizada</AlertTitle>
                <AlertDescription>{{ alertMessage }}</AlertDescription>
            </Alert>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex flex-col gap-4 p-4">
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Rank' : 'Criar Novo Rank' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_simulado">Simulado</Label>
                    <Select v-model="form.id_simulado">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Simulado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_simuladoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
    <div>
                    <Label for="id_users">Usuario</Label>
                    <Select v-model="form.id_users">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Usuario" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_usersOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="pontuacao_final">Pontuação Final</Label>
                    <Input id="pontuacao_final" v-model.number="form.pontuacao_final" type="number" step="1" placeholder="Digite Pontuação Final" />
                </div>
                <div>
                    <Label for="posicao_rank">Posição no Rank</Label>
                    <Input id="posicao_rank" v-model.number="form.posicao_rank" type="number" step="1" placeholder="Digite Posição no Rank" />
                </div>
                <div class="flex items-center space-x-2">
                    <Checkbox id="classificacao" v-model="form.classificacao" />
                    <Label for="classificacao">Classificação</Label>
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Rank' : 'Criar Rank' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
