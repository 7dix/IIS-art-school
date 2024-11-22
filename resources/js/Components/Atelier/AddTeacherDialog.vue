<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
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
const availableTeachers = ref([]);
const selectedTeachers = ref([]);

const openDialog = () => {
    isDialogOpen.value = true;
        fetchAvailableTeachers();
    };

const fetchAvailableTeachers = async () => {
    try {
        const response = await axios.get(
            `/api/ateliers/${props.atelierId}/users-in-atelier`
        );
        console.log("Fetched teachers:", response.data); 
        availableTeachers.value = response.data;
    } catch (error) {
        console.error("Failed to fetch available teachers:", error);
    }
};

const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {
        props.onConfirm(selectedTeachers.value); 
    }
    selectedTeachers.value = [];
    isDialogOpen.value = false;
};

const handleCancel = () => {
    isDialogOpen.value = false;
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
                <AlertDialogTitle>Add teachers</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <Multiselect
                    v-model="selectedTeachers"
                    :options="availableTeachers"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :preserve-search="true"
                    placeholder="Select Teachers"
                    label="name"
                    track-by="id"
                    :customLabel="(user) => `${user.name}`"
                />
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel"
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction @click="handleConfirm"
                    >Save</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<style>
@import "vue-multiselect/dist/vue-multiselect.min.css";
</style>
