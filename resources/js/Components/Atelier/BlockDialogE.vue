<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from "vue";
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/Components/ui/alert-dialog";
import { Switch } from '@/Components/ui/switch'
import axios from "axios";

const props = defineProps({
    onConfirm: {
        type: Function,
        required: true,
    },
});

const isDialogOpen = ref(false);
const selectedEquipment = ref({});

const openDialog = (equipment_id) => {
    isDialogOpen.value = true;
    
    fetchEquipment(equipment_id);
};

const fetchEquipment = async (equipment_id) => {
    try {
        const response = await axios.get(`/api/get-equipment/${equipment_id}`);
        selectedEquipment.value = { ...response.data};
        selectedEquipment.value.can_be_borrowed = selectedEquipment.value.can_be_borrowed === 1;
        console.log(selectedEquipment.value.can_be_borrowed);
    
    } catch (error) {
        console.error("Failed to fetch equipment:", error);
    }
};



const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {

        console.log(selectedEquipment.value.can_be_borrowed);
        props.onConfirm(selectedEquipment.value); // Pass selected users to the onConfirm function
    }
    selectedEquipment.value = {}; // Clear selected users
    isDialogOpen.value = false;
};


const handleCancel = () => {
    isDialogOpen.value = false;
};

const handleSwitchChange = (value) => {
    console.log("Switch changed to:", value);
};

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape' && isDialogOpen.value) {
        handleCancel();
    }
  };

  onMounted(() => {
        document.addEventListener('keydown', handleKeydown);
    
    });
  
  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
  });


defineExpose({ openDialog });
</script>

<template>
    <AlertDialog :open="isDialogOpen">
        <AlertDialogTrigger asChild>
            <button class="hidden"></button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Block Equipment for everyone</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>


                <Switch v-model:checked="selectedEquipment.can_be_borrowed">                
                </Switch>
                
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