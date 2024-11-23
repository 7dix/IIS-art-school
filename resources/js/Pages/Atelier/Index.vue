<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Table from "@/Components/Table.vue";
import { Head, Link } from "@inertiajs/vue3";
import { VTColumn } from "@/types";
import { h, ref, toRaw, reactive } from "vue";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import EditAtelierDialog from '@/Components/Atelier/AtelierEditDialog.vue';
import DeleteDialog from "@/Components/DeleteDialog.vue";
import axios from 'axios';


const props = defineProps({
    ateliers: {
        type: Object,
        required: true,
    },
    types: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    role: {
        type: String,
        required: true,
    }
});


////////////////////
////EDIT dialog/////
////////////////////
const showDialog = ref(false);
const selectedAtelier = ref(null);
const errors = ref({});


const openEditDialog = (atelier) => {
    selectedAtelier.value = { ...atelier };  // Clone atelier data to edit
    showDialog.value = true;
};

const saveAtelier = async (updatedAtelier) => {
    errors.value = {};
    updatedAtelier = JSON.parse(JSON.stringify(updatedAtelier));

    try {
        await axios.put(`/api/atelier/${updatedAtelier.id}`, updatedAtelier);
            for (let i = 0; i < props.ateliers.data.length; i++) {
                if (props.ateliers.data[i].id === updatedAtelier.id) {
                props.ateliers.data[i] = updatedAtelier;
                props.ateliers.data[i].manager = props.users.data.find(user => user.id === updatedAtelier.manager_id);

                break;
                }
            }
        } catch (error) {
            errors.value = error.response.data.errors;
            return;
        }  
    
    showDialog.value = false;
};

const closeDialog = () => {
    showDialog.value = false;
};

////////////////////
////DELETE DIALOG/////
////////////////////
const deleteDialogRef = ref(null);

const openDeleteDialog = (item) => {
    selectedAtelier.value = item;
    if (deleteDialogRef.value) {
        deleteDialogRef.value.openDialog(item.id);
    }
};

const confirmDelete = async (id: number) => {
    if (id) {
        const response = await axios.delete(route("atelier.destroy", id));
        if (response.status === 200) {
            props.ateliers.data.splice(
                props.ateliers.data.findIndex((item) => item.id === id),
                1
            );
        } else {
            console.error(response);
        }
    }
    return { confirmDelete };
};



////////////////////
////TABLE/////
////////////////////
const columns: VTColumn[] = [
    {
        key: "name",
        header: "Name",
    },
    {
        key: "room",
        header: "Room",
    },
    {
        key: "manager",
        header: "Manager",
        renderAs: (item) => {
            return h(
                "span",
                item.manager?.name
            );
        },
    },
    {
        key: "ateliers",
        header: "Users",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                },
                item.users.length
            );
        },
    },
    {
        key: "equipments",
        header: "Equipments",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                },
                item.equipments.length
            );
        },
    },
    {
        key: "actions",
        header: "Actions",
        renderAs: (item) => {
            return h("div", {
                class: "flex items-center",
            }, [
                h(
                Link,
                {
                    href: `/atelier/${item.id}/dashboard`,
                    class: "bg-blue-500 text-white hover:bg-blue-700 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md",
                },
                h(Icon, {
                    icon: "majesticons:open",
                    class: "w-5 h-5",
                })
                ),
                props.role == 'admin' && h(
                        Button,
                        {
                            onClick: () => openEditDialog(item),
                            class: 'bg-blue-500 text-white ml-2 py-4 px-4 rounded-md hover:bg-blue-600 ',
                        },
                        'Edit'
                    ),
                props.role == 'admin' && h(
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
                
                ]
            );
        },
    },
];
</script>
<template>
    <Head title="Atelier" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Ateliers
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div
                        v-if="props.role == 'admin'"
                        class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6"
                    >
                        <Link
                            :href="route('atelier.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Create Atelier
                        </Link>
                    </div>
                    <div class="p-6 text-gray-900">
                        <Table
                            :data="ateliers.data"
                            :columns="columns"
                            icon="solar:case-minimalistic-outline"
                        />
                    </div>
                </div>
            </div>
        </div>
         <!-- Edit Dialog -->
     <EditAtelierDialog 
          v-if="showDialog" 
          :atelier="selectedAtelier" 
          :isOpen="showDialog" 
          :types="props.types"
          :users="props.users"
          :errors="errors"
          @save="saveAtelier" 
          @close="closeDialog"
        />
        <DeleteDialog ref="deleteDialogRef" :onConfirm="confirmDelete" />

    </AuthenticatedLayout>
</template>
