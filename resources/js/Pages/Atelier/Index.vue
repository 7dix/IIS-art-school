<script setup lang="ts">

const props = defineProps({
    ateliers: {
        type: Object,
        required: true,
    },
})

</script>

<template>
    <div>
        <h1 class="text-2xl font-semibold mb-6">Ateliers</h1>
        <pre>{{ props.ateliers }}</pre>
    </div>
</template>