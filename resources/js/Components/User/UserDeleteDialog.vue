<script setup lang="ts">
    import { defineProps, defineEmits, ref, watch, onMounted, onUnmounted, toRaw } from 'vue';
    import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/Components/ui/alert-dialog";

const props = defineProps<{
    user: any,
    errors: any,
    isOpen: boolean,

}>();
const emit = defineEmits(['save', 'close']);
const deletedUser = ref({ ...props.user });


const save = () => {
        emit('save', deletedUser.value.id);
    };

const close = () => {
    emit('close');
};
const handleKeydown = (event: KeyboardEvent) => {
if (event.key === 'Escape') {
    close();
}
};

onMounted(() => {
if (props.isOpen) {
    document.addEventListener('keydown', handleKeydown);
}   
});

onUnmounted(() => {
document.removeEventListener('keydown', handleKeydown);
});


</script>

<template>
    <AlertDialog :open="isOpen">
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
                <div v-if="errors" class="mt-2 text-sm text-red-600"> {{ errors.message }} </div>    
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="close()"
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction @click="save()"
                    >Delete</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
