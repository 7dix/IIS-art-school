<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import { VTColumn } from "@/types";
import { h } from "vue";
import { Button } from '@/Components/ui/button'


const props = defineProps({
    equipments: {
        type: Object,
        required: true,
    },
    types: {
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
        "key": "type",
        "header": "Type",
        renderAs: (item) => {
            return h('span', item.type.name)
        }
    },

    {
        "key": "owner",
        "header": "Owner",
        renderAs: (item) => {
            return h('span', item.owner.first_name + " " + item.owner.last_name)
        }
    },
    {
        "key": "atelier",
        "header": "Atelier",
        renderAs: (item) => {
            return h('span', item.atelier.name)
        }
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
    <Head title="Dashboard" />  
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Equipment
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <h1> Logged user: {{ $page.props.auth.user["first_name"] + " " + $page.props.auth.user["last_name"] }} </h1>
                    <div
                        class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6">
                        <Link
                            :href="route('equipment.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Equipment
                        </Link>
                    </div>


                    <div class="p-6 text-gray-900">
                        <Table :data="equipments.data" :columns="columns" icon="solar:case-minimalistic-outline" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
