<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';
import { Input } from '@/Components/ui/input';
import { Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
 } from '@/Components/ui/select';
 import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/Components/ui/number-field'
import EquipmentSelectHours from '@/Components/Equipment/EquipmentSelectHours.vue';


const props = defineProps({
    types: {
        type: Object,
        required: true,
    },
    ateliers: {
        type: Object,
        required: true,
    },
})


const form = useForm({
    name: '',
    maximum_leasing_period: '',
    year_of_manufacture: '',
    allowed_leasing_hours: [],
    date_of_purchase: '',
    type_id: '',
    atelier_id: '',
});

const createEquipment = () => {    
    form.post(route('equipment.store'));
};

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
                                <label for="name" class="block text-sm font-medium text-gray-700">*Name:</label>
                                <Input type="text" id="name" v-model="form.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-600"> {{ form.errors.name }} </div>    
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700" for="role">*Type:</label>
                                <Select v-model="form.type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="type in props.types.data" :key="type.id" :value="type.id">
                                            {{ type.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> 
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-600"> {{ form.errors.name }} </div>    

                            </div>
                            <div class="mb-4">    
                                <label class="block text-sm font-medium text-gray-700" for="role">*Atelier:</label>
                                <Select v-model="form.atelier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a atelier" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="atelier in props.ateliers.data" :key="atelier.id" :value="atelier.id">
                                            {{ atelier.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> 
                                <div v-if="form.errors.atelier_id" class="mt-2 text-sm text-red-600"> {{ form.errors.atelier_id }} </div>    

                            </div>
                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">*Maximum leasing period (in days):</label>
                                <NumberField
                                    id="room"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    :default-value="7"
                                    :step="1"
                                    v-model="form.maximum_leasing_period"
                                    :format-options="{
                                    style: 'decimal',
                                    }"
                                >
                                    <NumberFieldContent class="w-48">
                                        <NumberFieldDecrement />
                                        <NumberFieldInput />
                                        <NumberFieldIncrement />
                                    </NumberFieldContent>
                                </NumberField>
                                <div v-if="form.errors.maximum_leasing_period" class="mt-2 text-sm text-red-600"> {{ form.errors.maximum_leasing_period }} </div> 
                            </div>


                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">*Leasing hours (everyday):</label>
                                <EquipmentSelectHours v-model="form.allowed_leasing_hours" />
                                <div class="text-gray-600 text-xs">Minimum is 0 and maximum is 24</div>
                                <div v-if="form.errors.allowed_leasing_hours" class="mt-2 text-sm text-red-600"> {{ form.errors.allowed_leasing_hours }} </div>    
                            </div>


                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">Year of manufacture:</label>
                                <Input type="text" id="room" v-model="form.year_of_manufacture" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                                <div v-if="form.errors.year_of_manufacture" class="mt-2 text-sm text-red-600"> {{ form.errors.year_of_manufacture }} </div>    

                            </div>

                            <div class="mb-4">
                                <label for="room" class="block text-sm font-medium text-gray-700">Date of purchase:</label>
                                <Input type="date" id="room" v-model="form.date_of_purchase" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                                <div class="text-gray-600 text-xs">Correct Format: DayDay, MonthMonth, YearYearYearYear.</div>
                                <div v-if="form.errors.date_of_purchase" class="mt-2 text-sm text-red-600"> {{ form.errors.date_of_purchase }} </div>    

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