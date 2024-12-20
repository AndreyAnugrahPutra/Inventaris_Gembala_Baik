<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';
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

const pageTitle = 'Profile'

const props = defineProps({
    flash : Object,
    dataProfile : Object,
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
            <div class="flex w-full gap-x-[2rem] justify-center items-center">
                <!-- Card Profile -->
                <Card class="w-[32rem] text-xl flex flex-col gap-4 text-slate-800 p-6 rounded-md" style="border-width: 2px" unstyled>
                    <template #header>
                        <div class="flex justify-center">
                            <i class="pi pi-user" style="font-size: 4rem;"></i>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex flex-col">
                            <!-- Nama -->
                            <span>Nama : {{ props.dataProfile?.username }}</span>
                            <!-- Email -->
                            <span>Email : {{ props.dataProfile?.email }}</span>
                            <!-- Unit -->
                            <span>Unit : {{ props.dataProfile?.unit.nama_unit }}</span>
                            <!-- Jumlah Permohonan -->
                            <span>Jumlah Permohonan : {{ props.dataProfile?.barang_keluar_count }}</span>
                        </div>
                    </template>
                </Card>
            </div>
        </template>
    </AuthLayout>
</template>

<style scoped>
</style>