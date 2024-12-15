<script setup>
import { router } from '@inertiajs/vue3';
import { 
    Button
} from 'primevue';


const props = defineProps({
    isSidebarCollapsed : Boolean,
})

// const confirm = useConfirm()

// const confirmLogout = () =>
// {
//     confirm.require({
//         message: 'Yakin ingin logout dari aplikasi?',
//         header: 'Peringatan',
//         icon: 'pi pi-exclamation-triangle',
//         rejectProps: {
//             label: 'Batal',
//             severity: 'secondary',
//             outlined: true
//         },
//         acceptProps: {
//             label: 'Ya'
//         },
//         accept : () => {
//             // localStorage.clear()
//             router.post(route('logout'))
//         },
//     });
// }

</script>

<template>
    <div class="transition-all ease-in-out duration-[450ms] rounded-lg h-[99vh] p-2 flex flex-col bg-gradient-to-b from-sky-500 to-sky-400 gap-[2rem] justify-between fixed" :class="{'w-[80px]':props.isSidebarCollapsed,'w-[200px]':!props.isSidebarCollapsed}">
       
        <div class="flex flex-col gap-4 text-lg items-center">
            <Button :label="props.isSidebarCollapsed?null:'Dashboard'" icon="pi pi-home" class="w-full p-1 flex items-center justify-center" :class="{'bg-slate-100 text-sky-500 rounded' : route().current('dashboard'),'text-slate-50' : !route().current('dashboard'),'gap-0 rounded-lg':props.isSidebarCollapsed,'gap-2':!props.isSidebarCollapsed}" @click="router.visit(route('dashboard'))" unstyled/>

            <Button :label="props.isSidebarCollapsed?'':'Barang'" icon="pi pi-box" class="w-full p-1 flex items-center justify-center" :class="{'bg-slate-100 text-sky-500 rounded' : route().current('barang'),'text-slate-50':!route().current('barang'),'gap-0 rounded-lg':props.isSidebarCollapsed,'gap-2':!props.isSidebarCollapsed}"@click="router.visit(route('barang'))" unstyled/>

            <Button :label="props.isSidebarCollapsed?'':'Users'" icon="pi pi-users" class="w-full p-1 flex items-center justify-center" :class="{'bg-slate-100 text-sky-500 rounded' : route().current('users.page'),'text-slate-50':!route().current('users.page'),'gap-0 rounded-lg':props.isSidebarCollapsed,'gap-2':!props.isSidebarCollapsed}" @click="router.visit(route('users.page'))" unstyled/>
        </div>
        <!-- logout button -->
        <!-- <Button icon="pi pi-power-off" class="w-full text-slate-100 bg-red-500 rounded p-1" :class="{'rounded-lg':props.isSidebarCollapsed}" @click="confirmLogout" unstyled/> -->
    </div>
</template>