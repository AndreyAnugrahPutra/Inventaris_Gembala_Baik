<script setup>
import { onMounted, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import {FilterMatchMode} from '@primevue/core/api'

import {
    Button,
    Column,
    DataTable,
    Dialog,
    useConfirm,
    Toast,useToast,
} from 'primevue'
import AuthLayout from '@/Layouts/AuthLayout.vue'

onMounted(() => 
{
    dataKategoriFix.value = props.dataKategori?.map((p,i) => ({index : i+1, ...p}))
})

const props = defineProps({
    flash : Object,
    dataKategori : Object,
})

const dataKategoriFix =ref([])
const formType = ref()

const toast = useToast()    
const dt = ref()

const showForm = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const confirm = useConfirm()

const pageTitle = 'Kategori Barang'
</script>

<template>
    <Head :title="pageTitle" />
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-[1rem]">
                <Button label="Tambah Kategori" severity="success" @click="showForm = true, formType = 'tambahData'" class="w-[fit-content]" icon="pi pi-tag" size="small"/>

                <DataTable removable-sort striped-rows :value="dataKategoriFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                    <Column header="No" field="index" />
                </DataTable>
            </div>
        </template>
    </AuthLayout>
</template>

<style scoped>
</style>