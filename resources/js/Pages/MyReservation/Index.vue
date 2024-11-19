<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import { Head, Link } from '@inertiajs/vue3';



const props = defineProps({
    reservations: {
        type: Object,
        required: true,
    },
})

</script>
<template>
    <Head title="Atelier" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                My reservations
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <Table 
                        :data="reservations.data" 
                        noDataMessage="You dont have any reservations. Create one first." />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>