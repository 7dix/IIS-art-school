<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'
import { statusClassColor, parseDateTime } from "@/Composables/reservationUtils";
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { ref } from 'vue';
import { useToast } from '@/components/ui/toast/use-toast'



const props = defineProps({
    reservation: {
        type: Object,
        required: true,
    },
})

const { toast } = useToast()
const state = ref(props.reservation.status);

const changeStatus = (status) => {
    axios.post(route('reservation.state-update', props.reservation.id), {
        status: status
    }).then(response => {
        state.value = status;
        toast({
            title: 'Success',
            description: response.data.message
        });
        console.log(response);
    }).catch(error => {
        toast({
            title: 'Error',
            variant: "destructive",
            description: error.response.data.message
        });
        console.error(error);
    });
}



</script>

<template>
    <Head title="Reservation" />
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink :href="route('reservation.index')">Reservations</BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbLink class="font-bold">Reservation #{{reservation.id}}</BreadcrumbLink>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg ">
                    <!-- Actions -->
                    <div class="flex items-start justify-between p-4">
                        <h2
                            class="text-xl font-semibold leading-tight text-gray-800"
                        >
                            Reservation detail
                        </h2>
                        <div class="flex items-end justify-end space-x-4">
                            <Button v-if="state === 'pending'"
                                variant="outline"
                                class="btn-accept"
                                @click="changeStatus('approved')"
                            >
                                Accept
                            </Button>
                            <Button v-if="state === 'pending'"
                                variant="outline"
                                class="btn-reject"
                                @click="changeStatus('rejected')"
                                >
                                Reject
                            </Button>
                            <Button v-if="state === 'approved'"
                                variant="outline"
                                class="btn-accept"
                                @click="changeStatus('ongoing')"
                            >
                                Hand Over Equipment
                            </Button>
                            <Button v-if="state === 'ongoing'"
                                variant="outline"
                                class="btn-accept"
                                @click="changeStatus('completed')"
                            >
                                Return Equipment
                            </Button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                        <!-- Reservation Info -->
                        <div class="info-category">
                            <h3 class="info-category-label">Info</h3>
                            <div>
                                <div class="info-box">
                                    <div class="info-box-label">Status</div>
                                    <div class="info-box-value">
                                        <Badge :class="statusClassColor(state)">
                                            {{ state }}
                                        </Badge>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">Leasing period</div>
                                    <div class="flex space-x-2 items-center">
                                        <div class="info-box-value">{{ parseDateTime(reservation.start_date) }}</div>
                                        <Icon icon="solar:arrow-right-broken" />
                                        <div class="info-box-value">{{ parseDateTime(reservation.end_date) }}</div>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">Reservation created</div>
                                    <div class="info-box-value">{{ parseDateTime(reservation.created_at) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Info -->
                        <div class="info-category row-span-2">
                            <h3 class="info-category-label">Equipment</h3>
                            <div>
                                <div class="info-box">
                                    <div class="info-box-label">Name</div>
                                    <div class="info-box-value">{{ reservation.equipment.name }}</div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">Type</div>
                                    <div class="info-box-value">{{ reservation.equipment.type.name }}</div>
                                </div>
                                
                                <div class="info-box">
                                    <div class="info-box-label">Atelier</div>
                                    <a 
                                        :href="route('ateliers.dashboard', reservation.equipment.atelier.id)" 
                                        class="font-semibold info-box-value bg-blue-100 px-4 rounded">
                                        {{ reservation.equipment.atelier.name }}
                                    </a>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">Location</div>
                                    <div class="info-box-value">{{ reservation.equipment.atelier.room }}</div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">Owner</div>
                                    <div class="info-box-value">{{ reservation.equipment.owner.first_name }} {{ reservation.equipment.owner.last_name }} | {{ reservation.equipment.owner.email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="info-category">
                            <h3 class="info-category-label">Who is leasing</h3>
                            <div>
                                <div class="info-box">
                                    <div class="info-box-label">Name</div>
                                    <div class="info-box-value">{{reservation.user.first_name}} {{reservation.user.last_name}}</div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box-label">E-mail</div>
                                    <div class="info-box-value">{{reservation.user.email}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>

<style scoped>
    .btn-accept{
        @apply bg-green-500 text-white;
    }
    .btn-reject{
        @apply bg-red-500 text-white;
    }

    .info-category {
        @apply flex flex-col;
    }

    .info-category-label {
        font-size: 1.25rem;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-box {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-box-label {
        font-weight: 600;
    }
</style>