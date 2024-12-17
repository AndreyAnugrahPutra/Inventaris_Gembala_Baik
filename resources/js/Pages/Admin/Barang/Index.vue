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
    InputNumber,
    InputText,
    Select,
    useConfirm,
    useToast,
} from 'primevue'

import AuthLayout from '@/Layouts/AuthLayout.vue'

onMounted(() =>
{
    dataBarangFix.value = props.dataBarang?.map((p,i) => ({index:i+1,...p}))
})

const props = defineProps({
    flash : Object,
    dataBarang : Object,
    dataKategori : Object,
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

const dataBarangFix = ref([])

const pageTitle = 'Stok Barang'

const barangForm = useForm({
    id_brg : null,
    id_ktg : null,
    nama_brg : null,
    stok_brg : null,
    satuan : null,
})

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    router.visit(route('admin.barang.page'))
}

const exportCSV = () => dt.value.exportCSV()

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Barang':'Edit Barang'
} 

const hideForm = () => 
{
    showForm.value = false
    barangForm.reset()
    barangForm.clearErrors()
}

const editBarang = idx => 
{
    barangForm.id_brg = dataBarangFix.value[idx-1]['id_brg']
    barangForm.id_ktg = dataBarangFix.value[idx-1]['id_ktg']
    barangForm.nama_brg = dataBarangFix.value[idx-1]['nama_brg']
    barangForm.stok_brg = dataBarangFix.value[idx-1]['stok_brg']
    barangForm.satuan = dataBarangFix.value[idx-1]['satuan']
    
    formType.value = 'Edit Barang'
    showForm.value = true
}

const submitBarang = () => {
    confirm.require({
        message: `Tambah Barang ${barangForm.nama_brg??''} ?`,
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
            barangForm.post(route('admin.barang.tambah'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const updateBarang = () => {
    confirm.require({
        message: `Update Barang ${barangForm.nama_brg??''} ?`,
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
            barangForm.post(route('admin.barang.update'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const hapusBarang = idx => {

    barangForm.id_brg = dataBarangFix.value[idx-1]['id_brg']
    barangForm.nama_brg = dataBarangFix.value[idx-1]['nama_brg']

    confirm.require({
        message: `Hapus Barang ${barangForm.nama_brg??''} ?`,
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
            barangForm.post(route('admin.barang.hapus'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- dialog tambah barang -->
                <Dialog @hide="hideForm()" :header="formType" v-model:visible="showForm" class="w-[32rem]"  modal>
                    <form @submit.prevent class="flex flex-wrap gap-[2rem] w-full items-center my-1" autocomplete="off">
                        <!-- nama barang -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText inputId="custom" v-model="barangForm.nama_brg"/>
                                <label for="custom">Nama Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!barangForm.errors.nama_brg">
                                {{ barangForm.errors.nama_brg }}
                            </span>
                        </div>
                        <!-- nama kategori -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <Select inputId="kategori" class="w-[12.5rem]" :options="props.dataKategori" optionLabel="nama_kategori" optionValue="id_ktg" v-model="barangForm.id_ktg"/>
                                <label for="kategori">Kategori Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!barangForm.errors.id_ktg">
                                {{ barangForm.errors.id_ktg }}
                            </span>
                        </div>
                        <!-- stok barang -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputNumber fluid inputId="stok" v-model="barangForm.stok_brg"/>
                                <label for="custom">Stok Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!barangForm.errors.stok_brg">
                                {{ barangForm.errors.stok_brg }}
                            </span>
                        </div>
                        <!-- satuan barang -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText class="w-50" inputId="custom" v-model="barangForm.satuan"/>
                                <label for="custom">Satuan Barang</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!barangForm.errors.satuan">
                                {{ barangForm.errors.satuan }}
                            </span>
                        </div>
                        <div class="w-full flex flex-items-center gap-x-2">
                            <Button label="Batal" @click="hideForm()" severity="danger"/>
                            <Button label="Submit" @click="submitBarang()" type="submit" v-if="formType!=='Edit Barang'"/>
                            <Button label="Update" @click="updateBarang()" v-else/>
                        </div>
                    </form>
                </Dialog>
                <!-- tambah barang -->
                <Button @click="openForm('tambahData')" class="w-fit" severity="success" label="Tambah Barang" icon="pi pi-box" size="small" />
                <!-- Datatable Barang -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable  removable-sort striped-rows :value="dataBarangFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Barang" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Barang</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Nama Barang" field="nama_brg"/>
                        <Column sortable header="Kategori Barang" field="kategori.nama_kategori"/>
                        <Column sortable header="Jumlah Barang" field="stok_brg"/>
                        <Column sortable header="Satuan" field="satuan"/>
                        <Column header="Action">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button  @click=editBarang(data.index) icon="pi pi-pen-to-square" outlined size="small" />
                                    <Button severity="danger" @click=hapusBarang(data.index) icon="pi pi-trash" outlined size="small" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
