<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import StatusFilter from '@/Components/Reservation/StatusFilter.vue';
import { Head, Link } from '@inertiajs/vue3';
import { VTColumn, VTFilter } from "@/types";
import { h, ref, render } from "vue";
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'



const props = defineProps({
    reservations: {
        type: Object,
        required: true,
    },
})

const parseDateTime = (date: string) => {
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('cs-CZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const statusClass = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-500';
        case 'approved':
            return 'bg-green-500';
        case 'rejected':
            return 'bg-red-500';
        case 'ongoing':
            return 'bg-blue-500';
        case 'completed':
            return 'bg-green-500';
        default:
            return 'bg-gray-500';
    }
}

const columns: VTColumn[] = [
    {
        "key": "equipment",
        "header": "Equipment",
        renderAs: (item) => {
            return h('span', item.equipment.name)
        }
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
                    class: statusClass(item.status)
                },
                item.status
            );
        }
    },
    {
        "key": "user",
        "header": "User",
        renderAs: (item) => {
            return h('span', item.user.first_name + ' ' + item.user.last_name)
        }
    }
]

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
    <Head title="Atelier" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Manage reservations
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <StatusFilter :statuses="statuses" />
                    <Table :data="reservations.data" :columns="columns" :filter="statuses"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>