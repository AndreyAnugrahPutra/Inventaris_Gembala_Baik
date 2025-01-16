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
const showEditForm = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const statusPermo = [
    {status : 'ditolak'},
    {status : 'disetujui'},
]

const confirm = useConfirm()

const dataPermoFix = ref([])

const pageTitle = 'Permohonan'

const permoFormDisabled = useForm({
    tgl_permo : null,
    jumlah_per : null,
    nama_brg : null,
})

const editForm = useForm({
    forms : [],
    status : null,
    ket_permo : null,
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

const hideEditForm = () =>
{
    showEditForm.value = false
    editForm.reset()
    editForm.clearErrors()
}

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    router.visit(route('bendahara.permohonan.page'))

}

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

const editPermo = (id_permo) => 
{
    formType.value = 'Validasi Permohonan'

    const filterData = props.dataPermo.filter((data) => data.id_permo === id_permo)

    editForm.forms.push(filterData)

    showEditForm.value = true
}


const terimaPermo = () => {
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
            label: 'Terima',
            severity: 'info'
        },
        accept: () => {
            editForm.post(route('bendahara.permohonan.terima'), {
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
                <!-- edit dialog -->
                <Dialog :header="formType" class="w-[36rem]" @hide="hideForm()" v-model:visible="showEditForm" modal>
                    <form @submit.prevent class="flex flex-wrap gap-y-4 gap-x-20 items-center my-1" autocomplete="off">
                        <div v-for="(form, index) in editForm.forms[0]" :key="index" class="flex gap-y-8 gap-x-20 items-center flex-wrap border-b pb-6">
                            <!-- Tanggal Permohonan -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <DatePicker disabled class="w-[12.5rem]"  inputId="tgl" v-model="form.permohonan.tgl_permo" dateFormat="dd-mm-yy"/>
                                    <label for="tgl">Tanggal Permohonan</label>
                                </FloatLabel>
                        
                            </div>
                            <!-- Barang -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <InputText disabled inputId="barang" class="w-[12.5rem]" v-model="form.barang.nama_brg"/>
                                    <label for="barang">Nama Barang</label>
                                </FloatLabel>
                            </div>
                            <!-- Jumlah Permohonan -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <InputNumber disabled inputId="jum_per" class="w-[12.5rem]" v-model="form.jumlah_per"/>
                                    <label for="jum_per">Jumlah Permohonan</label>
                                </FloatLabel>
                            </div>
                            <!-- Jumlah Setuju Permohonan -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <InputNumber :disabled="form.permohonan.status==='ditolak'" inputId="jum_per" class="w-[12.5rem]" v-model="form.jumlah_setuju"/>
                                    <label for="jum_per">Jumlah Setuju</label>
                                </FloatLabel>
                                  <span class="text-sm text-red-500" v-if="!!editForm.errors['forms.0.'+index+'.jumlah_setuju']">
                                    {{ editForm.errors['forms.0.'+index+'.jumlah_setuju']}}
                                </span>
                            </div>
                        </div>
                        <!-- status permohonan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <Select inputId="status" class="w-[12.5rem]" v-model="editForm.status" :options="statusPermo" optionLabel="status" optionValue="status"/>
                                <label for="status">Status Permohonan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!editForm.errors.status">
                                {{ editForm.errors.status }}
                            </span>
                        </div>
                        <!-- Keterangan -->
                        <div class="flex flex-col h-10">
                            <FloatLabel variant="on">
                                <InputText inputId="barang" class="w-[12.5rem]" v-model="editForm.ket_permo"/>
                                <label for="barang">Keterangan</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!editForm.errors.ket_permo">
                                {{ editForm.errors.ket_permo }}
                            </span>
                        </div>
                        <div class="flex items-center gap-x-2 w-full">
                            <Button @click="terimaPermo()" label="Validasi"/>
                            <Button @click="hideEditForm()" label="Batal" severity="danger" outlined/>
                        </div>
                    </form>
                </Dialog>
               
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable exportFilename="data-permohonan-barang-guru" scrollable  removable-sort stripedRows :value="dataPermoFix" dataKey="id_permo" v-model:filters="filters" ref="dt" :rows="5" paginator>
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
                        <Column :exportable="false" sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Tanggal Permohonan" field="permohonan.tgl_permo" class="w-4">
                            <template #body="{data}">
                                {{ formatTanggal(data.permohonan.tgl_permo)}}
                            </template>
                        </Column>
                        <Column sortable header="Tanggal Diterima" field="permohonan.tgl_diterima" class="w-4">
                            <template #body="{data}">
                                {{ data.permohonan.tgl_diterima?formatTanggal(data.permohonan.tgl_diterima):'Belum diterima'}}
                            </template>
                        </Column>
                        <Column sortable header="Nama Barang" field="barang.nama_brg" class="w-4">
                             <template #body="{data}">
                                {{ data.barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan" field="jumlah_per" class="w-4">
                            <template #body="{data}">
                               {{ data.jumlah_per}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" field="jumlah_setuju" class="w-4">
                            <template #body="{data}">
                                {{ data.jumlah_setuju??'Menunggu Validasi Bendahara'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" field="barang.satuan" class="w-4">
                            <template #body="{data}">
                                {{ data.barang.satuan}}
                            </template>
                        </Column>
                        <Column sortable header="Bukti" class="w-4">
                            <template #body="{data}">
                                <div class="size-20 overflow-hidden border rounded" v-if="data?.permohonan.bukti_permo">
                                    <Image :src="data?.permohonan.bukti_permo" class="size-full" preview />
                                </div>
                                <span class="text-sm" v-else>Tidak ada foto</span>
                            </template>
                        </Column>
                        <Column sortable header="Status" field="permohonan.status" class="w-4"/>
                        <Column sortable header="Keterangan" field="ket_permo" style="min-width: 200px">
                            <template #body="{data}">
                                {{ data.ket_permo??'Tidak ada keterangan'}}
                            </template>
                        </Column>
                        <Column header="Action" frozen alignFrozen="right" style="min-width: 1rem">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.permohonan.status!=='diproses'" @click="editPermo(data.id_permo)" icon="pi pi-pen-to-square" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
