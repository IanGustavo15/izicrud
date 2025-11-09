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
    { title: 'Resultados', href: '/resultado' },
];

const headerTitle = 'Resultados';
const headerDescription = 'Gerencie seus resultados aqui.';

const props = defineProps<{
    item?: { id: number; id_inscricao: number; pontuacao_total: number; acertos: number; erros: number; tempo_total_minutos: number; percentual_acerto: number };
    sidebarNavItems: { title: string; href: string }[];
    id_inscricaoOptions: { value: number; label: string }[];
    
    
    
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_inscricao: props.item?.id_inscricao.toString() || 0,
    pontuacao_total: props.item?.pontuacao_total.toString() || 0,
    acertos: props.item?.acertos.toString() || 0,
    erros: props.item?.erros.toString() || 0,
    tempo_total_minutos: props.item?.tempo_total_minutos.toString() || 0,
    percentual_acerto: props.item?.percentual_acerto.toString() || 0
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
        form.put(`/resultado/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Resultado atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o resultado: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/resultado', {
            onSuccess: () => {
                showAlert('Resultado criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o resultado: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Resultado' : 'Criar Resultado'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Resultado' : 'Criar Novo Resultado'" description="Gerencie os detalhes do resultado" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Resultado' : 'Criar Novo Resultado' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
    <div>
                    <Label for="id_inscricao">Inscricao</Label>
                    <Select v-model="form.id_inscricao">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Inscricao" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_inscricaoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                <div>
                    <Label for="pontuacao_total">Pontuacao total</Label>
                    <Input id="pontuacao_total" v-model.number="form.pontuacao_total" type="number" step="1" placeholder="Digite Pontuacao total" />
                </div>
                <div>
                    <Label for="acertos">Acertos</Label>
                    <Input id="acertos" v-model.number="form.acertos" type="number" step="1" placeholder="Digite Acertos" />
                </div>
                <div>
                    <Label for="erros">Erros</Label>
                    <Input id="erros" v-model.number="form.erros" type="number" step="1" placeholder="Digite Erros" />
                </div>
                <div>
                    <Label for="tempo_total_minutos">Tempo total em Minutos</Label>
                    <Input id="tempo_total_minutos" v-model.number="form.tempo_total_minutos" type="number" step="1" placeholder="Digite Tempo total em Minutos" />
                </div>
                <div>
                    <Label for="percentual_acerto">Percentual de Acerto</Label>
                    <Input id="percentual_acerto" v-model.number="form.percentual_acerto" type="number" step="0.01" placeholder="Digite Percentual de Acerto" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Resultado' : 'Criar Resultado' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
