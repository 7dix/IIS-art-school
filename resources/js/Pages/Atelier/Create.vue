<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { Input } from '@/Components/ui/input';
import { Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
 } from '@/Components/ui/select';




const props = defineProps({
    ateliers: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: '',
    room: '',
    manager_id: "",
});

const createAtelier = () => {
    form.post(route('atelier.store'));
};

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Create an Atelier
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                    <form @submit.prevent="createAtelier">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">*Name</label>
                                <Input type="text" id="name" v-model="form.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-600"> {{ form.errors.name }} </div>    
                            </div>
                        
                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">*Room</label>
                                <Input type="text" id="room" v-model="form.room" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                                <div class="text-gray-600 text-xs">Correct Format: Capital letter followed by exactly 3 numbers</div>
                                <div v-if="form.errors.room" class="mt-2 text-sm text-red-600"> {{ form.errors.room }} </div>    
                            </div>
                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700">*Manager:</label>
                                <Select v-model="form.manager_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a manager" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="user in props.users.data" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> 
                                <div v-if="form.errors.manager_id" class="mt-2 text-sm text-red-600"> {{ form.errors.manager_id }} </div>    
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-700">
                                    Create
                                </button>
                                <Link :href="route('atelier.index')" class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700">
                                    Back
                                </Link>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>