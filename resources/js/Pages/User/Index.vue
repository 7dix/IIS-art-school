<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { VTColumn } from "@/types";
import Table from '@/Components/Table.vue';
import { h, ref } from "vue";
import { Button } from '@/Components/ui/button'
import EditUserDialog from '@/Components/User/UserEditDialog.vue';
import axios from 'axios';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Object,
        required: true,
    },

})


const showDialog = ref(false);
const selectedUser = ref(null);

const openEditDialog = (user) => {
  selectedUser.value = { ...user };  // Clone user data to edit
  showDialog.value = true;
};

const saveUser = async (updatedUser) => {
  console.log(updatedUser.id);
  const response = await axios.put(`/api/user/${updatedUser.id}`, updatedUser);
  if (response.status === 200) {
    for (let i = 0; i < props.users.data.length; i++) {
        if (props.users.data[i].id === updatedUser.id) {
        props.users.data[i] = updatedUser;
        props.users.data[i];
        break;
        }
    }
    } else {
        console.error("Failed to update type:", response);
    }
 
  showDialog.value = false;
};

const closeDialog = () => {
  showDialog.value = false;
};


const columns: VTColumn[] = [
    {
        "key": "full_name",
        "header": "Full Name",
        renderAs: (item) => {
            return h('span', item.first_name + ' ' + item.last_name)
        }
    },
    {
        "key": "email",
        "header": "Email",
    },
    {
        "key": "role",
        "header": "Role",
        renderAs: (item) => {
            return h('span', item.role)
        }
    },
    {
        "key": "role_id",
        "header": "Role id",
        renderAs: (item) => {
            return h('span', item.role_id)
        }
    },
    {
        "key": "actions",
        "header": "Actions",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => openEditDialog(item),
                    class: 'bg-blue-500 text-white'
                },
                'Edit'
            );
        },
    }
]

</script>

<template>
    <Head title="User" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Users
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <Table :data="users.data" :columns="columns" icon="solar:case-minimalistic-outline" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>


     <!-- Edit Dialog -->
     <EditUserDialog 
          v-if="showDialog" 
          :user="selectedUser" 
          :isOpen="showDialog" 
          :roles="props.roles"
          @save="saveUser" 
          @close="closeDialog"
        />

</template>
