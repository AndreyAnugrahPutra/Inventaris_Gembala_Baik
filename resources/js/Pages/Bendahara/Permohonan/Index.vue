<script setup>
import { onMounted, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import {FilterMatchMode} from '@primevue/core/api'

import {
    Button,
    Column,
    DataTable,
    DatePicker,
    Dialog,
    FileUpload,
    FloatLabel,
    IconField,
    Image,
    InputIcon,
    InputNumber,
    InputText,
    Select,
    useConfirm,
    useToast,
} from 'primevue'

import AuthLayout from '@/Layouts/AuthLayout.vue'

onMounted(() =>
{
    checkNotif()
    dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
})

const props = defineProps({
    flash : Object,
    dataPermo : Object,
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

const statusPermo = [
    {status : 'diterima'},
    {status : 'ditolak'},
    {status : 'diproses'},
]

const confirm = useConfirm()

const dataPermoFix = ref([])

const pageTitle = 'Permohonan'

const permoFormDisabled = useForm({
    tgl_permo : null,
    jumlah_per : null,
    nama_brg : null,
})

const permoForm = useForm({
    id_permo : null,
    status : null,
    id_dp : null,
    id_brg : null,
    jumlah_per : null,
    jumlah_setuju : null,
    ket_permo : null,
})

const exportCSV = () => dt.value.exportCSV()

const hideForm = () =>
{
    showForm.value = false
    permoForm.reset()
    permoForm.clearErrors()
}

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    router.visit(route('bendahara.permohonan.page'))

}

const editPermo = (idx) => 
{
    formType.value = 'Validasi Permohonan'

    permoForm.id_permo = dataPermoFix.value[idx-1]['id_permo']
    permoForm.status = dataPermoFix.value[idx-1]['status']
    permoForm.id_dp = dataPermoFix.value[idx-1]['details'][0].id_dp
    permoForm.id_brg = dataPermoFix.value[idx-1]['details'][0].id_brg
    permoForm.ket_permo = dataPermoFix.value[idx-1]['details'][0].ket_permo
    permoForm.jumlah_per = dataPermoFix.value[idx-1]['details'][0].jumlah_per
    permoForm.jumlah_setuju = dataPermoFix.value[idx-1]['details'][0].jumlah_setuju

    permoFormDisabled.nama_brg = dataPermoFix.value[idx-1]['details'][0].barang.nama_brg
    permoFormDisabled.tgl_permo = dataPermoFix.value[idx-1]['tgl_permo']
    permoFormDisabled.jumlah_per = dataPermoFix.value[idx-1]['details'][0].jumlah_per

    showForm.value = true
}


const terimaPermo = () => {
    confirm.require({
        message: `Terima Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Terima',
            severity: 'info'
        },
        accept: () => {
            permoForm.post(route('bendahara.permohonan.terima'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const tolakPermo = () => {
    confirm.require({
        message: `Tolak Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Tolak',
            severity: 'danger'
        },
        accept: () => {
            permoForm.post(route('bendahara.permohonan.terima'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

</script>

<template>
    <Head :title="pageTitle" />
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- Dialog bukti -->
                <Dialog :header="formType" class="w-[36rem]" @hide="hideForm()" v-model:visible="showForm" modal>
                    <form @submit.prevent class="flex flex-wrap gap-y-8 gap-x-20 items-center my-1" autocomplete="off">
                         <!-- Tanggal Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <DatePicker disabled class="w-[12.5rem]"  inputId="tgl" v-model="permoFormDisabled.tgl_permo" dateFormat="dd-mm-yy"/>
                                <label for="tgl">Tanggal Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoFormDisabled.errors.tgl_permo">
                                {{ permoFormDisabled.errors.tgl_permo }}
                            </span>
                        </div>
                        <!-- Barang -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText disabled inputId="barang" class="w-[12.5rem]" v-model="permoFormDisabled.nama_brg"/>
                                <label for="barang">Nama Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoFormDisabled.errors.nama_brg">
                                {{ permoFormDisabled.errors.nama_brg }}
                            </span>
                        </div>
                        <!-- Jumlah Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputNumber disabled inputId="jum_per" class="w-[12.5rem]" v-model="permoFormDisabled.jumlah_per"/>
                                <label for="jum_per">Jumlah Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoFormDisabled.errors.jumlah_per">
                                {{ permoFormDisabled.errors.jumlah_per }}
                            </span>
                        </div>
                        <!-- Jumlah Setuju Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputNumber inputId="jum_per" class="w-[12.5rem]" v-model="permoForm.jumlah_setuju"/>
                                <label for="jum_per">Jumlah Setuju</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.jumlah_setuju">
                                {{ permoForm.errors.jumlah_setuju }}
                            </span>
                        </div>
                        <!-- status permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <Select inputId="status" class="w-[12.5rem]" v-model="permoForm.status" :options="statusPermo" optionLabel="status" optionValue="status"/>
                                <label for="status">Status Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.status">
                                {{ permoForm.errors.status }}
                            </span>
                        </div>
                        <!-- Keterangan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText inputId="barang" class="w-[12.5rem]" v-model="permoForm.ket_permo"/>
                                <label for="barang">Keterangan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.ket_permo">
                                {{ permoForm.errors.ket_permo }}
                            </span>
                        </div>
                        <div class="flex items-center gap-x-2 w-full">
                            <Button @click="terimaPermo()" label="Terima" v-if="formType==='Validasi Permohonan'"/>
                            <Button @click="tolakPermo()" label="Tolak" severity="danger" v-if="formType==='Validasi Permohonan'"/>
                            <Button @click="hideForm()" label="Batal" severity="contrast" outlined/>
                        </div>
                    </form>
                </Dialog>
               
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable scrollable  removable-sort striped-rows :value="dataPermoFix" dataKey="id_permo" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Permohonan" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Permohonan</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Tanggal Permohonan" field="tgl_permo" class="w-4"/>
                        <Column sortable header="Nama Barang" class="w-4">
                             <template #body="{data}">
                                {{ data.details[0].barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan" class="w-4">
                            <template #body="{data}">
                               {{ data.details[0].jumlah_per}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" class="w-4">
                            <template #body="{data}">
                                {{ data.details[0].jumlah_setuju??'Menunggu Validasi Bendahara'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" class="w-4">
                            <template #body="{data}">
                                {{ data.details[0].barang.satuan}}
                            </template>
                        </Column>
                        <Column sortable header="Bukti" class="w-4">
                            <template #body="{data}">
                                <div class="size-20 overflow-hidden border rounded" v-if="data?.bukti_permo">
                                    <Image :src="data?.bukti_permo" class="size-full" preview />
                                </div>
                                <span class="text-sm" v-else>Tidak ada foto</span>
                            </template>
                        </Column>
                        <Column sortable header="Status" field="status" class="w-4"/>
                        <Column sortable header="Keterangan" style="min-width: 200px">
                            <template #body="{data}">
                                {{ data.details[0].ket_permo??'Tidak ada keterangan'}}
                            </template>
                        </Column>
                        <Column header="Action" frozen alignFrozen="right" style="min-width: 1rem">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.details[0].jumlah_setuju" @click="editPermo(data.index)" icon="pi pi-pen-to-square" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
