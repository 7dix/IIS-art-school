<script setup lang="ts">
import { computed, defineProps, onMounted } from "vue";
import { useVisualTable, VTRender } from "@/Composables/useVisualTable";
import { VTColumn, VTDisplayOptions, VTFilter } from "@/types";
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
import { Input } from "@/Components/ui/input";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { ScrollArea } from "@/Components/ui/scroll-area";
import { Icon } from "@iconify/vue";

const props = withDefaults(
    defineProps<{
        columns: VTColumn[];
        data: any[] | any;
        displayType?: VTDisplayOptions;
        icon?: string;
        pageSize?: number;
        pageSizes?: number[];
        class?: any;
        extendColumns?: boolean;
        flatData?: boolean;
        filter?: VTFilter[];
        prefix?: string;
        showSelect?: boolean;
        showDisplayOption?: boolean;
        showColumnToggle?: boolean;
        showFilters?: boolean;
        showSearch?: boolean;
        showPagination?: boolean;
        noDataMessage?: string;
    }>(),
    {
        columns: () => [],
        data: () => [],
        displayType: "table",
        icon: "lucide:database",
        pageSize: 10,
        pageSizes: () => [10, 20, 50],
        extendColumns: false,
        flatData: false,
        filter: () => [],
        showSelect: false,
        showDisplayOption: true,
        showColumnToggle: false,
        showFilters: false,
        showSearch: true,
        showPagination: true,
        noDataMessage: "No data available",
    }
);

const checkBoxSelect: VTColumn = {
    key: "checkbox",
    header: () =>
        h(resolveComponent("Checkbox"), {
            checked: table.select.isAllSelected()
                ? true
                : table.select.isIndeterminate()
                ? "indeterminate"
                : false,
            "onUpdate:checked": () => table.select.toggleAll(),
            ariaLabel: "Select all",
        }),
    renderAs: (item: any) =>
        h(resolveComponent("Checkbox"), {
            checked: table.select.isSelected(item.id),
            "onUpdate:checked": () => table.select.toggle(item.id),
            ariaLabel: "Select item",
        }),
    bindToPosition: "checkbox",
    enableSort: false,
    enableFilter: false,
    enableToggle: false,
    enableSearch: false,
};

const localColumns = computed(() => {
    const cols = [...props.columns];
    if (props.showSelect) {
        cols.unshift(checkBoxSelect);
    }
    return cols;
});

// Stavy pro vyhledávání a filtrování
const searchTerm = ref("");
const displayType = ref<VTDisplayOptions>(props.displayType);
const filters = ref<VTFilter[]>([{ column: "", operator: "===", value: "" }]);

// Funkce pro třídění
const sortBy = (key: string) => {
    table.setSort(key);
};

// Current filters
const activeFilters = computed(() =>
    filters.value.filter((f) => f.column && f.operator && f.value)
);

const addFilter = () => {
    if (filters.value.length >= 5) {
        // Max 5 filters
        return;
    }
    filters.value.push({ column: "", operator: "===", value: "" });
};

const updateFilterOptions = (index: number) => {
    filters.value[index].value = "";
};

const removeFilter = (index: number) => {
    filters.value.splice(index, 1);
};

const clearAllFilters = () => {
    filters.value = [{ column: "", operator: "===", value: "" }];
};

// Filter logic
const applySelectFilter = (item: any): boolean => {
    return activeFilters.value.every((filter) => {
        const itemValue = item[filter.column];
        const filterValue = filter.value;

        switch (filter.operator) {
            case "===":
                return (
                    itemValue == filterValue ||
                    String(itemValue).includes(String(filterValue))
                );
            case "!==":
                return (
                    itemValue != filterValue &&
                    !String(itemValue).includes(String(filterValue))
                );
            case ">":
                return itemValue >= filterValue;
            case "<":
                return itemValue <= filterValue;
            case "#":
                return String(itemValue).includes(
                    String(filterValue.split(" ")[0])
                );
            default:
                return true;
        }
    });
};

/**
 * Get filters from query string
 * ?filter-column=value
 * @returns void
 */
