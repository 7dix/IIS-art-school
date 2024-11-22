<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-semibold mb-4">Edit Type</h2>
        <hr class="mb-4" />
        <form @submit.prevent="save">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">*Name:</label>
            <input type="text" v-model="editableType.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            <div v-if="errors.name" class="mt-2 text-sm text-red-600"> {{ errors.name }} </div>    
          </div>
        
          <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save</button>
            <button type="button" @click="close" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import axios from 'axios';
  import { defineProps, defineEmits, ref, watch, onMounted, onUnmounted } from 'vue';
  
  const props = defineProps<{
    type: any,
    errors: any,
    isOpen: boolean,
  }>();
  const emit = defineEmits(['save', 'close']);
  const editableType = ref({ ...props.type });
  
  watch(
    () => props.type,
    (newType) => {
      editableType.value = { ...newType };
    }
  );
  const save = () => {
    
    emit('save', editableType.value);
  };
  
  const close = () => {
    emit('close');
  };
  
  // Close dialog when pressing the escape key
  const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
      close();
    }
  };
  
  onMounted(() => {
    if (props.isOpen) {
      document.addEventListener('keydown', handleKeydown);
    }
  });
  
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
  });
  </script>