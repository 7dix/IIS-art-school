<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Table from "@/Components/Types/Table.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    types: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Types" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Types
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div
                        class="mt-4 sm:mt-0 sm:ml-16 sm:flex sm:justify-end pr-6 pt-6">
                        <Link
                            :href="route('types.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Type
                        </Link>
                    </div>
                    <div class="p-6 text-gray-900">
                        <Table :types="types" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
