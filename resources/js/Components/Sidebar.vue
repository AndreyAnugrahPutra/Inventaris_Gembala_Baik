<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
    Button,
    Fluid,
    PanelMenu
} from 'primevue'

import {adminMenu, adminPanel} from './Composables/sidebarLists'


const props = defineProps({
    isSidebarCollapsed : Boolean,
})

const openPanel = ref(false)

const adminMenus = ref(adminMenu)
const adminPanels = ref(adminPanel)

const closePanel = () =>
{
    setTimeout(() => openPanel.value = false,500)
}

</script>

<template>
    <div class="transition-all ease-in-out duration-[450ms] rounded-lg h-[99vh] p-2 flex flex-col bg-gradient-to-b from-sky-500 to-sky-400 gap-[2rem] justify-between fixed" :class="{'w-[80px]':props.isSidebarCollapsed,'w-[200px]':!props.isSidebarCollapsed}">
       
        <div class="flex flex-col gap-4 text-lg items-center">
            <Button v-if="$page.props.auth.user.id_role === 1" v-for="menu in adminMenus" :key="menu.route" :label="props.isSidebarCollapsed?null:menu.label" :icon="menu.icon" class="w-full p-1 flex items-center justify-center" :class="{'bg-slate-100 text-sky-500 rounded' : route().current(menu.route),'text-slate-50' : !route().current(menu.route),'gap-0 rounded-lg':props.isSidebarCollapsed,'gap-2':!props.isSidebarCollapsed}" @click="router.visit(route(menu.route))" unstyled/>

            <PanelMenu @panel-open="openPanel=true" @panel-close="closePanel()" class="w-full transition-all"  :model="adminPanels" v-if="$page.props.auth.user.id_role === 1" :class="{'rounded bg-slate-100 text-sky-500':openPanel}" unstyled>
                <template #item="{item}">
                    <Button v-if="item.route" :label="props.isSidebarCollapsed?null:item.label" :icon="item.icon" class="w-full p-1 flex items-center justify-center" :class="{'gap-x-2':!props.isSidebarCollapsed}" unstyled/>
                    <a v-else class="flex justify-center items-center cursor-pointer rounded" :target="item.target" :class="{'gap-0':props.isSidebarCollapsed,'gap-x-2':!props.isSidebarCollapsed,'bg-slate-100 text-sky-500':openPanel,'text-white':!openPanel}">
                        <span class="ml-2" :class="{'hidden':props.isSidebarCollapsed}">{{ item.label }}</span>
                        <span v-if="item.items" class="pi pi-angle-down text-primary " />
                    </a>
                </template>
            </PanelMenu>
        </div>
    </div>
</template>