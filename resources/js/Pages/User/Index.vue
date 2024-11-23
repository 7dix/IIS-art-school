<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { VTColumn } from "@/types";
import Table from '@/Components/Table.vue';
import { h, ref } from "vue";
import { Button } from '@/Components/ui/button'
import EditUserDialog from '@/Components/User/UserEditDialog.vue';
import DeleteDialog from "@/Components/User/UserDeleteDialog.vue";
import { Icon } from "@iconify/vue";
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

////////////////////
////EDIT dialog/////
////////////////////
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


////////////////////
////DELETE DIALOG/////
////////////////////
const showDeleteDialog = ref(false);
const errors = ref({});

const openDeleteDialog = (item) => {
    selectedUser.value = item;
    showDeleteDialog.value = true;
};

const confirmDelete = async (user_id) => {
    errors.value = {};
    try {
        await axios.post(`/api/userDelete/${user_id}`);
    
        props.users.data.splice(
            props.users.data.findIndex((item) => item.id === user_id),
            1
        );
        
        showDeleteDialog.value = false
    } catch (error) {
        errors.value = error.response.data;
        return;
    } 
  };

const closeDeleteDialog = () => {
    showDeleteDialog.value = false; 
    errors.value = {};
};


////////////////////
////TABLE/////
////////////////////
const columns: VTColumn[] = [
    {
        "key": "full_name",
        "header": "Full Name",
        renderAs: (item) => {
            return h('span', item.name)
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
            return h("div", {}, [ 
            h(
                Button,
                {
                    onClick: () => openEditDialog(item),
                    class: 'bg-blue-500 text-white'
                },
                'Edit'
            ),
            h(
                    Button,
                    {
                        onClick: () => {
                            openDeleteDialog(item);
                        },
                        class: "bg-red-500 text-white hover:bg-red-700 ml-2",

                    },
                    h(Icon, {
                        icon: "material-symbols:delete",
                        class: "w-5 h-5",
                    })
                ),
            ]);
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


        <DeleteDialog 
            v-if="showDeleteDialog" 
            :isOpen="showDeleteDialog"
            :user="selectedUser"
            :errors="errors" 
            @save="confirmDelete" 
            @close="closeDeleteDialog" 
            />
        <!-- Edit Dialog -->
        <EditUserDialog 
                v-if="showDialog" 
                :user="selectedUser" 
                :isOpen="showDialog" 
                :roles="props.roles"
                @save="saveUser" 
                @close="closeDialog"
                />

    </AuthenticatedLayout>
</template>
