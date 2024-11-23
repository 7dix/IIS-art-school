<script setup lang="ts">
    import axios from 'axios';
    import { defineProps, defineEmits, ref, watch, onMounted, onUnmounted, toRaw } from 'vue';
    import { Input } from '@/Components/ui/input';
    import { Select,
        SelectContent,
        SelectGroup,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
    } from '@/Components/ui/select';
    import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
    } from '@/components/ui/number-field'
    import EquipmentSelectHours from '@/Components/Equipment/EquipmentSelectHours.vue';

    const props = defineProps<{
    equipment: any,
    isOpen: boolean,
    types: any[] | any,
    ateliers: any[] | any,
    errors: any,
    }>();
    const emit = defineEmits(['save', 'close']);
    const editableEquipment = ref({ ...props.equipment });


    watch(
    () => props.equipment,
    (newEquipment) => {
        editableEquipment.value = { ...newEquipment };
    }
    );
    


    const save = () => {
        editableEquipment.value.atelier_name = props.ateliers.data.find((atelier) => atelier.id === editableEquipment.value.atelier_id).name;
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
    }   
    });

    onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    });
</script>
<template>
<div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96 max-h-[90vh] overflow-y-auto">
    <h2 class="text-lg font-semibold mb-4">Edit equipment</h2>
    <hr class="mb-4" />
    <form @submit.prevent="save">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">*Name:</label>
            <Input type="text" v-model="editableEquipment.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            <div v-if="errors.name" class="mt-2 text-sm text-red-600"> {{ errors.name }} </div>
        </div>
        <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">*Type:</label>
            <Select v-model="editableEquipment.type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <SelectTrigger>
                    <SelectValue placeholder="Select a type" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="type in props.types.data" :key="type.id" :value="type.id">
                        {{ type.name }}
                    </SelectItem>
                </SelectContent>
            </Select> 
            <div v-if="errors.name" class="mt-2 text-sm text-red-600"> {{ errors.type_id }} </div>    

        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">*Atelier</label>
            <Select v-model="editableEquipment.atelier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <SelectTrigger>
                    <SelectValue placeholder="Select a atelier" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="atelier in props.ateliers.data" :key="atelier.id" :value="atelier.id">
                        {{ atelier.name }}
                    </SelectItem>
                </SelectContent>
            </Select> 
            <div v-if="errors.name" class="mt-2 text-sm text-red-600"> {{ errors.atelier_id }} </div>    

        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">*Maximum leasing period:</label>
            <Input type="text" v-model="editableEquipment.maximum_leasing_period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            <div v-if="errors.maximum_leasing_period" class="mt-2 text-sm text-red-600"> {{ errors.maximum_leasing_period }} </div>    
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">*Leasing hours:</label>
            <EquipmentSelectHours v-model="editableEquipment.allowed_leasing_hours" />
            <div class="text-gray-600 text-xs">Correct Format: hours from 8 - 18, separated by commas.</div>
            <div v-if="errors.allowed_leasing_hours" class="mt-2 text-sm text-red-600"> {{ errors.allowed_leasing_hours }} </div>    
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Year of manufacture:</label>
            <Input type="text" v-model="editableEquipment.year_of_manufacture" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            <div v-if="errors.year_of_manufacture" class="mt-2 text-sm text-red-600"> {{ errors.year_of_manufacture }} </div>    
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Date of purchase:</label>
            <Input type="date" v-model="editableEquipment.date_of_purchase" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            <div class="text-gray-600 text-xs">Correct Format: DayDay, MonthMonth, YearYearYearYear.</div>
            <div v-if="errors.date_of_purchase" class="mt-2 text-sm text-red-600"> {{ errors.date_of_purchase }} </div>    
        </div>
    
        <div class="flex justify-between">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save</button>
            <button type="button" @click="close" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Cancel</button>
        </div>
    </form>
    </div>
</div>
</template>
