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

const pageTitle = 'Permohonan Barang Keluar'

const permoForm = useForm({
    id_bk : null,
    tgl_bk : null,
    bukti_bk : null,
    status_bk : null,
    id_dbk : null,
    id_brg : null,
    jum_bk : null,
    ket_bk : null,
})

const exportCSV = () => dt.value.exportCSV()

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Permohonan Barang Keluar':'Edit Permohonan Barang Keluar'
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
    router.visit(route('guru.permohonan.page'))

}

const onUpload = (e) => 
{
    permoForm.bukti_bk = e.files[0]

    if(permoForm.bukti_bk?.size < 1000000)
    {
        permoForm.clearErrors('bukti_bk')
        toast.add({ severity: 'info', summary: 'Notifikasi', detail: 'foto terupload!', life: 2000, group : 'tr' })   
    }
    else
    {
        permoForm.errors.bukti_bk = 'Ukuran File melebihi 1Mb'
        disableSubmit.value = true 
    }
    const reader = new FileReader();

    reader.onloadend = async (e) => {
        previewImg.value = e.target.result;
    };

    reader.readAsDataURL(permoForm.bukti_bk);
}

const editPermo = (idx) => 
{
    formType.value = 'Edit Permohonan'

    permoForm.id_bk = dataPermoFix.value[idx-1]['id_bk']
    permoForm.tgl_bk = dataPermoFix.value[idx-1]['tgl_bk']
    permoForm.bukti_bk = dataPermoFix.value[idx-1]['bukti_bk']
    permoForm.status_bk = dataPermoFix.value[idx-1]['status_bk']
    permoForm.id_dbk = dataPermoFix.value[idx-1]['details'].id_dbk
    permoForm.id_brg = dataPermoFix.value[idx-1]['details'].id_brg
    permoForm.jum_bk = dataPermoFix.value[idx-1]['details'].jum_bk
    permoForm.ket_bk = dataPermoFix.value[idx-1]['details'].ket_bk

    showForm.value = true
}


const submitPermo = () => {
    confirm.require({
        message: `Tambah Permohonan Barang Keluar?`,
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
            permoForm.post(route('guru.permohonan.tambah'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const updatePermo = () => {
    confirm.require({
        message: `Update Permohonan Barang Keluar?`,
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
            permoForm.post(route('guru.permohonan.update'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const hapusPermo = idx => {
    permoForm.id_bk = dataPermoFix.value[idx-1]['id_bk']
    permoForm.bukti_bk = dataPermoFix.value[idx-1]['bukti_bk']
    permoForm.id_dbk = dataPermoFix.value[idx-1]['details'][0].id_dbk

    confirm.require({
        message: `Hapus Permohonan Barang Keluar?`,
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
            permoForm.post(route('guru.permohonan.hapus'), {
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
                <Button @click="openForm('tambahData')" class="w-fit" severity="success" label="Tambah Permohonan Barang Keluar" icon="pi pi-file" size="small" />
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
                                <DatePicker  inputId="tgl" v-model="permoForm.tgl_bk" dateFormat="dd-mm-yy"/>
                                <label for="tgl">Tanggal Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.tgl_bk">
                                {{ permoForm.errors.tgl_bk }}
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
                                <InputNumber inputId="jum_per" v-model="permoForm.jum_bk"/>
                                <label for="jum_per">Jumlah Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.jum_bk">
                                {{ permoForm.errors.jum_bk }}
                            </span>
                        </div>
                        <!-- Upload Bukti -->
                        <div class="flex flex-col h-10">
                            <FileUpload mode="basic"  name="demo[]" accept=".jpg,.jpeg,.png"  invalidFileSizeMessage="Ukuran File Melebihi 1Mb" @uploader="onUpload($event)" auto customUpload chooseLabel="Upload Bukti" class="w-52" />
                            <span class="text-sm text-red-500" v-if="!!permoForm.errors.bukti_bk">
                                {{ permoForm.errors.bukti_bk }}
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
                    <DataTable  removable-sort striped-rows :value="dataPermoFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
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
                        <Column sortable header="Tanggal Permohonan" field="tgl_bk" class="w-4"/>
                        <Column sortable header="Nama Barang" filterField="details.barang.nama_brg" class="w-4">
                             <template #body="{data}">
                                {{ data.details.barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan" filterField="details.jum_bk" class="w-4">
                            <template #body="{data}">
                               {{ data.details.jum_bk}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" class="w-4">
                            <template #body="{data}">
                                {{ data.details.jumlah_setuju??'Menunggu Validasi Tata Usaha'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" filterField="details.barang.satuan" class="w-4">
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
                        <Column sortable header="Status" field="status_bk" class="w-4"/>
                        <Column header="Action" frozen alignFrozen="right" class="w-4">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.details.jumlah_setuju" @click="editPermo(data.index)" icon="pi pi-pen-to-square" outlined size="small"/>
                                    <Button :disabled="data.details.jumlah_setuju" @click="hapusPermo(data.index)" severity="danger" icon="pi pi-trash" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
