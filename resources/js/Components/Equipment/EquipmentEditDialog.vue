<template>
<div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
    <h2 class="text-lg font-semibold mb-4">Edit equipment</h2>
    <hr class="mb-4" />
    <form @submit.prevent="save">
        <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
        <input type="text" v-model="editableEquipment.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
        </div>
        <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Atelier</label>
        <select v-model="editableEquipment.atelier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option v-for="atelier in ateliers.data" :key="atelier.id" :value="atelier.id">{{ atelier.name }}</option>
            <option v-if="editableEquipment.type_id == ''"> -- Select Type of equipment first -- </option>
        </select>
        </div>
        <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Maximum leasing period:</label>
        <input type="text" v-model="editableEquipment.maximum_leasing_period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Year of manufacture:</label>
        <input type="text" v-model="editableEquipment.year_of_manufacture" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Date of purchase:</label>
        <input type="date" v-model="editableEquipment.date_of_purchase" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
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
    equipment: any,
    isOpen: boolean,
    types: any[] | any,
    }>();
    const emit = defineEmits(['save', 'close']);
    const editableEquipment = ref({ ...props.equipment });
    let ateliers = ref<{ id: number; name: string }[]>([]);

    let ateliers_: any[] | any;

    watch(
    () => props.equipment,
    (newEquipment) => {
        editableEquipment.value = { ...newEquipment };
    }
    );

    watch(
    () => editableEquipment.value.type_id,
    (newValue) => {
        getAteliers(newValue);
        editableEquipment.value.atelier_id = '';
    }
    );

    


    const getAteliers = (type_id) => {
    axios.get(`/api/getAteliersWithType/${type_id}`)
    .then(response => {
        ateliers.value = response.data;
        ateliers_ = response.data.data;
    })
    .catch(error => {
        ateliers.value = [];
        console.log(error);
    });

    }


    const save = () => {
        console.log(ateliers_);

        editableEquipment.value.atelier_name = ateliers_.find((atelier) => atelier.id === editableEquipment.value.atelier_id).name;
        editableEquipment.value.type_name = props.types.data.find((type) => type.id === editableEquipment.value.type_id).name;
        emit('save', editableEquipment.value);
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
        getAteliers(editableEquipment.value.type_id);
    }
    });

    onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    });
</script>