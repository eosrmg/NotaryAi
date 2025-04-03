<script setup lang="ts">
import Button from './ui/button.vue';
import Calendar from './ui/calendar.vue';
import Dialog from './ui/dialog.vue';
import Input from './ui/input.vue';
import Switch from './ui/switch.vue';

export { Button, Calendar, Dialog, Input, Switch };
</script>

<template>
  <div>
    <slot />
  </div>
</template>

<style scoped>
/* This file is intentionally left blank */
</style>