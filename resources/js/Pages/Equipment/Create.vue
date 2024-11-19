<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Table from '@/Components/Atelier/Table.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';



const props = defineProps({
    types: {
        type: Object,
        required: true,
    },
})

let ateliers = ref({});

const form = useForm({
    name: '',
    maximum_leasing_period: '',
    type_id: '',
    atelier_id: '',
});

const createEquipment = () => {    
    form.post(route('equipment.store'));
};

watch(
    () => form.type_id, //kdyz se zmeni typ vybaveni -> v atelierech jen ty ateliery co maji ten typ
    (newValue) => {
        getAteliers(newValue);
    }
)

const getAteliers = (type_id) => {
    axios.get(`/api/getAteliersWithType/${type_id}`)
    .then(response => {
        console.log(response.data);
        ateliers.value = response.data;
    })
    .catch(error => {
        console.log(error);
    });
}


</script>



<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Create an Equipment
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                    <form @submit.prevent="createEquipment">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" v-model="form.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">Maximum leasing period</label>
                                <input type="text" id="room" v-model="form.maximum_leasing_period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700" for="role">Type:</label>
                                <select v-model="form.type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option v-for="type in props.types.data" :key="type.id" :value="type.id">{{ type.name }}</option>
                                </select>
                            </div>
                            <div class="mb-4">    
                                <label class="block text-sm font-medium text-gray-700" for="role">Ateliers:</label>
                                <select v-model="form.atelier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option v-for="atelier in ateliers.data" :key="atelier.id" :value="atelier.id">{{ atelier.name }}</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-700">
                                    Create
                                </button>
                                <Link :href="route('equipment.index')" class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700">
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