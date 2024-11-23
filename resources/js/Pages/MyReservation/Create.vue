<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, usePage } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import axios from "axios";
import { Button } from "@/Components/ui/button";
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
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/Components/ui/popover";
import { Calendar } from "@/Components/ui/v-calendar";
import { CalendarIcon } from "@radix-icons/vue";
import { addDays, format, isWithinInterval } from "date-fns";
import "@vuepic/vue-datepicker/dist/main.css";

// Get the current date in the required format
const getCurrentDate = () => {
    const now = new Date();
    return format(now, "yyyy-MM-dd'T'HH:mm:ss");
};

// Calculate the end date based on the start date and leasing period
const calculateEndDate = (startDate, leasingPeriod) => {
    const start = new Date(startDate);
    start.setDate(start.getDate() + leasingPeriod);
    return format(start, "yyyy-MM-dd'T'HH:mm:ss");
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
    type_id: props.typeId || "",
    equipment_id: props.equipmentId || "",
    start_date: getCurrentDate(), // Set the default value to today's date
    end_date: "",
    status: "pending",
});

const filteredEquipment = ref([]);
const existingReservations = ref([]);
const isInitialized = ref(false);
const warningMessage = ref("");

watch(
    () => form.type_id,
    (newTypeId) => {
        if (newTypeId) {
            // Fetch equipment based on the selected type and user's ateliers
            axios
                .get(`/api/types/${newTypeId}/user-equipment`)
                .then((response) => {
                    filteredEquipment.value = response.data;
                    // Set the equipment_id if it is provided
                    if (props.equipmentId) {
                        form.equipment_id = props.equipmentId;
                    }
                })
                .catch((error) => {
                    console.error("Failed to fetch equipment:", error);
                    filteredEquipment.value = [];
                });
        } else {
            filteredEquipment.value = [];
        }
    }
);

// Ensure the equipment list is fetched when the component is mounted
if (form.type_id) {
    axios
        .get(`/api/types/${form.type_id}/user-equipment`)
        .then((response) => {
            filteredEquipment.value = response.data;
            // Set the equipment_id if it is provided
            if (props.equipmentId) {
                form.equipment_id = props.equipmentId;
            }
        })
        .catch((error) => {
            console.error("Failed to fetch equipment:", error);
            filteredEquipment.value = [];
        });
}

const selectedEquipment = computed(() => {
    return (
        filteredEquipment.value.find(
            (equipment) => equipment.id === form.equipment_id
        ) || null
    );
});

// Watch for changes to selectedEquipment and update end_date accordingly
watch(selectedEquipment, (newEquipment) => {
    if (newEquipment && newEquipment.maximum_leasing_period) {
        if (!isInitialized.value) {
            const endDate = calculateEndDate(
                form.start_date,
                newEquipment.maximum_leasing_period
            );
            form.end_date = endDate;
            date.value.end = new Date(endDate);
            isInitialized.value = true;
        }

        // Fetch existing reservations for the selected equipment
        axios
            .get(`/api/equipment/${newEquipment.id}/reservations`)
            .then((response) => {
                console.log(
                    "Existing reservations for selected equipment:",
                    response.data
                );
                existingReservations.value = response.data;
            })
            .catch((error) => {
                console.error("Failed to fetch reservations:", error);
            });
    } else {
        form.end_date = "";
        date.value.end = null;
        existingReservations.value = [];
    }
});

// Watch for changes to start_date and update end_date accordingly
watch(
    () => form.start_date,
    (newStartDate) => {
        if (
            selectedEquipment.value &&
            selectedEquipment.value.maximum_leasing_period
        ) {
            const endDate = calculateEndDate(
                newStartDate,
                selectedEquipment.value.maximum_leasing_period
            );
            form.end_date = endDate;
            date.value.end = new Date(endDate);
        }
    }
);

const createReservation = () => {
    const maxEndDate = calculateEndDate(
        form.start_date,
        selectedEquipment.value.maximum_leasing_period
    );
    if (new Date(form.end_date) > new Date(maxEndDate)) {
        alert("Selected end date exceeds maximum leasing period.");
        return;
    }
    form.post(route("my-reservation.store"), {
        onError: (errors) => console.log(errors),
    });
};

const goBack = () => {
    window.history.back();
};

const date = ref({
    start: new Date(form.start_date),
    end: form.end_date ? new Date(form.end_date) : addDays(new Date(), 20),
});

// Watch for changes to the date range and update form.start_date and form.end_date accordingly
watch(date, (newDate) => {
    form.start_date = format(newDate.start, "yyyy-MM-dd'T'HH:mm:ss");
    form.end_date = newDate.end
        ? format(newDate.end, "yyyy-MM-dd'T'HH:mm:ss")
        : "";

    if (
        selectedEquipment.value &&
        selectedEquipment.value.maximum_leasing_period
    ) {
        const maxEndDate = calculateEndDate(
            form.start_date,
            selectedEquipment.value.maximum_leasing_period
        );
        if (new Date(form.end_date) > new Date(maxEndDate)) {
            warningMessage.value =
                "Selected date range exceeds the maximum leasing period. The end date has been adjusted.";
            form.end_date = maxEndDate;
            date.value.end = new Date(maxEndDate);
        } else {
            warningMessage.value = "";
        }
    }
});

// Calculate the maximum end date based on the selected equipment's maximum leasing period
const maxEndDate = computed(() => {
    if (
        selectedEquipment.value &&
        selectedEquipment.value.maximum_leasing_period
    ) {
        return addDays(
            new Date(date.value.start),
            selectedEquipment.value.maximum_leasing_period
        );
    }
    return null;
});

