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
        equipments: any[] | any;
        types: any[] | any;
    }>(),
    {
        equipments: {},
        types: {}
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
        "key": "type",
        "header": "Type",
        renderAs: (item) => {
            return h('span', item.type.name)
        }
    },

    {
        "key": "owner",
        "header": "Owner",
        renderAs: (item) => {
            return h('span', item.owner.firstName + item.owner.lastName)
        }
    },
    {
        "key": "owner_id",
        "header": "Owner Id",
        renderAs: (item) => {
            return h('span', item.owner_id)
        }
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
        return props.equipments.data
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
            <!-- <pre>{{props.ateliers}}</pre> -->
        </div>
    </div>
    <!-- <div class="w-full">
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableRow :data-state="row.getIsSelected() && 'selected'">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="row.getIsExpanded()">
                                <TableCell :colspan="row.getAllCells().length">
                                    {{ JSON.stringify(row.original) }}
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>

                    <TableRow v-else>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                {{ table.getFilteredSelectedRowModel().rows.length }} of
                {{ table.getFilteredRowModel().rows.length }} row(s) selected.
            </div>
            <div class="space-x-2">
                <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()">
                    Previous
                </Button>
                <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    Next
                </Button>
            </div>
        </div>
    </div> -->
</template>