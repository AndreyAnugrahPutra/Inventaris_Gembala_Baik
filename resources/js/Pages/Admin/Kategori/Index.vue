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
    checkNotif()
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

const kategoriForm = useForm({
    id_ktg : null,
    nama : null,
})

const hideForm = () => {
    showForm.value = false
    kategoriForm.reset()
    kategoriForm.clearErrors()
}

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
const refreshPage = () => 
{
    checkNotif()
    showForm.value = false
    router.visit(route('admin.kategori.page'))
}

const exportCSV = () => dt.value.exportCSV()

const editKategori = (idx) => {
    kategoriForm.id_ktg = dataKategoriFix.value[idx-1]['id_ktg']
    kategoriForm.nama = dataKategoriFix.value[idx-1]['nama_kategori']
    formType.value = 'editData'
    showForm.value = true
}

const submitKategori = () => {
    confirm.require({
        message: `Tambah Kategori ${kategoriForm.nama??''} ?`,
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
            kategoriForm.post(route('admin.kategori.tambah'), {
                onSuccess : () => refreshPage(),
            })
        },
    }) 
}

const updateKategori = () => {
    confirm.require({
        message: `Update Kategori ${kategoriForm.nama??''} ?`,
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
            // showForm.value = false
            kategoriForm.post(route('admin.kategori.update'), {
                onSuccess : () => refreshPage(),
                onError : () => showForm.value = true,
            })
        },

    }) 
}

const hapusKategori = (idx) => {
    kategoriForm.id_ktg = dataKategoriFix.value[idx-1]['id_ktg']
    kategoriForm.nama = dataKategoriFix.value[idx-1]['nama_kategori']
    confirm.require({
        message: `Hapus Kategori ${kategoriForm.nama??''} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'info'
        },
        accept: () => {
            showForm.value = false
            kategoriForm.post(route('admin.kategori.hapus'), {
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
            <!-- Dialog Tambah / Edit Kategori -->
            <Dialog @hide="hideForm()" v-model:visible="showForm" modal :header="formType == 'tambahData' ? 'Tambah Kategori' : 'Edit Kategori'" class="w-fit">
                <form @submit.prevent="submitKategori || updateKategori" class="flex flex-wrap gap-[2rem] items-center my-1" autocomplete="off">
                    <!-- nama kategori -->
                    <div class="flex flex-col w-full h-10">
                        <FloatLabel variant="on">
                            <InputText fluid inputId="custom" v-model="kategoriForm.nama"/>
                            <label for="custom">Nama Kategori</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!kategoriForm.errors.nama">
                            {{ kategoriForm.errors.nama }}
                        </span>
                    </div>
                    <!-- submit button -->
                    <div class="flex items-center gap-x-2 w-full">
                        <Button label="Batal" @click="hideForm()" severity="danger"/>
                        <Button label="Submit" @click="submitKategori()" type="submit" v-if="formType=='tambahData'"/>
                        <Button label="Update" @click="updateKategori()" v-if="formType=='editData'"/>
                    </div>
                </form>
            </Dialog>
            <div class="flex flex-col gap-[1rem]">
                <Button label="Tambah Kategori" severity="success" @click="showForm = true, formType = 'tambahData'" class="w-[fit-content]" icon="pi pi-tag" size="small"/>

                <!-- datatable kategori -->
                <DataTable :globalFilterFields="['index','nama_kategori','barang_count']" removable-sort striped-rows :value="dataKategoriFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                    <template #header>
                        <div class="flex justify-between items-center gap-x-2">
                            <IconField class="w-full">
                                <InputIcon>
                                    <i class="pi pi-search me-4" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Cari Data Kategori" size="small" fluid/>
                            </IconField>
                            <Button severity="contrast" icon="pi pi-print" @click="exportCSV()" label="Export" size="small"/>
                        </div>
                    </template>
                    <template #empty>
                            <span class="flex justify-center">Tidak Ada Data Kategori</span>
                    </template>
                    <Column sortable header="No" field="index" class="w-4" />
                    <Column sortable header="Nama Kategori" field="nama_kategori" />
                    <Column sortable header="Jumlah Barang">
                        <template #body={data}>
                            <Badge :severity="data.barang_count > 0 ? 'success' : 'danger'" :value="data.barang_count" />
                        </template>
                    </Column>
                    <Column header="Action">
                        <template #body={data}>
                            <div class="flex items-center gap-x-2">
                                <Button icon="pi pi-pen-to-square" outlined @click="editKategori(data.index)" size="small" />
                                <Button icon="pi pi-trash" severity="danger" outlined  @click="hapusKategori(data.index)" size="small" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </template>
    </AuthLayout>
</template>

<style scoped>
</style>