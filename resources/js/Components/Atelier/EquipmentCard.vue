<script setup lang="ts">
import { defineProps, ref, onMounted } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import axios from "axios";
import BlockDialogE from "@/Components/Atelier/BlockDialogE.vue";


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


///////////////////
///BORROW BUTTON///
///////////////////	
const restrictedEquipmentIds = ref([]);
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

////////////////////
///BLOCK DIALOG/////
////////////////////
const blockDialogRef = ref(null);

const openBlockDialog = (item) => {
    if (blockDialogRef.value) {
        blockDialogRef.value.openDialog(item.id);
    }
};


const confirmBlock = async (selectedEquipment) => {
    try {
        await axios.put(`/api/block-equipment/${selectedEquipment.id}/${selectedEquipment.can_be_borrowed == false ? 0 : 1}`, selectedEquipment);
        props.equipments.data[props.equipments.data.findIndex((equipment) => equipment.id === selectedEquipment.id)].can_be_borrowed = selectedEquipment.can_be_borrowed;   
    }
    catch (error) {
        console.error("Failed to update equipment restrictions:", error);
    }
};

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
                        :disabled="restrictedEquipmentIds.includes(equipment.id) || !equipment.can_be_borrowed"
                        class="bg-green-500 disabled:bg-gray-500 text-white hover:bg-yellow-700 w-10 h-10 flex items-center justify-center"
                    >
                        <Icon icon="dashicons:hammer" class="w-5 h-5" />
                    </Button>

                    <Button
                            v-if="$page.props.auth.user.permissions.includes('restrict_equipment')"
                            @click="openBlockDialog(equipment)"
                            class="bg-yellow-500 text-white hover:bg-yellow-700 w-10 h-10 flex items-center justify-center"
                        >
                            <Icon icon="material-symbols:block" class="w-5 h-5" />
                    </Button>
                </li>
            </ul>
        </CardContent>
    </Card>

    <BlockDialogE ref="blockDialogRef" :onConfirm="confirmBlock" :atelierId="props.atelierId"/>

</template>