<script setup>
import { ref, watch } from "vue";
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/Components/ui/alert-dialog";
import Multiselect from "vue-multiselect";
import axios from "axios";

const props = defineProps({
    onConfirm: {
        type: Function,
        required: true,
    },
    atelierId: {
        type: Number,
        required: true,
    },
});

const isDialogOpen = ref(false);
const availableEquipment = ref([]);
const selectedEquipment = ref([]);
const initialSelectedEquipment = ref([]);
const userId = ref(null);

const openDialog = (id_) => {
    isDialogOpen.value = true;
    userId.value = id_;
    fetchEquipments();
    fetchRestrictedEquipments(id_);
};

const fetchEquipments = async () => {
    try {
        const response = await axios.get(`/api/ateliers/${props.atelierId}/equipment`);
        availableEquipment.value = response.data.data;
    } catch (error) {
        console.error("Failed to fetch equipment:", error);
    }
};

const fetchRestrictedEquipments = async (userId) => {
    try {
        const response = await axios.get(`/api/ateliers/${props.atelierId}/users/${userId}/restricted-equipment`);
        selectedEquipment.value = response.data.data;
        initialSelectedEquipment.value = [...response.data.data]; // Clone the initial selected equipment
    } catch (error) {
        console.error("Failed to fetch restricted equipment:", error);
    }
};

const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {
        const removedEquipment = initialSelectedEquipment.value.filter(
            (initial) => !selectedEquipment.value.some((selected) => selected.id === initial.id)
        );
        props.onConfirm(selectedEquipment.value, removedEquipment, userId.value); 
    }
    selectedEquipment.value = [];
    isDialogOpen.value = false;
};

const handleCancel = () => {
    isDialogOpen.value = false;
};

defineExpose({ openDialog });
</script>

<template>
    <AlertDialog :open="isDialogOpen">
        <AlertDialogTrigger asChild>
            <button class="hidden"></button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Block Equipment</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <Multiselect
                    v-model="selectedEquipment"
                    :options="availableEquipment"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :preserve-search="true"
                    placeholder="Select Equipment"
                    label="name"
                    track-by="id"
                    :customLabel="(equipment) => `${equipment.name}`"
                />
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="handleConfirm">Save</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<style>
@import "vue-multiselect/dist/vue-multiselect.min.css";
</style>