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
                <h2 class="text-lg font-semibold">Add users</h2>
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
                />
            </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel()"
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
