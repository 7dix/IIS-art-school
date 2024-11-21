<script setup lang="ts">
import { defineProps, ref } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import AddDialog from "@/Components/Atelier/AddDialog.vue";
import axios from "axios";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    users: {
        type: Array as () => {
            id: number;
            name: string;
        }[],
        required: true,
    },
    atelierId: {
        type: Number,
        required: true,
    },
});

const deleteDialogRef = ref(null);
const addDialogRef = ref(null);

const openDeleteDialog = (item) => {
    if (deleteDialogRef.value) {
        deleteDialogRef.value.openDialog(item.id);
    }
};

const openAddDialog = () => {
    if (addDialogRef.value) {
        addDialogRef.value.openDialog();
    }
};

const confirmDelete = async (id: number) => {
    if (id) {
        const response = await axios.delete(
            `/ateliers/${props.atelierId}/users/${id}`
        );
        if (response.status === 200) {
            props.users.splice(
                props.users.findIndex((item) => item.id === id),
                1
            );
        } else {
            console.error(response);
        }
    }
    return { confirmDelete };
};

const addUser = async (selectedUsers) => {
    try {
        const response = await axios.post(
            `/ateliers/${props.atelierId}/users`,
            { users: selectedUsers.map((user) => ({ id: user.id })) }
        );
        if (response.status === 200) {
            props.users.push(...response.data.users);
        } else {
            console.error(response);
        }
    } catch (error) {
        console.error("Failed to add users to atelier:", error);
    }
};
</script>

<template>
    <Card class="mb-6 w-full max-w-sm h-auto">
        <CardHeader class="grid grid-cols-3 items-center">
            <div></div>
            <CardTitle
                class="text-lg font-semibold leading-tight text-gray-800"
            >
                {{ title }}
            </CardTitle>
            <div class="flex justify-end">
                <Button
                    v-if="$page.props.auth.user.permissions.includes('assign_students')"
                    @click="openAddDialog"
                    class="bg-blue-500 text-white hover:bg-blue-700 w-10 h-10 flex items-center justify-center"
                >
                    <Icon icon="mingcute:add-fill" class="w-5 h-5" />
                </Button>
            </div>
        </CardHeader>
        <CardContent class="mt-4">
            <ul class="list-none">
                <li
                    v-for="user in users"
                    :key="user.id"
                    class="flex justify-between items-center py-2 border-b border-gray-200"
                >
                    <span>{{ user.name }}</span>
                    <Button
                        v-if="$page.props.auth.user.permissions.includes('assign_students')"
                        @click="openDeleteDialog(user)"
                        class="bg-red-500 text-white hover:bg-red-700 w-10 h-10 flex items-center justify-center"
                    >
                        <Icon icon="material-symbols:delete" class="w-5 h-5" />
                    </Button>
                </li>
            </ul>
        </CardContent>
        <DeleteDialog ref="deleteDialogRef" :onConfirm="confirmDelete" />
        <AddDialog
            ref="addDialogRef"
            :onConfirm="addUser"
            :atelierId="props.atelierId"
        />
    </Card>
</template>
