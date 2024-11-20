<script setup lang="ts">
import { defineProps, onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UsersCard from "@/Components/Atelier/UsersCard.vue";

const props = defineProps({
    atelier: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
    teachers: {
        type: Array,
        required: true,
    },
});

onMounted(() => {
    console.log("Atelier:", props.atelier);
    console.log("Students:", props.students);
    console.log("Teachers:", props.teachers);
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ atelier.name }} Dashboard
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <!-- Users Card for Students -->
                    <UsersCard
                        title="Students"
                        :users="students"
                        :atelierId="atelier.id"
                        :onAddUser="addUser"
                        :onRemoveUser="removeUser"
                    />

                    <!-- Users Card for Teachers -->
                    <UsersCard
                        title="Teachers"
                        :users="teachers"
                        :onAddUser="addUser"
                        :onRemoveUser="removeUser"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
