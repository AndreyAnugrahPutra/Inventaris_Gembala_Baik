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
    dataBarang : Object,
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
const lihatBukti = ref(false)
const disableSubmit = ref(false)
const previewImg = ref(null)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const confirm = useConfirm()

const dataPermoFix = ref([])

const pageTitle = 'Permohonan'

const permoForm = useForm({
    id_permo : null,
    tgl_permo : null,
    bukti_permo : null,
    status : null,
    id_dp : null,
    id_brg : null,
    jumlah_per : null,
    ket_permo : null,
})

// const exportForm = useForm({
//     data : null
// })

const exportCSV = () => dt.value.exportCSV()

const exportPDF = async () => { 
    router.post(route('admin.permohonan.pdf'),{data : dt.value.processedData},{
        onFinish : () => setTimeout(() => console.clear(),500)
    })
}

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Permohonan':'Edit Permohonan'
} 

const hideForm = () =>
{
    previewImg.value = null
    showForm.value = false
    permoForm.reset()
    permoForm.clearErrors()
}

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    router.visit(route('admin.permohonan.page'))

}

const onUpload = (e) => 
{
    permoForm.bukti_permo = e.files[0]

    if(permoForm.bukti_permo?.size < 1000000)
    {
        permoForm.clearErrors('bukti_permo')
        toast.add({ severity: 'info', summary: 'Notifikasi', detail: 'foto terupload!', life: 2000, group : 'tr' })   
    }
    else
    {
        permoForm.errors.bukti_permo = 'Ukuran File melebihi 1Mb'
        disableSubmit.value = true 
    }
    const reader = new FileReader();

    reader.onloadend = async (e) => {
        previewImg.value = e.target.result;
    };

    reader.readAsDataURL(permoForm.bukti_permo);
}

const editPermo = (idx) => 
{
    formType.value = 'Edit Permohonan'

    permoForm.id_permo = dataPermoFix.value[idx-1]['id_permo']
    permoForm.tgl_permo = dataPermoFix.value[idx-1]['tgl_permo']
    permoForm.bukti_permo = dataPermoFix.value[idx-1]['bukti_permo']
    permoForm.status = dataPermoFix.value[idx-1]['status']
    permoForm.id_dp = dataPermoFix.value[idx-1]['details'].id_dp
    permoForm.id_brg = dataPermoFix.value[idx-1]['details'].id_brg
    permoForm.jumlah_per = dataPermoFix.value[idx-1]['details'].jumlah_per
    permoForm.ket_permo = dataPermoFix.value[idx-1]['details'].ket_permo

    showForm.value = true
}

const exportData = ref([])

const showProsesData = () => 
{
    setTimeout(() =>
    { 
        exportData.value = dt.value.processedData.map((p,i) => ({index:i+1, ...p}))
        console.log(dt.value.processedData)
        console.log(exportData.value)
    }
    ,500)
}


