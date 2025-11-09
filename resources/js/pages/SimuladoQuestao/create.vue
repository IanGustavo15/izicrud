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
    { title: 'SimuladoQuestaos', href: '/simuladoquestao' },
];

const headerTitle = 'SimuladoQuestaos';
const headerDescription = 'Gerencie seus simuladoquestaos aqui.';

const props = defineProps<{
    item?: { id: number; id_simulado: number; id_questao: number };
    sidebarNavItems: { title: string; href: string }[];
    id_simuladoOptions: { value: number; label: string }[];
    id_questaoOptions: { value: number; label: string }[];
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    id_simulado: props.item?.id_simulado.toString() || 0,
    id_questao: props.item?.id_questao.toString() || 0
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
        form.put(`/simuladoquestao/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('SimuladoQuestao atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o simuladoquestao: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/simuladoquestao', {
            onSuccess: () => {
                showAlert('SimuladoQuestao criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o simuladoquestao: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar SimuladoQuestao' : 'Criar SimuladoQuestao'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar SimuladoQuestao' : 'Criar Novo SimuladoQuestao'" description="Gerencie os detalhes do simuladoquestao" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar SimuladoQuestao' : 'Criar Novo SimuladoQuestao' }}</h2>
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
                    <Label for="id_questao">Questao</Label>
                    <Select v-model="form.id_questao">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecione um Questao" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="option in (props.id_questaoOptions || [])" :key="option.value" :value="option.value.toString()">
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
</div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar SimuladoQuestao' : 'Criar SimuladoQuestao' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
