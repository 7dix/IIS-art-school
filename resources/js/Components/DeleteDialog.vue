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

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
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

// Instead of using a local ref, directly use props.isOpen
const handleConfirm = () => {
    props.onConfirm(props.type);
    // Emit an event to close the dialog
    emit("update:isOpen", false);
};

const handleCancel = () => {
    props.onCancel();
    // Emit an event to close the dialog
    emit("update:isOpen", false);
};
</script>

<template>
    <AlertDialog :open="props.isOpen">
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
