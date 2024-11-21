<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    types: {
        type: Array,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

const form = useForm({
    type_id: '',
    equipment_id: '',
    start_date: '',
    end_date: '',
    status: 'pending',
});

const filteredEquipment = ref([]);

watch(() => form.type_id, (newTypeId) => {
    if (newTypeId) {
        // Fetch equipment based on the selected type and user's ateliers
        axios.get(`/api/types/${newTypeId}/user-equipment`).then(response => {
            filteredEquipment.value = response.data;
        }).catch(error => {
            console.error("Failed to fetch equipment:", error);
            filteredEquipment.value = [];
        });
    } else {
        filteredEquipment.value = [];
    }
});

const createReservation = () => {
    form.post(route('my-reservation.store'), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>
    <Head title="Create Reservation" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Reservation
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="createReservation">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-4">
                                <label for="type_id" class="block text-sm font-medium text-gray-700">Type</label>
                                <select id="type_id" name="type_id" v-model="form.type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="" disabled>Select Type</option>
                                    <option v-for="type in props.types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="equipment_id" class="block text-sm font-medium text-gray-700">Equipment</label>
                                <select id="equipment_id" name="equipment_id" v-model="form.equipment_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="" disabled>Select Equipment</option>
                                    <option v-for="equipment in filteredEquipment" :key="equipment.id" :value="equipment.id">{{ equipment.name }}</option>
                                    <option v-if="filteredEquipment.length === 0" disabled>No equipment of this type available</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="datetime-local" id="start_date" name="start_date" v-model="form.start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="datetime-local" id="end_date" name="end_date" v-model="form.end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-700">
                                    Create
                                </button>
                                <Link :href="route('my-reservation.index')" class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700">
                                    Back
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>