<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-semibold mb-4">Edit atelier</h2>
        <hr class="mb-4" />
        <form @submit.prevent="save">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" id="name" v-model="editableAtelier.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
          </div>
          <div class="mb-4">
            <label for="room" class="block text-sm font-medium text-gray-700">Room:</label>
            <input type="text" id="room" v-model="editableAtelier.room" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
          </div>
          <div class="mb-4">
            <label for="manager" class="block text-sm font-medium text-gray-700">Manager:</label>
            <select id="manager" v-model="editableAtelier.manager_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              <option v-for="user in props.managers.data" :key="user.id" :value="user.id">
                {{ user.first_name + " " + user.last_name }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label for="types" class="block text-sm font-medium text-gray-700">Types:</label>
            <select id="types" v-model="editableAtelier.types" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              <option v-for="type in props.types.data" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
          </div>
          <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
              Save
            </button>
            <button type="button" @click="close" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, watch, onMounted, onUnmounted } from 'vue';
  
  const props = defineProps<{
    atelier:{
      id: number;
      name: string;
      room: string;
      manager_id: number;
      types: Array<{ id: number; name: string }>;
    };
    isOpen: boolean;
    types: { data: Array<{ id: number; name: string }> };
    managers: { data: Array<{ id: number; first_name: string; last_name: string }> };
  }>();
  
  const emit = defineEmits(['save', 'close']);
  
  const editableAtelier = ref({
    ...props.atelier,
    types: props.atelier.types.map((type) => type.id), // Převedení typů na pole ID
  });
  
  watch(
    () => props.atelier,
    (newAtelier) => {
      editableAtelier.value = {
        ...newAtelier,
        types: newAtelier.types.map((type) => type.id), // Udržení správného formátu
      };
    }
  );
  
  const save = () => {
    emit('save', editableAtelier.value);
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
        console.log(props.atelier.types);
      document.addEventListener('keydown', handleKeydown);
    }
  });
  
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
  });
  </script>
  