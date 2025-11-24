<script setup lang="ts">
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { X } from 'lucide-vue-next';

interface Props {
    modelValue: number[];
    availableItems: { value: number; label: string }[];
    label: string;
    relatedModel: string;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Selecione itens...'
});

const emit = defineEmits<{
    'update:modelValue': [value: number[]]
}>();

const selectedValue = ref<string>('');

const selectedItems = computed(() => {
    return props.availableItems.filter(item => props.modelValue.includes(item.value));
});

const availableForSelection = computed(() => {
    return props.availableItems.filter(item => !props.modelValue.includes(item.value));
});

function addItem() {
    if (selectedValue.value) {
        const newValue = parseInt(selectedValue.value);
        emit('update:modelValue', [...props.modelValue, newValue]);
        selectedValue.value = '';
    }
}

function removeItem(itemValue: number) {
    emit('update:modelValue', props.modelValue.filter(id => id !== itemValue));
}
</script>

<template>
    <div class="space-y-3">
        <Label>{{ label }}</Label>

        <!-- Lista de itens selecionados -->
        <div v-if="selectedItems.length > 0" class="flex flex-wrap gap-2">
            <Badge v-for="item in selectedItems" :key="item.value" variant="secondary" class="flex items-center gap-1">
                {{ item.label }}
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-4 w-4 p-0 hover:bg-destructive hover:text-destructive-foreground"
                    @click="removeItem(item.value)"
                >
                    <X class="h-3 w-3" />
                </Button>
            </Badge>
        </div>

        <!-- Select para adicionar novos itens -->
        <div class="flex gap-2">
            <div class="flex-1">
                <Select v-model="selectedValue">
                    <SelectTrigger>
                        <SelectValue :placeholder="placeholder" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="item in availableForSelection"
                            :key="item.value"
                            :value="item.value.toString()"
                        >
                            {{ item.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Button @click="addItem" :disabled="!selectedValue" variant="outline">
                Adicionar
            </Button>
        </div>
    </div>
</template>
