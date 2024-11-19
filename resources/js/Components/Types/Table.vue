<script setup lang="ts">
import { defineProps, onMounted } from "vue";
import { useVisualTable, VTRender } from "@/Composables/useVisualTable";
import { VTColumn } from "@/types";
import { ref } from "vue";
import { cn } from "@/Utils";
import { h, resolveComponent } from "vue";
import {
    Table,
    TableHeader,
    TableRow,
    TableHead,
    TableBody,
    TableCell,
} from "@/Components/ui/table";
import { Button } from "@/Components/ui/button";
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from "@/Components/ui/pagination";
import axios from "axios";

const props = withDefaults(
    defineProps<{
        types: any[] | any;
    }>(),
    {
        types: {},
    }
);

onMounted(() => {});

const showDialog = ref(false);
const selectedType = ref(null);

const openEditDialog = (type) => {
    selectedType.value = { ...type }; // Clone type data to edit
    showDialog.value = true;
};

const saveType = async (updatedType) => {
    console.log(updatedType.id);
    const response = await axios.put(
        `/api/type/${updatedType.id}`,
        updatedType
    );

    for (let i = 0; i < props.types.data.length; i++) {
        if (props.types.data[i].id === updatedType.id) {
            props.types.data[i] = updatedType;

            props.types.data[i];
            break;
        }
    }

    showDialog.value = false;
};

const closeDialog = () => {
    showDialog.value = false;
};

const columns: VTColumn[] = [
    {
        key: "name",
        header: "Name",
    },
    {
        key: "equipments",
        header: "Equipments",
        renderAs: (item) => {
            return h("span", item.equipments.length);
        },
    },
    {
        key: "ateliers",
        header: "Ateliers",
        renderAs: (item) => {
            return h("span", item.ateliers.length);
        },
    },
    {
        key: "actions",
        header: "Actions",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => openEditDialog(item),
                    class: "bg-blue-500 text-white",
                },
                "Edit"
            );
        },
    },
];

const table = useVisualTable({
    get data() {
        return props.types.data;
    },
    get columns() {
        return columns;
    },
    initialState: {
        pageSize: 10,
        sort: {
            key: "name",
            direction: "asc",
        },
    },
});
</script>
<template>
    <div class="w-full">
        <div class="rounded-md border">
            <div class="relative w-full overflow-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                v-for="column in table.columns.visible"
                                :key="column.key"
                            >
                                {{ column.header }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="row in table.data.paginated"
                            :key="row.id"
                        >
                            <TableCell
                                v-for="column in table.columns.visible"
                                :key="column.key"
                            >
                                <VTRender
                                    :content="column.content()"
                                    :item="row"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <!-- Paginator -->
        <div class="m-4">
            <Pagination
                v-slot="{ page }"
                v-model:page="table.paginator.currentPage"
                :total="table.data.filtered.length"
                :sibling-count="1"
                show-edges
                :default-page="1"
                :itemsPerPage="table.paginator.pageSize"
            >
                <PaginationList
                    v-slot="{ items }"
                    class="flex items-center gap-1"
                >
                    <PaginationFirst />
                    <PaginationPrev />

                    <template v-for="(item, index) in items">
                        <PaginationListItem
                            v-if="item.type === 'page'"
                            :key="index"
                            :value="item.value"
                            as-child
                        >
                            <Button
                                class="w-9 h-9 p-0"
                                :variant="
                                    item.value === page ? 'default' : 'outline'
                                "
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis
                            v-else
                            :key="item.type"
                            :index="index"
                        />
                    </template>

                    <PaginationNext />
                    <PaginationLast />
                </PaginationList>
            </Pagination>
        </div>

        <div></div>

        <!-- Edit Dialog -->
        <EditTypeDialog
            v-if="showDialog"
            :type="selectedType"
            :isOpen="showDialog"
            @save="saveType"
            @close="closeDialog"
        />
    </div>
</template>