// Compute disabled dates based on existing reservations
const disabledDates = computed(() => {
    return existingReservations.value.flatMap((reservation) => {
        const start = new Date(reservation.start_date);
        const end = new Date(reservation.end_date);
        const dates = [];
        for (let d = start; d <= end; d.setDate(d.getDate() + 1)) {
            dates.push(new Date(d));
        }
        return dates;
    });
});

// Compute the maximum range based on the selected equipment's maximum leasing period
const maxRange = computed(() => {
    return selectedEquipment.value
        ? selectedEquipment.value.maximum_leasing_period
        : null;
});

const leasingHours = computed(() => {
    let array = [];
    let hours = selectedEquipment.value.allowed_leasing_hours;
    if (typeof hours === 'string') {
    hours = hours
        .replace(/[\[\]\s]/g, '') // Remove brackets and whitespace
        .split(',')
        .map(Number)
        .filter(n => !isNaN(n)); // Remove any invalid numbers
    }   
    if (hours === undefined || hours === null || hours.length === 0) {
        return array;
    } else {
        hours = hours.sort((a, b) => a - b);
        let currentMin = hours[0];
        for (let i = 0; i < hours.length; i++) {
            if (hours[i] + 1 !== hours[i + 1]) {
                array.push({ from: currentMin, to: hours[i] + 1 });
                currentMin = hours[i + 1];
            }
        }
    }
    return array;
})
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
                                <label
                                    for="type_id"
                                    class="block text-sm font-medium text-gray-700"
                                    >Type</label
                                >
                                <Select required id="type_id" name="type_id" v-model="form.type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a type of equipment" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="type in types"
                                            :key="type.id"
                                            :value="type.id">
                                            {{ type.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> 
                            </div>
                            <div class="mb-4">
                                <label
                                    for="equipment_id"
                                    class="block text-sm font-medium text-gray-700"
                                    >Equipment</label
                                >
                                <Select required id="equipment_id" name="equipment_id" v-model="form.equipment_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a equipment" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="equipment in filteredEquipment"
                                            :key="equipment.id"
                                            :value="equipment.id"
                                        >{{ equipment.name }}
                                        </SelectItem>
                                        <SelectItem
                                            v-if="filteredEquipment.length === 0"
                                            disabled
                                        >No equipment of this type available
                                        </SelectItem>
                                    </SelectContent>
                                </Select> 
                            </div>

                            <!-- Display selected equipment details -->
                            <div v-if="selectedEquipment" class="mb-4">
                                <h3 class="text-lg font-semibold">
                                    Selected Equipment Details
                                </h3>
                                <div
                                    class="info-box"
                                    v-if="
                                        selectedEquipment.maximum_leasing_period
                                    "
                                >
                                    <div class="info-box-label">
                                        Maximum Leasing Period
                                    </div>
                                    <div class="info-box-value">
                                        {{
                                            selectedEquipment.maximum_leasing_period
                                        }}
                                        days
                                    </div>
                                </div>
                                <div
                                    class="info-box"
                                    v-if="
                                        selectedEquipment.allowed_leasing_hours
                                    "
                                >
                                    <div class="info-box-label">
                                        Allowed Leasing Hours
                                    </div>
                                    <div class="info-box-value">
                                        <div v-for="range in leasingHours" :key="range">
                                            {{ range.from.toString().padStart(2, '0') }}:00 - {{ range.to.toString().padStart(2, '0') }}:00
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="info-box"
                                    v-if="
                                        selectedEquipment.atelier &&
                                        selectedEquipment.atelier.name
                                    "
                                >
                                    <div class="info-box-label">Atelier</div>
                                    <div class="info-box-value">
                                        {{ selectedEquipment.atelier.name }}
                                    </div>
                                </div>
                                <div
                                    class="info-box"
                                    v-if="
                                        selectedEquipment.owner &&
                                        selectedEquipment.owner.name
                                    "
                                >
                                    <div class="info-box-label">Owner</div>
                                    <div class="info-box-value">
                                        {{ selectedEquipment.owner.name }} |
                                        {{ selectedEquipment.owner.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label
                                    for="date_range"
                                    class="block text-sm font-medium text-gray-700"
                                    >Date Range</label
                                >
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            id="date"
                                            variant="outline"
                                            class="w-[280px] justify-start text-left font-normal"
                                        >
                                            <CalendarIcon
                                                class="mr-2 h-4 w-4"
                                            />
                                            <span>
                                                {{
                                                    date.start
                                                        ? date.end
                                                            ? `${format(
                                                                  date.start,
                                                                  "LLL dd, y"
                                                              )} - ${format(
                                                                  date.end,
                                                                  "LLL dd, y"
                                                              )}`
                                                            : format(
                                                                  date.start,
                                                                  "LLL dd, y"
                                                              )
                                                        : "Pick a date"
                                                }}
                                            </span>
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent
                                        class="w-auto p-0"
                                        align="start"
                                    >
                                        <Calendar
                                            v-model.range="date"
                                            :columns="2"
                                            :min-date="new Date()"
                                            :show-months="2"
                                            :show-years="true"
                                            :disabled-dates="disabledDates"
                                            :max-range="maxRange"
                                        />
                                    </PopoverContent>
                                </Popover>
                                <p
                                    v-if="warningMessage"
                                    class="text-red-500 mt-2"
                                >
                                    {{ warningMessage }}
                                </p>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-700"
                                >
                                    Create
                                </button>
                                <button
                                    @click="goBack"
                                    type="button"
                                    class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700"
                                >
                                    Back
                                </button>
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
