<script setup>
import { defineProps } from "vue";
import { Card, CardHeader, CardTitle, CardContent } from "@/Components/ui/card";
import { Button } from "@/Components/ui/button";
import { Icon } from "@iconify/vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
    onAddUser: {
        type: Function,
        required: true,
    },
    onRemoveUser: {
        type: Function,
        required: true,
    },
});

const handleAddUser = () => {
    props.onAddUser();
};

const handleRemoveUser = (user) => {
    props.onRemoveUser(user);
};
</script>

<template>
    <Card class="mb-6 w-full max-w-sm">
        <CardHeader class="grid grid-cols-3 items-center">
            <div></div>
            <CardTitle
                class="text-lg font-semibold leading-tight text-gray-800"
            >
                {{ title }}
            </CardTitle>
            <div class="flex justify-end">
                <Button
                    @click="handleAddUser"
                    class="bg-blue-500 text-white hover:bg-blue-700 w-10 h-10 flex items-center justify-center"
                >
                    <Icon icon="mingcute:add-fill" class="w-5 h-5" />
                </Button>
            </div>
        </CardHeader>
        <CardContent class="mt-4">
            <ul class="list-none">
                <li
                    v-for="user in users"
                    :key="user.id"
                    class="flex justify-between items-center py-2 border-b border-gray-200"
                >
                    <span>{{ user.first_name }} {{ user.last_name }}</span>
                    <Button
                        @click="handleRemoveUser(user)"
                        class="bg-red-500 text-white hover:bg-red-700 w-10 h-10 flex items-center justify-center"
                    >
                        <Icon icon="material-symbols:delete" class="w-5 h-5" />
                    </Button>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
