<template>
  <div class="dialog-overlay" v-if="isOpen">
    <div class="dialog-content">
      <header class="dialog-header">
        <h2 class="dialog-title">{{ title }}</h2>
        <button class="dialog-close" @click="closeDialog">×</button>
      </header>
      <main class="dialog-body">
        <slot></slot>
      </main>
      <footer class="dialog-footer">
        <button class="dialog-button" @click="closeDialog">Cancel</button>
        <button class="dialog-button" @click="confirmDialog">Confirm</button>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineProps } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  isOpen: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['update:isOpen', 'confirm']);

const closeDialog = () => {
  emit('update:isOpen', false);
};

const confirmDialog = () => {
  emit('confirm');
  closeDialog();
};
</script>

<style scoped>
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.dialog-content {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 400px;
  max-width: 90%;
}

.dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
}

.dialog-title {
  margin: 0;
}

.dialog-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.dialog-body {
  padding: 16px;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  padding: 16px;
}

.dialog-button {
  margin-left: 8px;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>