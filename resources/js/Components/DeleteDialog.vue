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
});

const isDialogOpen = ref(false);
const id = ref(null);

const openDialog = (id_) => {
    isDialogOpen.value = true;
    id.value = id_;
};

const handleConfirm = () => {
    if (typeof props.onConfirm === "function") {
        console.log(id.value);
        props.onConfirm(id.value);
    }
    id.value = null;
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
                <h2 class="text-lg font-semibold">Are you sure?</h2>
                <p class="text-sm text-gray-500">
                    Do you really want to delete this? This action cannot be
                    undone.
                </p>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel()"
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction @click="handleConfirm()"
                    >Delete</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
