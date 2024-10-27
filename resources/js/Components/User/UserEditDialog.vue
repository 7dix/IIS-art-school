<!-- EditUserDialog.vue -->
<template>
    <div v-if="isOpen" class="dialog-overlay">
    <div class="dialog-content">
      <h2>Edit User</h2>
    <hr />
      <form @submit.prevent="save">
        <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" v-model="editableUser.first_name" class="form-control" />
        </div>
        <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" v-model="editableUser.last_name" class="form-control" />
        </div>
        <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" v-model="editableUser.email" class="form-control" />
        </div>
        <div class="form-group">
        <label for="role">Role:</label>
        <select v-model="editableUser.role_id" class="form-control">
            <option v-for="role in props.roles.data" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>   
        </div>

  
        <div class="button-group">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" @click="close" class="btn btn-secondary">Cancel</button>
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
    
    editableUser.value.role = props.roles.data.find((role) => role.id === editableUser.value.role_id);

    console.log(editableUser.value.role.id +"=="+ editableUser.value.role_id);
    emit('save', editableUser.value);
  };
  
  const close = () => {
    emit('close');
  };

//Close dialog when pressing the escape key
const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        close();
    }
};


    onMounted(() => {
        if (props.isOpen) {
            document.addEventListener('keydown', handleKeydown);
            editableUser.value.role_id = editableUser.value.role.id;
        }
    });
    

    onUnmounted(() => {
        document.removeEventListener('keydown', handleKeydown);
    });
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
    align-items: center;
    justify-content: center;
  }
  
  .dialog-content {
    background: #fff;
    padding: 15px;
    margin: 5px;
    border-radius: 5px;
    width: 450px;
  }
  .form-group {
    margin-bottom: 15px;
  }

  .form-group label {
    display: block;
    margin-bottom: 5px;
  }

  .form-group input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
  }
  .form-group {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}
.dialog-content h2 {
    margin-top: 0;
    margin-bottom: 0px;
    font-size: 18px;
    text-align: left;
    color: #333;
    padding: 0px;
}
.dialog-content hr {
    margin: 0px;
    padding: 0px;
    margin-bottom: 20px;
}
.form-group label {
    flex: 1;
    margin-right: 10px;
}

.form-group input {
    flex: 2;
    padding: 8px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.button-group {
    display: flex;
    justify-content: space-between;
}

.button-group .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.button-group .btn-primary {
    background-color: #007bff;
    color: white;
}

.button-group .btn-secondary {
    background-color: #6c757d;
    color: white;
}

.button-group .btn-primary:hover {
    background-color: #0056b3;
}

.button-group .btn-secondary:hover {
    background-color: #5a6268;
}

.form-group select {
    flex: 2;
    padding: 8px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

  </style>
  