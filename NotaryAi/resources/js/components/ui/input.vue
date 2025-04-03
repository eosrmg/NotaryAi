<template>
    <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :class="['input', { 'input-error': error }]"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps<{
    modelValue: string;
    type?: string;
    placeholder?: string;
    error?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<style scoped>
.input {
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 4px;
    width: 100%;
}

.input-error {
    border-color: red;
}
</style>