const getFiltersFromQueryString = () => {
    const searchParams = new URLSearchParams(window.location.search);
    const queryFilters: VTFilter[] = [];
    searchParams.forEach((value, key) => {
        const column = key.split("-")[1];
        // Remove existing filter for the column
        filters.value = filters.value.filter((f) => f.column !== column);
        queryFilters.push({ column, operator: "===", value });
    });
    filters.value.push(...queryFilters);
};

const getFiltersFromProps = () => {
    if (props.filter.length > 0) {
        filters.value.push(...props.filter);
    }
};

getFiltersFromProps();
getFiltersFromQueryString();

const applySearchFilter = (item: any) => {
    const searchTermLower = searchTerm.value.toLowerCase();
    const searchableColumns = table.columns.relevant.filter(
        (col) => col.enableSearch
    );

    if (searchableColumns.length === 0) {
        return true;
    }

    return searchableColumns.some((col) => {
        const value = item[col.key];
        return (
            value != null &&
            value.toString().toLowerCase().includes(searchTermLower)
        );
    });
};

// Funkce pro přepínání viditelnosti sloupce
const toggleColumnVisibility = (key: string) => {
    table.toggleVisibility(key);
};

// Inicializace tabulky
const table = useVisualTable({
    get data() {
        return props.data;
    },
    get columns() {
        return localColumns.value;
    },
    initialState: {
        pageSize: props.pageSize,
        sort: { key: "id", direction: "asc" },
    },
    state: {
        get filter() {
            return (item: any) =>
                applySearchFilter(item) && applySelectFilter(item);
        },
    },
    options: {
        extendedColumns: props.extendColumns,
        flatData: props.flatData,
    },
});

const itemsPerPage = computed({
    get: () => String(table.paginator.pageSize),
    set: (value: string) => {
        table.paginator.pageSize = Number(value);
    },
});

// pre render filters for columns
const getFiltersForCols = () => {
    let filters: any = [];
    table.columns.filterable.forEach((col: any) => {
        filters[col.key] = table.getFilterForColumn(col.key);
    });

    return filters;
};

