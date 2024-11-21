<script setup lang="ts">
import { defineProps, ref } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import AddTeacherDialog from "@/Components/Atelier/AddTeacherDialog.vue";
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
    teachers: {
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

const addDialogRef = ref(null);

const openAddDialog = () => {
    if (addDialogRef.value) {
        addDialogRef.value.openDialog();
    }
};

const removeTeacherRole = async (user) => {
    try {
        const response = await axios.post(
            `/ateliers/${props.atelierId}/remove-teacher-role`,
            { user_id: user.id }
        );
        if (response.status === 200) {
            // Remove the user from the teachers list and add to students list
            props.teachers.splice(
                props.teachers.findIndex((item) => item.id === user.id),
                1
            );
            props.users.push(response.data.user);
            // You might want to emit an event or call a method to update the students list
        } else {
            console.error(response);
        }
    } catch (error) {
        console.error("Failed to remove teacher role:", error);
    }
};

const addTeacher = async (selectedTeachers) => {
    try {
        const response = await axios.post(
            `/ateliers/${props.atelierId}/add-teachers`,
            { users: selectedTeachers.map((user) => user.id) }
        );
        if (response.status === 200) {
            // Add the new teachers to the list
            props.teachers.push(...response.data.teachers);
            response.data.teachers.forEach((newTeacher) => {
                const index = props.users.findIndex((user) => user.id === newTeacher.id);
                if (index !== -1) {
                    props.users.splice(index, 1);
                }
            });
        } else {
            console.error(response);
        }
    } catch (error) {
        console.error("Failed to add teachers to atelier:", error);
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
                    v-for="teacher in teachers"
                    :key="teacher.id"
                    class="flex justify-between items-center py-2 border-b border-gray-200"
                >
                    <span>{{ teacher.name }}</span>
                    <Button
                        @click="removeTeacherRole(teacher)"
                        class="bg-yellow-500 text-white hover:bg-yellow-700 w-10 h-10 flex items-center justify-center"
                    >
                        <Icon icon="dashicons:remove" class="w-5 h-5" />
                    </Button>
                </li>
            </ul>
        </CardContent>
        <DeleteDialog ref="deleteDialogRef" :onConfirm="confirmDelete" />
        <AddTeacherDialog
            ref="addDialogRef"
            :onConfirm="addTeacher"
            :atelierId="props.atelierId"
        />
    </Card>
</template>
