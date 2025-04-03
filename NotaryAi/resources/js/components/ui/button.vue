<template>
    <button :class="buttonClass" @click="handleClick">
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
import { defineProps, computed } from 'vue';

const props = defineProps<{
    variant?: string;
    className?: string;
    onClick?: () => void;
}>();

const buttonClass = computed(() => {
    return [
        'px-4 py-2 rounded',
        props.variant === 'outline' ? 'border border-gray-300' : 'bg-blue-500 text-white',
        props.className,
    ].join(' ');
});

const handleClick = () => {
    if (props.onClick) {
        props.onClick();
    }
};
</script>

<style scoped>
button {
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

button:hover {
    background-color: #0056b3;
}
</style>