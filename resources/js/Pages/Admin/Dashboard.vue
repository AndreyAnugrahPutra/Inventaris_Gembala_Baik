<script setup>
import { onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
// import komponen primevue
import {
    Card,
    useToast,
    Toast
} from 'primevue'
// import custom komponen
import AuthLayout from '@/Layouts/AuthLayout.vue';

onMounted(() =>
{
    checkNotif()
})

const pageTitle = 'Dashboard'

const props = defineProps({
    flash : Object,
    barangCount : Number,
    usersCount : Number,
})

const toast = useToast()

const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        setTimeout(() =>
        {
            toast.add({
                severity : props.flash.notif_status,
                summary : 'Notifikasi',
                detail : props.flash.notif_message,
                life : 4000,
                group : 'tc'
            })
        },1000)
    }
}

</script>

<template>
    <Toast position="top-center" group="tc"/>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex w-full gap-x-[2rem] items-center">
                <!-- Card Barang -->
                <Card class="w-[14rem] text-white p-6 rounded-md shadow-md bg-slate-500" unstyled>
                    <template #content>
                        <div class="flex justify-between items-center">
                            <i class="pi pi-file" style="font-size: 2rem;"></i>
                            <div class="flex flex-col items-center">
                                <h1>Barang</h1>
                                <h1>{{ props.barangCount??0 }}</h1>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Card Users -->
                <Link :href="route('admin.users.page')">
                    <Card class="w-[14rem] text-white p-6 rounded-md shadow-md bg-sky-500" unstyled>
                        <template #content>
                            <div class="flex justify-between items-center">
                                <i class="pi pi-users" style="font-size: 2rem;"></i>
                                <div class="flex flex-col items-center">
                                    <h1>Users</h1>
                                    <h1>{{ props.usersCount??0 }}</h1>
                                </div>
                            </div>
                        </template>
                    </Card>
                </Link>
            </div>
        </template>
    </AuthLayout>
</template>

<style scoped>
</style>