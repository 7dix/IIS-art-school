<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Table.vue';
import { Head, Link } from '@inertiajs/vue3';
import { VTColumn } from "@/types";
import { h } from "vue";
import { Button } from '@/Components/ui/button'




const props = defineProps({
    ateliers: {
        type: Object,
        required: true,
    },
})

const columns: VTColumn[] = [
    {
        "key": "name",
        "header": "Name",
    },
    {
        "key": "room",
        "header": "Room",
    },
    {
        "key": "manager",
        "header": "Manager",
        renderAs: (item) => {
            return h('span', item.manager.first_name + ' ' + item.manager.last_name)
        }
    },
    {
        "key": "users",
        "header": "Students",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    }
                },
                item.users.length
            );
        },
    },
    {
        "key": "types",
        "header": "Types",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                },
                item.types.length
            );
        },
    },
    {
        "key": "actions",
        "header": "Actions",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                    class: 'bg-blue-500 text-white'
                },
                'Edit'
            );
        },
    }
]

</script>
<template>
    <Head title="Atelier" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Ateliers
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6">
                        <Link :href="route('atelier.create')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Atelier
                        </Link>
                    </div>
                    <div class="p-6 text-gray-900">
                        <Table :data="ateliers.data" :columns="columns" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>