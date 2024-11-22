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
const availableUsers = ref([]); // Initialize availableUsers array
const selectedUsers = ref([]); // Initialize selectedUsers array

const openDialog = () => {
    isDialogOpen.value = true;
    fetchAvailableUsers(); // Fetch available users when dialog is opened
};

const fetchAvailableUsers = async () => {
    try {
        const response = await axios.get(
            `/api/ateliers/${props.atelierId}/available-users`
        );
        console.log("Fetched users:", response.data); // Log the fetched users
        availableUsers.value = response.data;
    } catch (error) {
        console.error("Failed to fetch available users:", error);
    }
};

const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {
        props.onConfirm(selectedUsers.value); // Pass selected users to the onConfirm function
    }
    selectedUsers.value = []; // Clear selected users
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
                <AlertDialogTitle>Add users</AlertDialogTitle>
            </AlertDialogHeader>
            <AlertDialogDescription>
                <Multiselect
                    v-model="selectedUsers"
                    :options="availableUsers"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :preserve-search="true"
                    placeholder="Select users"
                    label="name"
                    track-by="id"
                    :customLabel="
                        (user) => `${user.name}`
                    "
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