const submitPermo = () => {
    confirm.require({
        message: `Tambah Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Tambah',
            severity: 'info'
        },
        accept: () => {
            permoForm.post(route('admin.permohonan.tambah'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const updatePermo = idx => {
    confirm.require({
        message: `Update Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Update',
            severity: 'info'
        },
        accept: () => {
            permoForm.post(route('admin.permohonan.update'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const hapusPermo = idx => {
    permoForm.id_permo = dataPermoFix.value[idx-1]['id_permo']
    permoForm.bukti_permo = dataPermoFix.value[idx-1]['bukti_permo']
    permoForm.id_dp = dataPermoFix.value[idx-1]['details'].id_dp

    confirm.require({
        message: `Hapus Permohonan ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger'
        },
        accept: () => {
            permoForm.post(route('admin.permohonan.hapus'), {
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
                <!-- tambah permohonan -->
                <Button @click="openForm('tambahData')" class="w-fit" severity="success" label="Tambah Permohonan" icon="pi pi-file" size="small" />
                <!-- Dialog bukti -->
                <Dialog header="Bukti" class="w-fit" v-model:visible="lihatBukti" modal>
                    <div class="w-full flex items-center justify-center">
                        <div class="size-80 overflow-hidden border">
                            <img :src="previewImg" class="size-full">
                        </div>
                    </div>
                </Dialog>
                <!-- Dialog Tambah Permohonan -->
                <Dialog @hide="hideForm()" v-model:visible="showForm" :header="formType" class="w-[36rem]" modal>
                    <form @submit.prevent class="flex flex-wrap gap-y-8 gap-x-20 items-center my-1" autocomplete="off">
                        <!-- Tanggal Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <DatePicker  inputId="tgl" v-model="permoForm.tgl_permo" dateFormat="dd-mm-yy"/>
                                <label for="tgl">Tanggal Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.tgl_permo">
                                {{ permoForm.errors.tgl_permo }}
                            </span>
                        </div>
                        <!-- Barang -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <Select inputId="barang" class="w-[13rem]" v-model="permoForm.id_brg" :options="props.dataBarang" optionLabel="nama_brg" optionValue="id_brg"/>
                                <label for="barang">Nama Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.id_brg">
                                {{ permoForm.errors.id_brg }}
                            </span>
                        </div>
                        <!-- Jumlah Permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputNumber inputId="jum_per" v-model="permoForm.jumlah_per"/>
                                <label for="jum_per">Jumlah Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.jumlah_per">
                                {{ permoForm.errors.jumlah_per }}
                            </span>
                        </div>
                        <!-- Upload Bukti -->
                        <div class="flex flex-col h-10">
                            <FileUpload mode="basic"  name="demo[]" accept=".jpg,.jpeg,.png"  invalidFileSizeMessage="Ukuran File Melebihi 1Mb" @uploader="onUpload($event)" auto customUpload chooseLabel="Upload Bukti" class="w-52" />
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.bukti_permo">
                                {{ permoForm.errors.bukti_permo }}
                            </span>
                        </div>
                        <!-- button lihat bukti -->
                        <Button @click="lihatBukti=true" label="Lihat Bukti" icon="pi pi-eye" severity="success" v-if="previewImg"/>
                        <div class="flex items-center gap-x-2 w-full">
                            <Button @click="hideForm()" label="Batal" severity="danger"/>
                            <Button @click="submitPermo()" label="Submit" v-if="formType!=='Edit Permohonan'" :disabled="disableSubmit"/>
                            <Button @click="updatePermo()" label="Update" v-else :disabled="disableSubmit"/>
                        </div>
                    </form>
                </Dialog>
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable @filter="showProsesData()"scrollable  removable-sort striped-rows :value="dataPermoFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Permohonan" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                                <Button class="w-fit" icon="pi pi-print" severity="contrast" @click="exportPDF()" label="PDF" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Permohonan</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Tanggal Permohonan" field="tgl_permo" class="w-4"/>
                        <Column sortable header="Nama Barang" filterField="details.barang.nama_brg" class="w-4">
                             <template #body="{data}">
                                {{ data.details.barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan" filterField="details.jumlah_per" class="w-4">
                            <template #body="{data}">
                               {{ data.details.jumlah_per}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" filterField="details.jumlah_setuju" class="w-4">
                            <template #body="{data}">
                                {{ data.details.jumlah_setuju??'Menunggu Validasi Bendahara'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" filterField="details.barang.satuan" class="w-4">
                            <template #body="{data}">
                                {{ data.details.barang.satuan}}
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
                        <Column sortable header="Keterangan" field="details.ket_permo"/>
                        <Column header="Action" frozen alignFrozen="right">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.status==='diterima'?true:false" @click="editPermo(data.index)" icon="pi pi-pen-to-square" outlined size="small"/>
                                    <Button :disabled="data.status==='diterima'?true:false" @click="hapusPermo(data.index)" severity="danger" icon="pi pi-trash" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>