let columnsFilters = getFiltersForCols();
</script>
<template>
    <div>
        <Input
            v-model="searchTerm"
            placeholder="Search..."
            class="mb-2 block w-full bg-slate-200 transition focus:bg-slate-300/60 sm:hidden"
            @input="applySearchFilter"
        />
        <div class="mb-5">
            <div class="flex justify-between">
                <div class="space-x-0 sm:space-x-2">
                    <Input
                        v-model="searchTerm"
                        placeholder="Search..."
                        class="hidden w-40 bg-slate-200 transition focus:bg-slate-300/60 sm:inline-block"
                        @input="applySearchFilter"
                    />
                    {{ props.data.length }}
                    {{ table.data.original.length }}
                    {{ table.data.paginated.length }}
                    <div
                        class="inline-flex h-10 items-center gap-2 rounded-md p-2"
                        v-if="props.showFilters"
                    >
                        <Button
                            @click="addFilter"
                            variant="outline"
                            size="icon-sm"
                            class="size-6"
                            :class="
                                filters.length >= 5
                                    ? 'cursor-not-allowed text-slate-400 hover:text-slate-400'
                                    : 'hover:text-action'
                            "
                            ><Icon icon="lucide:x" class="size-3 rotate-45"
                        /></Button>
                        <span class="text-sm font-medium">Filters</span>
                        <Button
                            @click="clearAllFilters"
                            variant="outline"
                            size="icon-sm"
                            class="size-6 hover:text-red-500"
                            ><Icon icon="lucide:x" class="size-3"
                        /></Button>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2">

                    <Button
                        v-if="props.showDisplayOption"
                        size="icon"
                        @click="displayType = 'table'"
                        ><Icon
                            icon="solar:list-arrow-down-minimalistic-linear"
                            class="size-5"
                            :class="{
                                'text-action': displayType === 'table',
                                'text-muted-foreground':
                                    displayType !== 'table',
                            }"
                    /></Button>
                    <Button
                        v-if="props.showDisplayOption"
                        size="icon"
                        @click="displayType = 'grid'"
                        ><Icon
                            icon="solar:posts-carousel-vertical-bold-duotone"
                            class="size-5"
                            :class="{
                                'text-action': displayType === 'grid',
                                'text-muted-foreground': displayType !== 'grid',
                            }"
                    /></Button>
                </div>
            </div>

            <!-- Generovana filtrace -->
            <div
                v-if="props.showFilters"
                class="mb-2 mt-2 flex flex-wrap gap-x-2"
            >
                <div v-for="(filter, index) in filters" :key="index">
                    <div
                        class="mt-1 flex w-min items-center gap-2 rounded bg-slate-200 p-1"
                    >
                        <Select
                            v-model="filter.column"
                            name="column"
                            @update:modelValue="updateFilterOptions(index)"
                        >
                            <SelectTrigger
                                placeholder="Column"
                                class="h-6 w-max min-w-16 rounded border-0 bg-transparent py-0 text-xs font-medium transition-colors hover:bg-slate-100"
                            />
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem
                                        v-for="column in table.columns
                                            .filterable"
                                        :key="column.key"
                                        :value="column.key"
                                    >
                                        {{ column.header }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <Select v-model="filter.operator" name="operator">
                            <SelectTrigger
                                icon="no-icon"
                                class="flex h-6 w-6 justify-center rounded border-0 bg-white p-0 font-mono text-xs font-bold text-action transition-colors hover:bg-slate-100"
                            />
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem value="===">==</SelectItem>
                                    <SelectItem value="!==">!=</SelectItem>
                                    <SelectItem value=">">></SelectItem>
                                    <SelectItem value="<"><</SelectItem>
                                    <SelectItem value="#">#</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <Select v-model="filter.value" name="value">
                            <SelectTrigger
                                placeholder="Value"
                                class="h-6 w-max min-w-16 rounded border-0 bg-transparent py-0 text-xs font-medium text-action transition-colors hover:bg-slate-100"
                            />
                            <SelectContent>
                                <SelectLabel
                                    v-show="!filter.column"
                                    class="pl-2 text-xs font-normal italic text-slate-400"
                                    >No Column Selected</SelectLabel
                                >
                                <SelectGroup>
                                    <SelectItem
                                        v-for="option in columnsFilters[
                                            filter.column
                                        ]"
                                        :key="String(option)"
                                        :value="String(option)"
                                        >{{ option }}</SelectItem
                                    >
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <Button
                            @click="removeFilter(index)"
                            class="size-6 border-slate-300 bg-transparent hover:text-red-500"
                            variant="outline"
                            size="icon-sm"
                            ><Icon icon="lucide:x" class="size-3"
                        /></Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabulka -->
        <template v-if="displayType === 'table'">
            <ScrollArea class="calc-width" orientation="horizontal">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                v-for="column in table.columns.visible"
                                :key="column.key"
                                @click="sortBy(column.key)"
                                class="select-none border-x"
                                :class="{
                                    'cursor-pointer': column.enableSort,
                                    'hover:text-muted-foreground':
                                        !column.enableSort,
                                }"
                            >
                                <div class="flex items-center">
                                    <VTRender
                                        :content="column.header"
                                        :item="table"
                                    />
                                    <Icon
                                        v-if="
                                            table.sortState.value.key ===
                                                column.key && column.enableSort
                                        "
                                        :name="
                                            table.sortState.value.direction ===
                                            'asc'
                                                ? 'solar:double-alt-arrow-up-line-duotone'
                                                : 'solar:double-alt-arrow-down-line-duotone'
                                        "
                                        class="size-4"
                                    />
                                </div>
                            </TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow
                            v-for="row in table.data.paginated"
                            :key="row.id"
                            :class="table.getRowStyle(row)"
                        >
                            <TableCell
                                v-for="column in table.columns.visible"
                                :key="column.key"
                                :class="
                                    column.additionalColumn
                                        ? 'text-slate-400'
                                        : ''
                                "
                            >
                                <VTRender
                                    :content="column.content()"
                                    :item="row"
                                />
                            </TableCell>
                        </TableRow>

                        <TableRow
                            v-if="table.data.paginated.length === 0"
                            :colspan="table.columns.visible.length"
                        >
                            <div
                                class="flex w-full flex-col items-center justify-center gap-5 py-5"
                            >
                                <Icon
                                    name="lucide:database"
                                    class="h-12 w-12 text-muted-foreground"
                                />
                                <span class="mt-2">{{
                                    props.noDataMessage
                                }}</span>
                            </div>
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>
        </template>

        <!-- Grid -->
        <template v-else>
            <div
                class="mb-3 flex items-center justify-between border-b px-3 py-1.5"
            >
                <VTRender :content="table.columns.bind('checkbox')?.header" />
                <VTRender
                    :content="table.columns.bind('action')?.header"
                    :item="table"
                />
            </div>

            <div class="grid-container gap-6">
                <div
                    v-for="row in table.data.paginated"
                    :key="row.id"
                    class="grid rounded-lg p-6 transition"
                    :class="
                        table.getRowStyle(row) ?? 'bg-white hover:bg-white/50'
                    "
                >
                    <div
                        class="relative mb-4 flex items-center gap-3 self-start font-semibold text-action"
                    >
                        <Icon :icon="props.icon" class="size-6" />
                        <VTRender
                            :content="table.columns.bind('name')?.content()"
                            :item="row"
                            class="max-w-[70%]"
                        />

                        <VTRender
                            :content="table.columns.bind('checkbox')?.content()"
                            :item="row"
                            class="absolute right-0 top-0"
                        />
                    </div>

                    <div class="divide-y text-sm font-medium text-slate-600">
                        <div
                            v-for="col in table.columns.unbinded"
                            :key="col.key"
                            class="flex items-center justify-between px-3 py-2"
                        >
                            <span class="">{{ col.header }}</span>
                            <span class="font-normal text-slate-500">
                                <VTRender
                                    :content="col.content()"
                                    :item="row"
                                />
                            </span>
                        </div>
                    </div>

                    <div class="mt-2 flex items-center self-end">
                        <VTRender
                            :content="table.columns.bind('status')?.content()"
                            :item="row"
                        />
                        <VTRender
                            :content="table.columns.bind('action')?.content()"
                            :item="row"
                            class="ml-auto"
                        />
                    </div>
                </div>
            </div>
        </template>

        <!-- paginator -->
        <div
            class="mt-4 flex flex-col justify-between gap-1 sm:flex-row sm:items-center"
        >
            <div class="flex items-center gap-3">
                <div
                    class="whitespace-nowrap text-sm font-medium text-foreground"
                >
                    Page {{ table.paginator.currentPage }} of
                    {{ table.paginator.totalPages }}
                </div>

                <Select v-model="itemsPerPage">
                    <SelectTrigger class="h-9 w-[70px]">
                        {{ table.paginator.pageSize }}
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-bind="table.paginator.pageSize"
                                v-for="size in pageSizes"
                                :key="size"
                                :value="String(size)"
                            >
                                {{ size }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <div class="space-x-2">
                    <Button
                        variant="outline"
                        size="icon"
                        title="First page"
                        class="h-9 w-9 p-0"
                        @click="table.paginator.currentPage = 1"
                        :disabled="table.paginator.first"
                        ><Icon icon="solar:double-alt-arrow-left-linear"
                    /></Button>

                    <Button
                        variant="outline"
                        size="icon"
                        class="h-9 w-9 p-0"
                        title="Previous page"
                        @click="table.paginator.prevPage()"
                        :disabled="table.paginator.first"
                        ><Icon icon="solar:alt-arrow-left-linear"
                    /></Button>

                    <Button
                        variant="outline"
                        size="icon"
                        class="h-9 w-9 p-0"
                        title="Next page"
                        @click="table.paginator.nextPage()"
                        :disabled="table.paginator.last"
                        ><Icon icon="solar:alt-arrow-right-linear"
                    /></Button>

                    <Button
                        variant="outline"
                        size="icon"
                        class="h-9 w-9 p-0"
                        title="Last page"
                        @click="
                            table.paginator.currentPage =
                                table.paginator.totalPages
                        "
                        :disabled="table.paginator.last"
                        ><Icon icon="solar:double-alt-arrow-right-linear"
                    /></Button>
                </div>
            </div>
            <span
                v-show="
                    table.data.filtered.length !== table.data.original.length
                "
                class="text-normal text-sm italic text-slate-500"
                >{{ table.data.filtered.length }} items filtered</span
            >
        </div>
    </div>
</template>
