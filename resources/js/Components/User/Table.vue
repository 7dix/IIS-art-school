<script setup lang="ts">
import { defineProps, onMounted } from 'vue';
import { useVisualTable, VTRender } from "@/Composables/useVisualTable";
import { VTColumn } from "@/types";
import EditUserDialog from './UserEditDialog.vue';
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
} from '@/Components/ui/table';
import { Button } from '@/Components/ui/button';
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/Components/ui/pagination';
import axios from 'axios'; 


const props = withDefaults(
    defineProps<{
        users: any[] | any;
        roles: any[] | any;
    }>(),
    {
        users: {},
        roles: {}
    }
);

onMounted(() => {
    
})

const showDialog = ref(false);
const selectedUser = ref(null);

const openEditDialog = (user) => {
  selectedUser.value = { ...user };  // Clone user data to edit
  showDialog.value = true;
};

const saveUser = async (updatedUser) => {
  console.log(updatedUser.id);
  const response = await axios.put(`/api/user/${updatedUser.id}`, updatedUser);

  for (let i = 0; i < props.users.data.length; i++) {
    if (props.users.data[i].id === updatedUser.id) {
      props.users.data[i] = updatedUser;
      
      props.users.data[i];
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
        "key": "full_name",
        "header": "Full Name",
        renderAs: (item) => {
            return h('span', item.first_name + ' ' + item.last_name)
        }
    },
    {
        "key": "email",
        "header": "Email",
    },
    {
        "key": "role",
        "header": "Role",
        renderAs: (item) => {
            return h('span', item.role.name)
        }
    },
    {
        "key": "actions",
        "header": "Actions",
        renderAs: (item) => {
            return h(
                Button,
                {
                    onClick: () => openEditDialog(item),
                    class: 'bg-blue-500 text-white'
                },
                'Edit'
            );
        },
    }
]

const table = useVisualTable({
    get data() { 
        return props.users.data
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
        </div>

          <!-- Edit Dialog -->
          <EditUserDialog 
          v-if="showDialog" 
          :user="selectedUser" 
          :isOpen="showDialog" 
          :roles="props.roles"
          @save="saveUser" 
          @close="closeDialog"
        />
    </div>

</template>