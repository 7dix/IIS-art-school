<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb'
import { statusClassColor, parseDateTime } from "@/Composables/reservationUtils";
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { ref } from 'vue';



const props = defineProps({
    reservation: {
        type: Object,
        required: true,
    },
})

const state = ref(props.reservation.status);

</script>

<template>
    <Head title="Reservation" />
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink href="/reservation">Reservations</BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbLink class="font-bold" href="/reservation/{{ reservation.id }}">Reservation #{{reservation.id}}</BreadcrumbLink>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Reservation detail
            </h2>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg ">
                    <!-- Actions -->
                    <div>
                        <div class="flex justify-end p-4">
                            <Button
                                variant="outline"
                                class="btn-accept"
                                @click="() => state = 'approved'"
                            >
                                Accept
                            </Button>
                            <Button
                                variant="outline"
                                class="btn-reject"
                                @click="() => state = 'rejected'"
                            >
                                Reject
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
                                        <Badge :class="statusClassColor(reservation.status)">
                                            {{ reservation.status }}
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
                                    <div class="info-box-value">{{ reservation.equipment.atelier.name }}</div>
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
        <pre>{{ reservation }}</pre>
        
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

    .info-box-value {
        font-weight: 400;
    }
</style>