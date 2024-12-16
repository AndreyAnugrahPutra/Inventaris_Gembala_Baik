<script setup>
import {onBeforeMount, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { 
    Button,
    Toast,
    useConfirm,ConfirmDialog, 
    Popover
} from 'primevue'
// import custom komponen
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
    pageTitle : String
})

onBeforeMount(()=>
{
    localStorage.isSidebarCollapsed==='true' ? isSidebarCollapsed.value = true : isSidebarCollapsed.value = false
})

const isSidebarCollapsed = ref(false)

const toggleSidebar = () =>
{
    isSidebarCollapsed.value = !isSidebarCollapsed.value
    localStorage.isSidebarCollapsed ? localStorage.isSidebarCollapsed = isSidebarCollapsed.value :localStorage.setItem('isSidebarCollapsed', isSidebarCollapsed.value)
}

const openProfileMenu = ref()

const toggleProfileMenu = event =>
{
    openProfileMenu.value.toggle(event)
}

const confirm = useConfirm()

const confirmLogout = () =>
{
    confirm.require({
        message: 'Yakin ingin logout dari aplikasi?',
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Ya'
        },
        accept : () => {
            // localStorage.clear()
            router.post(route('logout'))
        },
    });
}

</script>

<template>
    <!-- toast notifikasi -->
    <Toast class="z-50" position="top-right" group="tr"/>
    <!-- dialog logout -->
    <ConfirmDialog class="w-[24rem]"></ConfirmDialog>
    <!-- #layout utama -->
    <div class="bg-slate-200 flex p-1 min-h-screen overflow-hidden">

        <!-- Sidebar -->
         <Sidebar :isSidebarCollapsed="isSidebarCollapsed"/>
        <!-- konten halaman -->
        <div class="transition-all duration-[450ms] w-full h-full px-1 overflow-hidden" :class="{'ml-[80px]':isSidebarCollapsed,'ml-[200px]':!isSidebarCollapsed}">
            <div class="flex flex-col gap-2">
                <!-- header -->
                <div  class="bg-slate-50 rounded-lg w-full flex justify-between items-center gap-x-2 py-2 px-4 shadow-lg z-10">

                    <div class="flex items-center gap-x-2">
                        <Button :icon="isSidebarCollapsed?'pi pi-bars':'pi pi-angle-left'" rounded severity="secondary" @click="toggleSidebar" size="small" />
    
                        <h1 class="text-[1.3rem] uppercase">
                            {{ props.pageTitle }}
                        </h1>
                    </div>

                    <Button @click="toggleProfileMenu" class="bg-slate-200 py-2 px-3 rounded-full text-center" unstyled>
                        <i class="pi pi-user"></i>
                    </Button>

                    <!-- popover profile menu -->
                    <Popover ref="openProfileMenu">
                        <div class="flex flex-col gap-2 w-[6rem]">
                            <Button label="Profile" icon="pi pi-cog" unstyled class="flex items-center gap-2 text-slate-500"/>
                            <Button @click="confirmLogout()" label="Logout" icon="pi pi-power-off" unstyled class="flex items-center gap-2 text-red-500"/>
                        </div>
                    </Popover>
                </div>
                <!-- header selesai -->

                <!-- body -->   
                <div class="bg-slate-50 rounded-lg w-full p-4 min-h-screen">
                    <slot name="pageContent"/>
                </div>
                <!-- body selesai -->
            </div>
        </div>
        <!-- konten halaman selesai -->
    </div>
</template>

<style scoped>
</style>