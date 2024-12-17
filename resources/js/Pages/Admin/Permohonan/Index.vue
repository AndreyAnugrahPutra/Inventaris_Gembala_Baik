<script setup>
import { onMounted, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import {FilterMatchMode} from '@primevue/core/api'

import {
    Badge,
    Button,
    Column,
    DataTable,
    Dialog,
    FloatLabel,
    IconField,
    InputIcon,
    InputText,
    useConfirm,
    useToast,
} from 'primevue'

import AuthLayout from '@/Layouts/AuthLayout.vue'

onMounted(() =>
{
    dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
})

const props = defineProps({
    flash : Object,
    dataPermo : Object
})

const checkNotif = () =>
{
    if(props.flash?.notif_status)
    {
        toast.add({
            severity : props.flash.notif_status,
            summary : 'notifikasi',
            detail : props.flash.notif_message,
            life : 4000,
            group : 'tr',
        })   
    }
}

const formType = ref()
const toast = useToast()    
const dt = ref()

const showForm = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const confirm = useConfirm()

const dataPermoFix = ref([])

const pageTitle = 'Permohonan'

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Unit':'Edit Unit'
} 

</script>

<template>
    <Head :title="pageTitle" />
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- tambah permohonan -->
                <Button @click="openForm('tambahData')" class="w-fit" severity="success" label="Tambah Permohonan" icon="pi pi-file" size="small" />
                <!-- Datatable Unit -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable  removable-sort striped-rows :value="dataPermoFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Permohonan</span>
                        </template>
                        <Column sortable header="No" field="index"/>
                        <Column sortable header="Tanggal Permohonan" field="tgl_permo"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
