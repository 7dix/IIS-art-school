<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { VTColumn } from "@/types";
import Table from "@/Components/Table.vue";
import { h, ref } from "vue";
import { Button } from "@/Components/ui/button";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import { Icon } from "@iconify/vue";
import EditTypeDialog from '@/Components/Types/TypeEditDialog.vue';
import axios from "axios";

const props = defineProps({
    types: {
        type: Object,
        required: true,
    },
});


////////////////////
////DELETE DIALOG/////
////////////////////
const deleteDialogRef = ref(null);
const selectedType = ref(null);

const openDeleteDialog = (item) => {
    selectedType.value = item;
    if (deleteDialogRef.value) {
        deleteDialogRef.value.openDialog();
    }
};

const confirmDelete = async (type) => {
    console.log(type);
    if (type) {
        try {
            const response = await axios.delete(`/types/${type.id}`);
            window.location.reload();
        } catch (error) {
            console.error("Failed to delete type:", error);
        }
    }
};

const cancelDelete = () => {
    // Handle cancel action if needed
};


////////////////////
////EDIT DIALOG/////
////////////////////
const showDialog = ref(false);

const openEditDialog = (type) => {
  selectedType.value = { ...type };  // Clone user data to edit
  showDialog.value = true;
};

const saveUser = async (updatedType) => {
  console.log(updatedType.id);
    const response = await axios.put(`/api/type/${updatedType.id}`, updatedType);
    if (response.status === 200) {
        const updatedTypeData = response.data;
        for (let i = 0; i < props.types.data.length; i++) {
            if (props.types.data[i].id === updatedTypeData.id) {
                props.types.data[i] = updatedTypeData;
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
////TABLE/////
////////////////////
const columns = [
    {
        key: "name",
        header: "Name",
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
        key: "ateliers",
        header: "Ateliers",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                },
                item.ateliers.length,
            );
            
        },
    },
    {
        key: "actions",
        header: "Actions",
        renderAs: (item) => {
            return h("div", {}, [
                h(
                    Button,
                    {
                        onClick: () => openDeleteDialog(item),
                        class: "bg-red-500 text-white hover:bg-red-700",
                    },
                    h(Icon, {
                        icon: "material-symbols:delete",
                        class: "w-5 h-5",
                    })
                ),
                h(
                    Button,
                    {
                        onClick: () => openEditDialog(item),
                        class: 'bg-blue-500 text-white ml-2'
                    },
                    'Edit'
                ),
            ]);
        },
    },
];
</script>

<template>
    <Head title="Types" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Types
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div
                        class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6"
                    >
                        <Link
                            :href="route('types.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Create Type
                        </Link>
                    </div>
                    <div class="p-6 text-gray-900">
                        <Table
                            :data="types.data"
                            :columns="columns"
                            icon="solar:case-minimalistic-outline"
                        />
                    </div>
                </div>
            </div>
        </div>
        <DeleteDialog
            ref="deleteDialogRef"
            :type="selectedType"
            :onConfirm="confirmDelete"
            :onCancel="cancelDelete"
        />
    </AuthenticatedLayout>


     <!-- Edit Dialog -->
     <EditTypeDialog 
          v-if="showDialog" 
          :type="selectedType" 
          :isOpen="showDialog" 
          @save="saveUser" 
          @close="closeDialog"
        />


</template>
