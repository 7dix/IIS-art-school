<script setup lang="ts">
import { defineProps, ref, onMounted } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import axios from "axios";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    equipments: {
        type: Object,
        required: true,
    },
    atelierId: {
        type: Number,
        required: true,
    },
    userId: {
        type: Number,
        required: true,
    },
});

const addDialogRef = ref(null);
const restrictedEquipmentIds = ref([]);

const openAddDialog = () => {
    if (addDialogRef.value) {
        addDialogRef.value.openDialog();
    }
};

const borrowEquipment = (equipment) => {
    console.log("Borrowing equipment:", equipment.name);
    window.location.href = route('my-reservation.create', { type_id: equipment.type_id, equipment_id: equipment.id });
};

const fetchRestrictedEquipments = async () => {
    try {
        console.log("User ID:", props.userId);
        const response = await axios.get(`/api/ateliers/${props.atelierId}/users/${props.userId}/restricted-equipment`);
        console.log("Response data:", response.data);
        restrictedEquipmentIds.value = response.data.data.map(equipment => equipment.id);
        console.log("Restricted Equipment IDs:", restrictedEquipmentIds.value);
    } catch (error) {
        console.error("Failed to fetch restricted equipment:", error);
    }
};

onMounted(() => {
    fetchRestrictedEquipments();
});
</script>

<template>
    <Card class="mb-6 w-full max-w-sm h-auto">
        <CardHeader class="grid grid-cols-3 items-center">
            <div></div>
            <CardTitle class="text-lg font-semibold leading-tight text-gray-800">
                {{ title }}
            </CardTitle>
            <div class="flex justify-end">
                
            </div>
        </CardHeader>
        <CardContent class="mt-4">
            <ul class="list-none">
                <li
                    v-for="equipment in equipments.data"
                    :key="equipment.id"
                    class="flex justify-between items-center py-2 border-b border-gray-200"
                >
                    <span>{{ equipment.name + " (" + equipment.type_name + ")" }}</span>
                    <Button
                        @click="borrowEquipment(equipment)"
                        :disabled="restrictedEquipmentIds.includes(equipment.id)"
                        class="bg-green-500 disabled:bg-gray-500 text-white hover:bg-yellow-700 w-10 h-10 flex items-center justify-center"
                    >
                        <Icon icon="dashicons:hammer" class="w-5 h-5" />
                    </Button>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>