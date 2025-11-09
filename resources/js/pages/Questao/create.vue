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
    { title: 'Questaos', href: '/questao' },
];

const headerTitle = 'Questaos';
const headerDescription = 'Gerencie seus questaos aqui.';

const props = defineProps<{
    item?: { id: number; texto_questao: string; area_concurso: string; diciplina: string; nivel_dificuldade: string; gabarito_correto: string };
    sidebarNavItems: { title: string; href: string }[];
    
    
    
    
    
}>();

const isEditing = ref(!!props.item);

const showAlertState = ref(false);
const alertMessage = ref('');
const alertVariant = ref<'success' | 'warning' | 'destructive'>('success');

const form = useForm({
    texto_questao: props.item?.texto_questao.toString() || '',
    area_concurso: props.item?.area_concurso.toString() || '',
    diciplina: props.item?.diciplina.toString() || '',
    nivel_dificuldade: props.item?.nivel_dificuldade.toString() || '',
    gabarito_correto: props.item?.gabarito_correto.toString() || ''
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
        form.put(`/questao/${props.item?.id}`, {
            onSuccess: () => {
                showAlert('Questao atualizado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao atualizar o questao: ${errorMessages}`, 'destructive');
            },
        });
    } else {
        form.post('/questao', {
            onSuccess: () => {
                showAlert('Questao criado com sucesso!', 'success');
                formErrors.value = {};
            },
            onError: (errors) => {
                formErrors.value = errors as unknown as Record<string, string[]>;
                const errorMessages = Object.values(formErrors.value).flat().join(', ');
                showAlert(`Erro ao criar o questao: ${errorMessages}`, 'destructive');
            },
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Editar Questao' : 'Criar Questao'" />

    <AppLayout :breadcrumbs="breadcrumbs" :headerTitle="headerTitle" :headerDescription="headerDescription" :sidebarNavItems="props.sidebarNavItems">
        <div class="space-y-6">
            <HeadingSmall :title="isEditing ? 'Editar Questao' : 'Criar Novo Questao'" description="Gerencie os detalhes do questao" />
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
                    <h2 class="text-lg font-semibold">{{ isEditing ? 'Editar Questao' : 'Criar Novo Questao' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                    <Label for="texto_questao">Texto da questao</Label>
                    <Textarea id="texto_questao" v-model="form.texto_questao" placeholder="Digite Texto da questao" rows="4" />
                </div>
                <div>
                    <Label for="area_concurso">Area Concurso</Label>
                    <Input id="area_concurso" v-model="form.area_concurso" type="text" placeholder="Digite Area Concurso" />
                </div>
                <div>
                    <Label for="diciplina">Diciplina</Label>
                    <Input id="diciplina" v-model="form.diciplina" type="text" placeholder="Digite Diciplina" />
                </div>
                <div>
                    <Label for="nivel_dificuldade">Nivel Dificuldade</Label>
                    <Input id="nivel_dificuldade" v-model="form.nivel_dificuldade" type="text" placeholder="Digite Nivel Dificuldade" />
                </div>
                <div>
                    <Label for="gabarito_correto">Gabarito</Label>
                    <Input id="gabarito_correto" v-model="form.gabarito_correto" type="text" placeholder="Digite Gabarito" />
                </div>
                        <Button type="submit" class="my-4" :disabled="form.processing">
                            {{ isEditing ? 'Atualizar Questao' : 'Criar Questao' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
