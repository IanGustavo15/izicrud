<script setup lang="ts">
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';


const props = defineProps<{
    item?: { nome: string; email: string; cpf: string; telefone: string; foto: string; [key: string]: any };
    isEditing?: boolean;
    processing?: boolean;
    
}>();

const emit = defineEmits<{
    submit: [formData: FormData];
    alert: [message: string, variant?: 'success' | 'warning' | 'destructive'];
}>();

// Estados locais para gerenciamento de arquivos
const filesToRemove = ref<{field: string, index?: number}[]>([]);
const currentFiles = ref<{[key: string]: any}>({});

// Form refs
const nomeRef = ref(props.item?.nome?.toString() || '');
const emailRef = ref(props.item?.email?.toString() || '');
const cpfRef = ref(props.item?.cpf?.toString() || '');
const telefoneRef = ref(props.item?.telefone?.toString() || '');
const fotoRef = ref(null as File | null);

// Inicializar arquivos atuais quando editando
if (props.isEditing && props.item) {
    Object.keys(props.item).forEach((key: keyof typeof props.item) => {
        const value = props.item![key];
        if (typeof value === 'string' && value.includes('uploads/') || Array.isArray(value)) {
            currentFiles.value[key] = Array.isArray(value) ? [...value] : value;
        }
    });
}

// Funções para gerenciamento de arquivos
function downloadFile(filePath: string) {
    const link = document.createElement('a');
    link.href = `/storage/${filePath}`;
    link.download = getFileName(filePath);
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function removeFileLocally(fieldName: string, index?: number) {
    if (!confirm('Tem certeza de que deseja remover este arquivo?')) {
        return;
    }

    filesToRemove.value.push({ field: fieldName, index });

    if (index !== undefined) {
        if (Array.isArray(currentFiles.value[fieldName])) {
            currentFiles.value[fieldName].splice(index, 1);
        }
    } else {
        currentFiles.value[fieldName] = null;
    }

    emit('alert', 'Arquivo marcado para remoção. Salve o formulário para confirmar.', 'warning');
}

function getFileName(filePath: string): string {
    return filePath.split('/').pop() || filePath;
}

function submitForm() {
    const formData = new FormData();

    formData.append('nome', nomeRef.value);

    formData.append('email', emailRef.value);

    formData.append('cpf', cpfRef.value);

    formData.append('telefone', telefoneRef.value);

    // Arquivo
    if (fotoRef.value) {
        formData.append('foto', fotoRef.value);
    }

    // Arquivos para remover
    if (filesToRemove.value.length > 0) {
        formData.append('filesToRemove', JSON.stringify(filesToRemove.value));
    }

    // Se for edição, adicionar _method
    if (props.isEditing) {
        formData.append('_method', 'PUT');
    }

    emit('submit', formData);
}

function handleFotoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    fotoRef.value = file;
}
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div>
            <Label for="nome">Nome</Label>
            <Input id="nome" v-model="nomeRef" type="text" placeholder="Digite Nome" />
        </div>

        <div>
            <Label for="email">E-mail</Label>
            <Input id="email" v-model="emailRef" type="email" placeholder="Digite E-mail" />
        </div>

        <div>
            <Label for="cpf">CPF</Label>
            <Input id="cpf" v-model="cpfRef" type="text" placeholder="Digite CPF" />
        </div>

        <div>
            <Label for="telefone">Telefone</Label>
            <Input id="telefone" v-model="telefoneRef" type="text" placeholder="Digite Telefone" />
        </div>

        <div>
            <Label for="foto">Foto</Label>

            <!-- Arquivo existente -->
            <div v-if="isEditing && currentFiles.foto" class="mb-3 p-3 border rounded-lg bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ getFileName(currentFiles.foto) }}</span>
                    </div>
                    <div class="flex space-x-2">
                        <Button @click.prevent="downloadFile(currentFiles.foto)" type="button" variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download
                        </Button>
                        <Button @click="removeFileLocally('foto')" type="button" variant="destructive" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remover
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Upload de novo arquivo -->
            <Input id="foto" @change="handleFotoChange" type="file" />
            <p class="text-xs text-gray-500 mt-1">{{ isEditing ? 'Selecione um novo arquivo para substituir o atual' : 'Selecione um arquivo' }}</p>
        </div>

        <Button type="submit" class="my-4" :disabled="processing">
            {{ isEditing ? 'Atualizar Cliente' : 'Criar Cliente' }}
        </Button>
    </form>
</template>
