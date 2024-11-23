<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="text-2xl">Welcome in the IIS Art School Reservation system dear <b>{{ $page.props.auth.user.name }}</b>!</div>
                        <div>You can borrow equipment in the My Reservation page</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
