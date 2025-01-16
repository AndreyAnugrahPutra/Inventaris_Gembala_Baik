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
    dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
})

const props = defineProps({
    flash : Object,
    dataBarangKeluar : Object,
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
    {status : 'ditolak'},
    {status : 'disetujui'},
]

const confirm = useConfirm()

const dataBarangKeluarFix = ref([])

const pageTitle = 'Barang Keluar'

const permoFormDisabled = useForm({
    tgl_bk : null,
    jum_bk : null,
    nama_brg : null,
})

const permoForm = useForm({
    id_bk : null,
    status_bk : null,
    id_dbk : null,
    id_brg : null,
    jum_bk : null,
    jum_setuju_bk : null,
    ket_bk : null,
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
    router.visit(route('admin.barang_keluar.page'))

}

const editPermo = (idx) => 
{
    formType.value = 'Validasi Permohonan'

    permoForm.id_bk = dataBarangKeluarFix.value[idx-1]['id_bk']
    permoForm.status = dataBarangKeluarFix.value[idx-1]['status']
    permoForm.id_dbk = dataBarangKeluarFix.value[idx-1]['details'].id_dbk
    permoForm.id_brg = dataBarangKeluarFix.value[idx-1]['details'].id_brg
    permoForm.ket_bk = dataBarangKeluarFix.value[idx-1]['details'].ket_bk
    permoForm.jum_bk = dataBarangKeluarFix.value[idx-1]['details'].jum_bk
    permoForm.jum_setuju_bk = dataBarangKeluarFix.value[idx-1]['details'].jum_setuju_bk

    permoFormDisabled.nama_brg = dataBarangKeluarFix.value[idx-1]['details'].barang.nama_brg
    permoFormDisabled.tgl_bk = dataBarangKeluarFix.value[idx-1]['tgl_bk']
    permoFormDisabled.jum_bk = dataBarangKeluarFix.value[idx-1]['details'].jum_bk

    showForm.value = true
}


const validasiPermo = () => {
    confirm.require({
        message: `Validasi Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Validasi',
            severity: 'info'
        },
        accept: () => {
            permoForm.post(route('admin.barang_keluar.validasi'), {
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
                                <DatePicker disabled class="w-[12.5rem]"  inputId="tgl" v-model="permoFormDisabled.tgl_bk" dateFormat="dd-mm-yy"/>
                                <label for="tgl">Tanggal Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoFormDisabled.errors.tgl_bk">
                                {{ permoFormDisabled.errors.tgl_bk }}
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
                                <InputNumber disabled inputId="jum_per" class="w-[12.5rem]" v-model="permoFormDisabled.jum_bk"/>
                                <label for="jum_per">Jumlah Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoFormDisabled.errors.jum_bk">
                                {{ permoFormDisabled.errors.jum_bk }}
                            </span>
                        </div>
                        <!-- Jumlah Setuju Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputNumber :disabled="permoForm.status_bk==='ditolak'" inputId="jum_per" class="w-[12.5rem]" v-model="permoForm.jum_setuju_bk"/>
                                <label for="jum_per">Jumlah Setuju</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.jum_setuju_bk">
                                {{ permoForm.errors.jum_setuju_bk }}
                            </span>
                        </div>
                        <!-- status permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <Select inputId="status" class="w-[12.5rem]" v-model="permoForm.status_bk" :options="statusPermo" optionLabel="status" optionValue="status"/>
                                <label for="status">Status Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.status_bk">
                                {{ permoForm.errors.status_bk }}
                            </span>
                        </div>
                        <!-- Keterangan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText inputId="barang" class="w-[12.5rem]" v-model="permoForm.ket_bk"/>
                                <label for="barang">Keterangan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.ket_bk">
                                {{ permoForm.errors.ket_bk }}
                            </span>
                        </div>
                        <div class="flex items-center gap-x-2 w-full">
                            <Button @click="validasiPermo()" label="Validasi" v-if="formType==='Validasi Permohonan'"/>
                            <Button @click="hideForm()" label="Batal" severity="danger" outlined/>
                        </div>
                    </form>
                </Dialog>
               
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable exportFilename="data-barang-keluar" scrollable  removable-sort striped-rows :value="dataBarangKeluarFix" dataKey="id_bk" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Permohonan Barang Keluar" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Permohonan</span>
                        </template>
                        <Column :exportable="false" sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Tanggal Permohonan" field="tgl_bk" class="w-4"/>
                        <Column sortable header="Nama Pemohon" field="user.username" class="w-4"/>
                        <Column sortable header="Nama Unit" field="user.unit.nama_unit" class="w-4"/>
                        <Column sortable header="Nama Barang" field="details.barang.nama_brg" filterField="details.barang.nama_brg" class="w-4">
                             <template #body="{data}">
                                {{ data.details.barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Status" field="status_bk" class="w-4"/>
                        <Column sortable header="Jumlah Permohonan" field="details.jum_bk" filterField="details.jum_bk" class="w-4">
                            <template #body="{data}">
                               {{ data.details.jum_bk}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" field="details.jum_setuju_bk" filterField="details.jum_setuju_bk" class="w-4">
                            <template #body="{data}">
                                {{ data.details.jum_setuju_bk??'Menunggu Validasi'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" field="details.barang.satuan" filterField="details.barang.satuan" class="w-4">
                            <template #body="{data}">
                                {{ data.details.barang.satuan}}
                            </template>
                        </Column>
                        <Column sortable header="Bukti" class="w-4">
                            <template #body="{data}">
                                <div class="size-20 overflow-hidden border rounded" v-if="data?.bukti_bk">
                                    <Image :src="data?.bukti_bk" class="size-full" preview />
                                </div>
                                <span class="text-sm" v-else>Tidak ada foto</span>
                            </template>
                        </Column>
                        <Column sortable header="Keterangan" field="details.ket_bk">
                            <template #body="{data}">
                                {{ data.details.ket_bk??'Tidak ada keterangan'}}
                            </template>
                        </Column>
                        <Column header="Action" frozen alignFrozen="right" style="min-width: 1rem">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.status_bk!=='diproses'" @click="editPermo(data.index)" icon="pi pi-pen-to-square" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
