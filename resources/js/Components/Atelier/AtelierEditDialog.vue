<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Input } from '@/Components/ui/input';
import { Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
 } from '@/Components/ui/select';
  
  const props = defineProps<{
    atelier: any,
    managers: any[] | any,
    errors: any,
    isOpen: boolean;
    types: { data: Array<{ id: number; name: string }> };
    users: { data: Array<{ id: number; name: string; }> };
  }>();
  
  const emit = defineEmits(['save', 'close']);
  
  const editableAtelier = ref({...props.atelier});
  
  watch(
    () => props.atelier,
    (newAtelier) => {
      editableAtelier.value = {...newAtelier};
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
        document.addEventListener('keydown', handleKeydown);
    }
  });
  
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
  });
</script>  
<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-semibold mb-4">Edit atelier</h2>
        <hr class="mb-4" />
        <form @submit.prevent="save">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">*Name</label>
            <Input type="text" id="name" v-model="editableAtelier.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
            <div v-if="errors.name" class="mt-2 text-sm text-red-600"> {{ errors.name }} </div>
          </div>
          <div class="mb-4">
            <label for="room" class="block text-sm font-medium text-gray-700">*Room:</label>
            <Input type="text" id="room" v-model="editableAtelier.room" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
            <div class="text-gray-600 text-xs">Correct Format: Capital letter followed by exactly 3 numbers</div>
            <div v-if="errors.room" class="mt-2 text-sm text-red-600"> {{ errors.room }} </div>
          </div>
          <div class="mb-4">
            <label for="manager" class="block text-sm font-medium text-gray-700">*Manager:</label>
            <Select v-model="editableAtelier.manager_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
              <SelectTrigger>
                  <SelectValue placeholder="Select a manager" />
              </SelectTrigger>
              <SelectContent>
                  <SelectItem v-for="user in props.users.data" :key="user.id" :value="user.id">
                      {{ user.name }}
                  </SelectItem>
              </SelectContent>
          </Select> 
            <div v-if="errors.manager_id" class="mt-2 text-sm text-red-600"> {{ errors.manager_id }} </div>

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
  
  