<script setup lang="ts">
import { ref, watch } from "vue";
import { Button } from "@/Components/ui/button";
import { forEachChild } from "typescript";
import { VTFilter } from "@/types";
import { statusClassColor } from "@/Composables/reservationUtils";


const props = defineProps({
    statuses: {
        type: Array<VTFilter>,
        required: true,
    },
});
const statuses = ref(props.statuses);


const changeStatus = (status) => {
    props.statuses.forEach((status_) => {
        if (status === status_){
            status_.operator = true;
        } else {
            status_.operator = '!==';
        }
    });
}

</script>

<template>
    <div class="w-fit my-2" v-if="statuses">
        <b>Status filter</b>
        <div class="flex space-x-2">
            <Button variant="outline" @click="() => statuses.forEach(status => { status.operator = true; })">
                Show all
            </Button>
            <Button
                variant="outline"
                v-for="status in statuses"
                :key="status"
                @click="changeStatus(status)"
                :class="status.operator == true ? statusClassColor(status.value) : 'bg-slate-300 hover:bg-slate-300 hover:text-white'"
                class="hover:shadow-md active:shadow-sm text-white"
            >
                {{ status.value.charAt(0).toUpperCase() + status.value.slice(1) }}
            </Button>
        </div>
    </div>
</template>