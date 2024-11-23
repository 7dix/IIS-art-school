<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import { VTColumn } from "@/types";
import { h, ref, toRaw, reactive } from "vue";
import { Button } from '@/Components/ui/button'
import EditEquipmentDialog from '@/Components/Equipment/EquipmentEditDialog.vue';
import DeleteDialog from "@/Components/DeleteDialog.vue";
import { Icon } from "@iconify/vue";
import axios from 'axios';



const props = defineProps({
    equipments: {
        type: Object,
        required: true,
    },
    types: {
        type: Object,
        required: true,
    },
    ateliers : {
        type: Object,
        required: true,
    }
})


////////////////////
////DELETE DIALOG/////
////////////////////
const deleteDialogRef = ref(null);
const selectedEquipment = ref(null);

const openDeleteDialog = (item) => {
    selectedEquipment.value = item;
    if (deleteDialogRef.value) {
        deleteDialogRef.value.openDialog(item.id);
    }
};

const confirmDelete = async (id: number) => {
    if (id) {
        const response = await axios.delete(route("equipment.destroy", id));
        if (response.status === 200) {
            props.equipments.data.splice(
                props.equipments.data.findIndex((item) => item.id === id),
                1
            );
        } else {
            console.error(response);
        }
    }
    return { confirmDelete };
};





////////////////////
////EDIT dialog/////
////////////////////
const showDialog = ref(false);
const errors = ref({});


const openEditDialog = (equipment) => {
  selectedEquipment.value = { ...equipment };  // Clone equipment data to edit
  showDialog.value = true;
};

const saveEquipment = async (updatedEquipment) => {
  errors.value = {};  
  updatedEquipment = JSON.parse(JSON.stringify(updatedEquipment));

  try {
    await axios.put(`/api/equipment/${updatedEquipment.id}`, updatedEquipment);
    for (let i = 0; i < props.equipments.data.length; i++) {
        if (props.equipments.data[i].id === updatedEquipment.id) {
        props.equipments.data[i] = updatedEquipment;
        props.equipments.data[i].type_name = updatedEquipment.type_name;
        props.equipments.data[i].atelier_name = updatedEquipment.atelier_name;

        
        props.equipments.data[i].allowed_leasing_hours = updatedEquipment.allowed_leasing_hours.split(",").map(Number);
            
        props.equipments.data[i].allowed_leasing_hours = reactive(updatedEquipment.allowed_leasing_hours);

        break;
        }
    }
  }
  
    catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
            return {errors, updatedEquipment};
        }
    }
 
  showDialog.value = false;
};

const closeDialog = () => {
  showDialog.value = false;
};




////////////////////
////TABLE/////
////////////////////
const columns: VTColumn[] = [
    {
        "key": "name",
        "header": "Name",
    },
    {
        "key": "type",
        "header": "Type",
        renderAs: (item) => {
            return h('span', item.type_name)
        }
    },

    {
        "key": "owner",
        "header": "Owner",
        renderAs: (item) => {
            return h('span', item.owner.name)
        }
    },
    {
        "key": "atelier",
        "header": "Atelier",
        renderAs: (item) => {
            return h('span', item.atelier_name)
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
                    onClick: () => {
                        openEditDialog(item);
                    },
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
            ])
        },
    }
]


</script>

<template>
    <Head title="Dashboard" />  
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Equipment
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div
                        class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6">
                        <Link
                            :href="route('equipment.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Equipment
                        </Link>
                    </div>


                    <div class="p-6 text-gray-900">
                        <Table :data="equipments.data" :columns="columns" icon="solar:case-minimalistic-outline" />
                    </div>
                </div>
            </div>
        </div>


        <DeleteDialog ref="deleteDialogRef" :onConfirm="confirmDelete" />
       
        <EditEquipmentDialog 
          v-if="showDialog" 
          :equipment="selectedEquipment" 
          :isOpen="showDialog" 
          :types="props.types"
          :ateliers="props.ateliers"
          :errors="errors"
          @save="saveEquipment"
          @close="closeDialog" />


    </AuthenticatedLayout>


</template>
