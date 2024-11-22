<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import StatusFilter from '@/Components/Reservation/StatusFilter.vue';
import { Head, Link } from '@inertiajs/vue3';
import { VTColumn, VTFilter } from "@/types";
import { h, ref, render } from "vue";
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { statusClassColor, parseDateTime } from "@/Composables/reservationUtils";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'





const props = defineProps({
    reservations: {
        type: Object,
        required: true,
    },
})




const columns: VTColumn[] = [
    {
        "key": "equipment",
        "header": "Equipment",
    },
    {
        "key": "user",
        "header": "User",
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
            return h('span', `${parseDateTime(item.start_date)}`);
        }
    },
    {
        "key": "end_date",
        "header": "Return date",
        renderAs: (item) => {
            return h('span', `${parseDateTime(item.end_date)}`);
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
    "key": "id",
        "header": "Actions",
        renderAs: (item) => {
            return h('div', 
                { class: "flex items-center justify-center" },
                [
                    h(Link, 
                        {
                            href: route('reservation.show', item.id),
                            class: "inline-flex items-center px-4 py-2 bg-primary text-white font-semibold text-xs uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 rounded-md"
                        },
                        'Show'
                    )
                ]
            )
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
    <Head title="Manage Reservations" />
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink>Reservations</BreadcrumbLink>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Manage reservations
            </h2>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <StatusFilter :statuses="statuses" />
                    <Table :data="reservations" :columns="columns" :filter="statuses" :flatData="true"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>