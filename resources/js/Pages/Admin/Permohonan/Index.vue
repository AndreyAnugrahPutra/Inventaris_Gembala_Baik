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
    console.log(dataPermoFix.value[0].details[0].jumlah_per)
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
    tgl_permo : null,
    bukti_permo : null,
    id_brg : null,
    jumlah_per : null,
})

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

    console.log(permoForm.bukti_permo)

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
                <Dialog @hide="hideForm()" v-model:visible="showForm" :header="formType" class="w-[32rem]" modal>
                    <form @submit.prevent class="flex flex-wrap gap-[2rem] items-center my-1" autocomplete="off">
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
                        </div>
                    </form>
                </Dialog>
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable  removable-sort striped-rows :value="dataPermoFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Permohonan</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Tanggal Permohonan" field="tgl_permo" class="w-4"/>
                        <Column sortable header="Nama Barang">
                             <template #body="{data}">
                                {{ data.details[0].barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan">
                            <template #body="{data}">
                               {{ data.details[0].jumlah_per}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui">
                            <template #body="{data}">
                                {{ data.details[0].jumlah_setuju??'Menunggu Validasi Bendahara'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan">
                            <template #body="{data}">
                                {{ data.details[0].barang.satuan}}
                            </template>
                        </Column>
                        <Column sortable header="Bukti">
                            <template #body="{data}">
                                <div class="size-20 overflow-hidden border rounded" v-if="data?.bukti_permo">
                                    <Image :src="data?.bukti_permo" preview />
                                </div>
                                <span class="text-sm" v-else>Tidak ada foto</span>
                            </template>
                        </Column>
                        <Column sortable header="Status" field="status"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
