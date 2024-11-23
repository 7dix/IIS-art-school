<script setup lang="ts">

import { defineProps, ref, watch, defineEmits, computed, onMounted } from 'vue';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';


const props = defineProps<{
    modelValue: number[];
}>();

const emit = defineEmits(['update:modelValue']);

const hours = ref(props.modelValue);

interface Range {
  id: number;
  from: number;
  to: number;
}

const ranges = ref<Range[]>([
]);

const loadRanges = () => {
    if (hours.value.length === 0) {
        ranges.value = [{ id: 1, from: 8, to: 16 }];
        return;
    } else {
        hours.value.sort((a, b) => a - b);
        let currentMin = hours.value[0];
        for (let i = 0; i < hours.value.length; i++) {
            if (hours.value[i] + 1 !== hours.value[i + 1]) {
                ranges.value.push({ id: ranges.value.length + 1, from: currentMin, to: hours.value[i] + 1 });
                currentMin = hours.value[i + 1];
            }
        }
    }
    
}



const selectedHours = computed(() => {
    const hoursSet = new Set<number>();
    ranges.value.forEach(range => {
        for (let i = range.from; i < range.to; i++) {
            if (i < 0 || i > 23 ) {
                continue;
            }
            if (typeof i !== 'number') {
                return [];
            }
            hoursSet.add(i);
        }
    });
    return Array.from(hoursSet).sort((a, b) => a - b);
});

watch(selectedHours, (newValue) => {
    emit('update:modelValue', newValue);
});

watch(ranges, (newValue) => {
    newValue.forEach((range) => {
        if (range.from < 0) {
            range.from = 0;
        }
        if (range.to < 0) {
            range.to = 0;
        }
        if (range.to > 24) {
            range.to = 24;
        }
        if (range.from > 24) {
            range.from = 24;
        }
        if (range.from > range.to) {
            range.to = range.from;
        }
        if (range.to < range.from) {
            range.from = range.to;
        }
    });
}, { deep: true });

onMounted(() => {
    loadRanges();
    emit('update:modelValue', selectedHours.value);
});

const removeRange = (range) => {
    if (ranges.value.length === 1) {
        return;
    }
    ranges.value.splice(ranges.value.indexOf(range), 1);

    // Update the id of each range
    ranges.value.forEach((range, index) => {
        range.id = index + 1;
    });
}

</script>
<template>
    <div class="my-4">
        <div v-for="range in ranges" :key="range" class="flex flex-row items-center gap-4 my-2">
            <Input type='number' class="w-24 text-center" v-model="range.from" max="24" min="0"/>
            -
            <Input type='number' class="w-24 text-center" v-model="range.to" max="24" min="0"/>
            <div class="flex flex-row gap-2 items-center">
                <Button
                    v-if="range.id === ranges.length"
                    variant="outline"
                    size="icon"
                    title="First page"
                    class="h-9 w-9 p-0"
                    @click="ranges.push({ id: ranges.length + 1, from: undefined, to: undefined })"
                    ><Icon icon="lucide:plus"/>
                </Button>
                <Button
                    v-if="ranges.length > 1"
                    variant="outline"
                    size="icon"
                    title="First page"
                    class="h-9 w-9 p-0"
                    @click="removeRange(range)"
                    ><Icon icon="lucide:delete"/>
                </Button>
            </div>
        </div>
    </div>
</template>