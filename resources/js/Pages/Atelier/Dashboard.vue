<script setup lang="ts">
import { defineProps, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UsersCard from "@/Components/Atelier/UsersCard.vue";
import TeachersCard from "@/Components/Atelier/TeachersCard.vue";
import EquipmentsCard from "@/Components/Atelier/EquipmentCard.vue";

const props = defineProps({
    atelier: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
    teachers: {
        type: Array,
        required: true,
    },
    equipments: {
        type: Array,
        required: true,
    }
});

const { props: pageProps } = usePage();
const userId = pageProps.auth.user.id;

onMounted(() => {
    console.log("Atelier:", props.atelier);
    console.log("Users:", props.users);
    console.log("Teachers:", props.teachers);
    console.log("User ID:", userId);
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
                    <!-- Users Card for Users -->
                    <UsersCard
                        title="Users"
                        :users="users"
                        :atelierId="atelier.id"
                    />

                    <!-- Users Card for Teachers -->
                    <TeachersCard
                        title="Teachers"
                        :teachers="teachers"
                        :users="users"
                        :atelierId="atelier.id"
                    />
                     <!-- Equipments Card -->
                     <EquipmentsCard
                        title="Equipments"
                        :equipments="equipments"
                        :atelierId="atelier.id"
                        :userId="userId"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>