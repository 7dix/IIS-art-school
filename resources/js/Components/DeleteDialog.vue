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

const props = defineProps({
    onConfirm: {
        type: Function,
        required: true,
    },
    onCancel: {
        type: Function,
        required: true,
    },
    type: {
        type: Object,
        required: true,
    },
});

const isDialogOpen = ref(false);

const openDialog = () => {
    isDialogOpen.value = true;
};

const handleConfirm = () => {
    console.log(props.type);
    if (typeof props.onConfirm === "function") {
        props.onConfirm(props.type);
    }
    isDialogOpen.value = false;
};

const handleCancel = () => {
    if (typeof props.onCancel === "function") {
        props.onCancel();
    }
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
                <h2 class="text-lg font-semibold">Are you sure?</h2>
                <p class="text-sm text-gray-500">
                    Do you really want to delete this type? This action cannot
                    be undone.
                </p>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel"
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction @click="handleConfirm"
                    >Delete</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
