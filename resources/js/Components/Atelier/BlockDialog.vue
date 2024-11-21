<script setup>
import { ref } from "vue";
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

const openDialog = () => {
    isDialogOpen.value = true;
    fetchEquipments();
};

const fetchEquipments = async () => {
    try {
        const response = await axios.get(`/api/ateliers/${props.atelierId}/equipment`);
        availableEquipment.value = response.data;
    } catch (error) {
        console.error("Failed to fetch equipment:", error);
    }
};

const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {
        props.onConfirm(selectedEquipment.value); 
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