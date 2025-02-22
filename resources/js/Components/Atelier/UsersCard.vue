<script setup lang="ts">
import { defineProps, ref } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import BlockDialog from "@/Components/Atelier/BlockDialog.vue";
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
const blockDialogRef = ref(null);
const addDialogRef = ref(null);

const openDeleteDialog = (item) => {
    if (deleteDialogRef.value) {
        deleteDialogRef.value.openDialog(item.id);
    }
};

const openBlockDialog = (item) => {
    if (blockDialogRef.value) {
        blockDialogRef.value.openDialog(item.id);
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

const confirmBlock = async (selectedEquipments, removedEquipments, userId) => {
    try {
        // Add new restrictions
        if (selectedEquipments.length > 0) {
            await axios.post(`/ateliers/${props.atelierId}/restrictions`, {
                equipments: selectedEquipments.map((equipment) => ({ id: equipment.id })),
                user_id: userId,
            });
        }

        // Remove restrictions
        if (removedEquipments.length > 0) {
            await axios.post(`/ateliers/${props.atelierId}/remove-restrictions`, {
                equipments: removedEquipments.map((equipment) => ({ id: equipment.id })),
                user_id: userId,
            });
        }
    } catch (error) {
        console.error("Failed to update equipment restrictions:", error);
    }
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
        console.error("Failed to update equipment restrictions:", error);
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
                    <div class="flex justify-end space-x-2">
                        <Button
                            v-if="$page.props.auth.user.permissions.includes('restrict_equipment')"
                            @click="openBlockDialog(user)"
                            class="bg-yellow-500 text-white hover:bg-yellow-700 w-10 h-10 flex items-center justify-center"
                        >
                            <Icon icon="material-symbols:block" class="w-5 h-5" />
                        </Button>
                        <Button
                            v-if="$page.props.auth.user.permissions.includes('assign_students')"
                            @click="openDeleteDialog(user)"
                            class="bg-red-500 text-white hover:bg-red-700 w-10 h-10 flex items-center justify-center"
                        >
                            <Icon icon="material-symbols:delete" class="w-5 h-5" />
                        </Button>
                    </div>
                </li>
            </ul>
        </CardContent>
        <DeleteDialog ref="deleteDialogRef" :onConfirm="confirmDelete" />
        <BlockDialog ref="blockDialogRef" :onConfirm="confirmBlock" :atelierId="props.atelierId"/>
        <AddDialog
            ref="addDialogRef"
            :onConfirm="addUser"
            :atelierId="props.atelierId"
        />
    </Card>
</template>