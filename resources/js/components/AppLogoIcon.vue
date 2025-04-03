<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" :class="className" v-bind="$attrs">
        <path
            fill="currentColor"
            d="M24 2l6.93 14.05L46 18.26l-11 10.73 2.6 15.18L24 36.92l-13.6 7.25L13 29 2 18.26l15.07-2.21L24 2z"
        />
    </svg>
</template>

