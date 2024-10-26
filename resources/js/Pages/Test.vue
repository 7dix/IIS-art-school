<template>
    <div>
        <AuthenticatedLayout>
            <h1 class="text-2xl">Test</h1>
            <table>
                <thead>
                    <tr>
                        <th v-for="column in table.columns.visible" :key="column.key">
                            {{ column.header }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in table.data.paginated" :key="row.id">
                        <td v-for="column in table.columns.visible" :key="column.key">
                            <VTRender :content="column.content()" :item="row" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <PrimaryButton @click="table.paginator.prevPage()">Chcu zpatky</PrimaryButton>
            <PrimaryButton @click="table.paginator.nextPage()">Chcu dal</PrimaryButton>
            <pre>{{table}}</pre>
        </AuthenticatedLayout>
    </div>
</template>
<script setup lang="ts">
    import "@/Types/VisualTable.d.ts";
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { useVisualTable, VTRender } from "@/Composables/useVisualTable";
    import { VTColumn } from "@/types";
    import { h, resolveComponent } from "vue";
    import PrimaryButton from "@/Components/PrimaryButton.vue";

    const data = [
        {
            id: 1,
            name: 'John Doe',
            email: 'gey@google.com',
        },
        {
            id: 2,
            name: 'Jane Doe',
            email: 'lopata@lff.cz',
        },
        {
            id: 3,
            name: 'John Smith',
            email: 'fdsa@fgdsf.cz'
        }
    ]

    const columns: VTColumn[] = [
        // {
        //     "key": "id",
        //     "header": "ID",
        // }
        // {
        //     "key": "name",
        //     "header": "Name",
        // },
        // {
        //     "key": "email",
        //     "header": "Email",
        //     renderAs: (item) => {
        //         return h(
        //             PrimaryButton,
        //             {
        //                 onClick: () => {
        //                     console.log(item);
        //                 }
        //             },
        //             item.email
        //         );
        //     },
        // }
    ]

    const table = useVisualTable({
        get data() { 
            return data 
        },
        get columns() { 
            return columns
        },
        options: {
            extendedColumns: true,
        },
        initialState: {
            pageSize: 3,
            sort: {
                key: 'id',
                direction: 'asc'
            }
        }
    });
    table.toggleVisibility('id');
    table.toggleVisibility('name');
    table.toggleVisibility('email');




    
    
</script>