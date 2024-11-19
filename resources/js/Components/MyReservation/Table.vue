<script setup lang="ts">
import { defineProps, onMounted } from 'vue';
import { useVisualTable, VTRender } from "@/Composables/useVisualTable";
import { VTColumn } from "@/types";
import { ref } from 'vue';
import { cn } from '@/Utils'
import { h, resolveComponent } from "vue";
import { 
    Table, 
    TableHeader, 
    TableRow, 
    TableHead, 
    TableBody, 
    TableCell 
} from '@/Components/ui/table'
import { Button } from '@/Components/ui/button'
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/Components/ui/pagination'


const props = withDefaults(
    defineProps<{
        reservations: any[] | any;
    }>(),
    {
        reservations: {}
    }
);

onMounted(() => {
    
})
const columns: VTColumn[] = [
    {
        "key": "name",
        "header": "Name",
    },
    {
        "key": "room",
        "header": "Room",
    },
    {
        "key": "manager",
        "header": "Manager",
        renderAs: (item) => {
            return h('span', item.manager.first_name + ' ' + item.manager.last_name)
        }
    },
    {
        "key": "users",
        "header": "Students",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    }
                },
                item.users.length
            );
        },
    },
    {
        "key": "equipments",
        "header": "Equipments",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                },
                item.equipments.length
            );
        },
    },
    {
        "key": "actions",
        "header": "Actions",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => {
                        console.log(item);
                    },
                    class: 'bg-blue-500 text-white'
                },
                'Edit'
            );
        },
    }
]

const table = useVisualTable({
    get data() { 
        return props.reservations.data
    },
    get columns() { 
        return columns
    },
    initialState: {
        pageSize: 10,
        sort: {
            key: 'name',
            direction: 'asc'
        }
    }
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
                            v-for="column in table.columns.visible" :key="column.key"
                            >
                                {{ column.header }}    
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="row in table.data.paginated" :key="row.id">
                            <TableCell v-for="column in table.columns.visible" :key="column.key">
                                <VTRender :content="column.content()" :item="row" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <!-- Paginator -->
        <div class="m-4">
            <Pagination v-slot="{ page }" v-model:page="table.paginator.currentPage" :total="table.data.filtered.length" :sibling-count="1" show-edges :default-page="1" :itemsPerPage="table.paginator.pageSize">
                <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                    <PaginationFirst />
                    <PaginationPrev />
              
                    <template v-for="(item, index) in items">
                      <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                        <Button class="w-9 h-9 p-0" :variant="item.value === page ? 'default' : 'outline'">
                          {{ item.value }}
                        </Button>
                      </PaginationListItem>
                      <PaginationEllipsis v-else :key="item.type" :index="index" />
                    </template>
              
                    <PaginationNext />
                    <PaginationLast />
                  </PaginationList>
            </Pagination>
        </div>

        <div>
            <!-- <pre>{{props.reservations}}</pre> -->
        </div>
    </div>
</template>