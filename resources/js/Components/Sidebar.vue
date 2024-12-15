<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
    Button
} from 'primevue'

import {adminMenu} from './Composables/SidebarLists'


const props = defineProps({
    isSidebarCollapsed : Boolean,
})

const adminMenus = ref(adminMenu)

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
//     })
// }

</script>

<template>
    <div class="transition-all ease-in-out duration-[450ms] rounded-lg h-[99vh] p-2 flex flex-col bg-gradient-to-b from-sky-500 to-sky-400 gap-[2rem] justify-between fixed" :class="{'w-[80px]':props.isSidebarCollapsed,'w-[200px]':!props.isSidebarCollapsed}">
       
        <div class="flex flex-col gap-4 text-lg items-center">
            <Button v-if="$page.props.auth.user.id_role === 1" v-for="menu in adminMenus" :key="menu.route" :label="props.isSidebarCollapsed?null:menu.label" :icon="menu.icon" class="w-full p-1 flex items-center justify-center" :class="{'bg-slate-100 text-sky-500 rounded' : route().current(menu.route),'text-slate-50' : !route().current(menu.route),'gap-0 rounded-lg':props.isSidebarCollapsed,'gap-2':!props.isSidebarCollapsed}" @click="router.visit(route(menu.route))" unstyled/>
        </div>
        <!-- logout button -->
        <!-- <Button icon="pi pi-power-off" class="w-full text-slate-100 bg-red-500 rounded p-1" :class="{'rounded-lg':props.isSidebarCollapsed}" @click="confirmLogout" unstyled/> -->
    </div>
</template>