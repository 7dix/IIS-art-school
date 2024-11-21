<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import axios from 'axios';

// Get the current date in the required format
const getCurrentDate = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

// Calculate the end date based on the start date and leasing period
const calculateEndDate = (startDate, leasingPeriod) => {
    const start = new Date(startDate);
    start.setDate(start.getDate() + leasingPeriod);
    const year = start.getFullYear();
    const month = String(start.getMonth() + 1).padStart(2, '0');
    const day = String(start.getDate()).padStart(2, '0');
    const hours = String(start.getHours()).padStart(2, '0');
    const minutes = String(start.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const props = defineProps({
    types: {
        type: Array,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
    typeId: {
        type: Number,
        default: 0,
    },
    equipmentId: {
        type: Number,
        default: 0,
    },
});

const form = useForm({
    type_id: props.typeId || '',
    equipment_id: props.equipmentId || '',
    start_date: getCurrentDate(), // Set the default value to today's date
    end_date: '',
    status: 'pending',
});

const filteredEquipment = ref([]);

watch(() => form.type_id, (newTypeId) => {
    if (newTypeId) {
        // Fetch equipment based on the selected type and user's ateliers
        axios.get(`/api/types/${newTypeId}/user-equipment`).then(response => {
            filteredEquipment.value = response.data;
            // Set the equipment_id if it is provided
            if (props.equipmentId) {
                form.equipment_id = props.equipmentId;
            }
        }).catch(error => {
            console.error("Failed to fetch equipment:", error);
            filteredEquipment.value = [];
        });
    } else {
        filteredEquipment.value = [];
    }
});

// Ensure the equipment list is fetched when the component is mounted
if (form.type_id) {
    axios.get(`/api/types/${form.type_id}/user-equipment`).then(response => {
        filteredEquipment.value = response.data;
        // Set the equipment_id if it is provided
        if (props.equipmentId) {
            form.equipment_id = props.equipmentId;
        }
    }).catch(error => {
        console.error("Failed to fetch equipment:", error);
        filteredEquipment.value = [];
    });
}

const selectedEquipment = computed(() => {
    return filteredEquipment.value.find(equipment => equipment.id === form.equipment_id) || null;
});

// Watch for changes to selectedEquipment and update end_date accordingly
watch(selectedEquipment, (newEquipment) => {
    if (newEquipment && newEquipment.maximum_leasing_period) {
        form.end_date = calculateEndDate(form.start_date, newEquipment.maximum_leasing_period);
    } else {
        form.end_date = '';
    }
});

// Watch for changes to start_date and update end_date accordingly
watch(() => form.start_date, (newStartDate) => {
    if (selectedEquipment.value && selectedEquipment.value.maximum_leasing_period) {
        form.end_date = calculateEndDate(newStartDate, selectedEquipment.value.maximum_leasing_period);
    }
});

const createReservation = () => {
    form.post(route('my-reservation.store'), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Create Reservation" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Reservation
                {{ userId }}
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
                                    <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
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

                            <!-- Display selected equipment details -->
                            <div v-if="selectedEquipment" class="mb-4">
                                <h3 class="text-lg font-semibold">Selected Equipment Details</h3>
                                <div class="info-box" v-if="selectedEquipment.maximum_leasing_period">
                                    <div class="info-box-label">Maximum Leasing Period</div>
                                    <div class="info-box-value">{{ selectedEquipment.maximum_leasing_period }} days</div>
                                </div>
                                <div class="info-box" v-if="selectedEquipment.allowed_leasing_hours">
                                    <div class="info-box-label">Allowed Leasing Hours</div>
                                    <div class="info-box-value">{{ selectedEquipment.allowed_leasing_hours }}</div>
                                </div>
                                <div class="info-box" v-if="selectedEquipment.atelier && selectedEquipment.atelier.name">
                                    <div class="info-box-label">Atelier</div>
                                    <div class="info-box-value">{{ selectedEquipment.atelier.name }}</div>
                                </div>
                                <div class="info-box" v-if="selectedEquipment.owner && selectedEquipment.owner.name">
                                    <div class="info-box-label">Owner</div>
                                    <div class="info-box-value">{{ selectedEquipment.owner.name }} | {{ selectedEquipment.owner.email }}</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="datetime-local" id="start_date" name="start_date" v-model="form.start_date" :min="getCurrentDate()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="datetime-local" id="end_date" name="end_date" v-model="form.end_date" :min="form.start_date" :max="calculateEndDate(form.start_date, selectedEquipment?.maximum_leasing_period || 0)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-700">
                                    Create
                                </button>
                                <a @click="goBack" class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700">
                                    back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    .info-box {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-box-label {
        font-weight: 600;
    }
</style>