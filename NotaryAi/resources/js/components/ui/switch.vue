<template>
  <div class="switch" @click="toggleSwitch">
    <div class="switch-handle" :class="{ 'on': isChecked }"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['update:modelValue']);

const isChecked = ref(props.modelValue);

const toggleSwitch = () => {
  isChecked.value = !isChecked.value;
  emit('update:modelValue', isChecked.value);
};

watch(() => props.modelValue, (newValue) => {
  isChecked.value = newValue;
});
</script>

<style scoped>
.switch {
  display: inline-block;
  width: 50px;
  height: 24px;
  background-color: #ccc;
  border-radius: 12px;
  position: relative;
  cursor: pointer;
  transition: background-color 0.3s;
}

.switch-handle {
  width: 22px;
  height: 22px;
  background-color: white;
  border-radius: 50%;
  position: absolute;
  top: 1px;
  left: 1px;
  transition: transform 0.3s;
}

.switch-handle.on {
  transform: translateX(26px);
}

.switch.on {
  background-color: #4caf50;
}
</style>