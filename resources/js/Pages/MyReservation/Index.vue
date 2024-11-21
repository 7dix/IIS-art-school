<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/Components/ui/badge'
import StatusFilter from '@/Components/Reservation/StatusFilter.vue';
import { statusClassColor, parseDateTime } from "@/Composables/reservationUtils";
import { defineProps, h, ref } from 'vue';
import { VTColumn, VTFilter } from "@/types";


const props = defineProps({
    reservations: {
        type: Array,
        required: true,
    },
});

const columns: VTColumn[] = [
    {
        "key": "equipment",
        "header": "Equipment",
    },
    {
        "key": "created_at",
        "header": "Created at",  
        renderAs: (item) => {
            return h('span', `${parseDateTime(item.created_at)}`);
        }

    },
    {
        "key": "start_date",
        "header": "Borrow date",
        renderAs: (item) => {
            return h('span', `${parseDateTime(item.created_at)}`);
        }
    },
    {
        "key": "end_date",
        "header": "Return date",
        renderAs: (item) => {
            return h('span', `${parseDateTime(item.created_at)}`);
        }
    },
    {
        "key": "status",
        "header": "Status",
        renderAs: (item) => {
            return h(
                Badge,
                {
                    class: statusClassColor(item.status)
                },
                item.status
            );
        }
    },
    {
        key: "actions",
        header: "Actions",
        renderAs: (item) => {
            return h(Link, 
                {
                    href: route('reservation.show', item.id),
                    class: "inline-flex items-center px-4 py-2 bg-primary text-white font-semibold text-xs uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 rounded-md"
                },
                'Show'
            );
        },
    }
];
const statuses = ref<VTFilter[]>([
    { column: 'status', operator: true, value: 'pending' },
    { column: 'status', operator: true, value: 'approved' },
    { column: 'status', operator: true, value: 'rejected' },
    { column: 'status', operator: true, value: 'ongoing' },
    { column: 'status', operator: true, value: 'completed' },
    { column: 'status', operator: true, value: 'cancelled' },
]);
</script>

<template>
    <Head title="My Reservations" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Reservations
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6">
                        <Link
                            :href="route('my-reservation.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Create Reservation
                        </Link>
                    </div>
                    <div class="p-6 text-gray-900">
                        <StatusFilter :statuses="statuses" />
                        <Table :data="reservations" :columns="columns" :filter="statuses" noDataMessage="You don't have any reservations. Create one first." />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>