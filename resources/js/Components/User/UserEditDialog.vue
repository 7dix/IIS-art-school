<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
      <h2 class="text-lg font-semibold mb-4">Edit User</h2>
      <hr class="mb-4" />
      <form @submit.prevent="save">
        <div class="mb-4">
          <label for="first_name" class="block text-sm font-medium text-gray-700">First Name:</label>
          <input type="text" v-model="editableUser.first_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
          <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
          <input type="text" v-model="editableUser.last_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
          <input type="email" v-model="editableUser.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
          <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
          <select v-model="editableUser.role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option v-for="role in props.roles.data" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
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
  user: any,
  isOpen: boolean,
  roles: any[] | any,
}>();
const emit = defineEmits(['save', 'close']);
const editableUser = ref({ ...props.user });

watch(
  () => props.user,
  (newUser) => {
    editableUser.value = { ...newUser };
  }
);
const save = () => {
  const role = props.roles.data.find((role) => role.id === editableUser.value.role_id);
  editableUser.value.role = role.name;
  emit('save', editableUser.value);
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