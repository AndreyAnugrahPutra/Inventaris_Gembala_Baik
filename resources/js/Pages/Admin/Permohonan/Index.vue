<script setup>
import { onMounted, reactive, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import {FilterMatchMode} from '@primevue/core/api'

import {
    Tag,
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
    // for(let i=0;i<props.dataBarang.length;i++)
    // {
    //     dataSelect.push(props.dataBarang)
    // }
    reloadData()
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

const dataSelect = reactive([])

const pageTitle = 'Permohonan'

const permoForm = useForm({
    forms : [
        { nomor : 0, id_brg : null, jumlah_per : null}
    ],
    tgl_permo : null,
})

const editForm = useForm({
    forms : [],
    id_permo : null,
    tgl_permo : null,
    tgl_diterima : null,
    bukti_permo : null,
    status : null,
})

const exportCSV = () => dt.value.exportCSV()

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Permohonan':'Edit Permohonan'
} 

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

const hideForm = () =>
{
    reloadData()
    previewImg.value = null
    showForm.value = false
    permoForm.reset()
    permoForm.clearErrors()
    editForm.reset()
    editForm.clearErrors()
}

const refreshPage = () =>
{
    checkNotif()
    showForm.value = false
    router.visit(route('admin.permohonan.page'))

}

const reloadData = () =>
{
    dataSelect.splice(0, dataSelect.length)

    for(let i=0;i<props.dataBarang.length;i++)
    {
        dataSelect.push(props.dataBarang)
    }
}

const tambahField = () => {
    const currentIndex = permoForm.forms.length
    permoForm.forms.push({ nomor : permoForm.forms.length, id_brg : null, jumlah_per : null,})

    const filterDataCurrent = dataSelect[currentIndex-1].filter(data => data.id_brg === permoForm.forms[currentIndex-1].id_brg)
    const filterDataNext = dataSelect[currentIndex].filter(data => {
        return !permoForm.forms.some(selected => selected.id_brg === data.id_brg)
    })
    dataSelect[currentIndex-1] = filterDataCurrent
    dataSelect[currentIndex] = filterDataNext
}

const removeField = idx => permoForm.forms.splice(idx,1)

const onUpload = (e) => 
{
    editForm.bukti_permo = e.files[0]

    if(editForm.bukti_permo?.size < 1000000)
    {
        editForm.clearErrors('bukti_permo')
        toast.add({ severity: 'info', summary: 'Notifikasi', detail: 'foto terupload!', life: 2000, group : 'tr' })   
    }
    else
    {
        editForm.errors.bukti_permo = 'Ukuran File melebihi 1Mb'
        disableSubmit.value = true 
    }
    const reader = new FileReader();

    reader.onloadend = async (e) => {
        previewImg.value = e.target.result;
    };

    reader.readAsDataURL(editForm.bukti_permo);
}

const editPermo = (id_permo) => 
{
    formType.value = 'Edit Permohonan'
    
    const filterData = props.dataPermo.filter((data) => data.id_permo === id_permo)
    editForm.forms.push(filterData)
    editForm.id_permo = filterData[0].id_permo
    editForm.tgl_permo = filterData[0].permohonan.tgl_permo
    editForm.status = filterData[0].permohonan.status
    showForm.value = true
}

const exportData = ref([])

const showProsesData = () => 
{
    setTimeout(() =>
    { 
        exportData.value = dt.value.processedData.map((p,i) => ({index:i+1, ...p}))
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

const updatePermo = () => {
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
            editForm.post(route('admin.permohonan.update'), {
                onSuccess : () => refreshPage(),
            })
        },
    })
}

const hapusPermo = id_permo => {
    editForm.id_permo = id_permo

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
            editForm.post(route('admin.permohonan.hapus'), {
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
                    <form @submit.prevent class="flex flex-wrap gap-y-8 gap-x-10 items-center my-1" autocomplete="off">
                        <div class="flex flex-col h-10" v-if="formType==='Tambah Permohonan'">
                            <FloatLabel variant="on">
                                <DatePicker inputId="tgl_permo" v-model="permoForm.tgl_permo" dateFormat="dd/mm/yy"/>
                                <label for="tgl_permo">Tanggal Permohonan</label>
                            </FloatLabel>
                                <span class="text-sm text-red-500" v-if="!!permoForm.errors.tgl_permo">
                                    {{ permoForm.errors.tgl_permo }}
                                </span>
                        </div>
                        <div class="flex flex-col h-10" v-else>
                            <FloatLabel variant="on">
                                <DatePicker inputId="tgl_permo" v-model="editForm.tgl_permo" disabled dateFormat="dd/mm/yy"/>
                                <label for="tgl_permo">Tanggal Permohonan</label>
                            </FloatLabel>
                        </div>
                        <div class="flex flex-col h-10" v-if="editForm.status==='disetujui'">
                            <FloatLabel variant="on">
                                <DatePicker inputId="tgl_diterima" v-model="editForm.tgl_diterima" dateFormat="dd/mm/yy"/>
                                <label for="tgl_diterima">Tanggal Diterima</label>
                            </FloatLabel>
                            <span class="text-sm text-red-500" v-if="!!editForm.errors.tgl_diterima">
                                {{ editForm.errors.tgl_diterima }}
                            </span>
                        </div>
                        <div class="flex gap-y-8 gap-x-8 items-center" v-for="(form, index) in permoForm.forms" :key="index" v-if="formType==='Tambah Permohonan'">
                            <!-- Barang -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <Select inputId="barang" class="w-[13rem]" v-model="form.id_brg" :options="dataSelect[index]" optionLabel="nama_brg" optionValue="id_brg"/>
                                    <label for="barang">Nama Barang</label>
                                </FloatLabel>
                                <span class="text-sm text-red-500" v-if="!!permoForm.errors['forms.'+index+'.id_brg']">
                                    {{ permoForm.errors['forms.'+index+'.id_brg'] }}
                                </span>
                            </div>
                            <!-- Jumlah Permohonan -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <InputNumber inputId="jum_per" v-model="form.jumlah_per"/>
                                    <label for="jum_per">Jumlah Permohonan</label>
                                </FloatLabel>
                                <span class="text-sm text-red-500" v-if="!!permoForm.errors['forms.'+index+'.jumlah_per']">
                                    {{ permoForm.errors['forms.'+index+'.jumlah_per'] }}
                                </span>
                            </div>
                            <Button icon="pi pi-minus" severity="danger" @click="removeField(index)" size="small" v-if="permoForm.forms.length > 1"/>
                        </div>
                        <div class="flex flex-wrap gap-y-8 gap-x-8 items-center border-b pb-6" v-else v-for="(form, index) in editForm.forms[0]">
                            <!-- Barang -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <Select :disabled="form.permohonan.status==='disetujui'" inputId="barang" class="w-[13rem]" v-model="form.barang.id_brg" :options="props.dataBarang" optionLabel="nama_brg" optionValue="id_brg"/>
                                    <label for="barang">Nama Barang</label>
                                </FloatLabel>
                                <span class="text-sm text-red-500" v-if="!!editForm.errors['forms.'+index+'.id_brg']">
                                    {{ editForm.errors['forms.'+index+'.id_brg'] }}
                                </span>
                            </div>
                            <!-- Jumlah Permohonan -->
                            <div class="flex flex-col h-10">
                                <FloatLabel variant="on">
                                    <InputNumber :disabled="form.permohonan.status==='disetujui'" inputId="jum_per" v-model="form.jumlah_per"/>
                                    <label for="jum_per">Jumlah Permohonan</label>
                                </FloatLabel>
                                <span class="text-sm text-red-500" v-if="!!editForm.errors['forms.'+index+'.jumlah_per']">
                                    {{ editForm.errors['forms.'+index+'.jumlah_per'] }}
                                </span>
                            </div>
                            <!-- Jumlah Setuju -->
                            <div class="flex flex-col h-10" v-if="editForm.status==='disetujui'">
                                <FloatLabel variant="on">
                                    <InputNumber disabled inputId="jum_setuju" v-model="form.jumlah_setuju"/>
                                    <label for="jum_setuju">Jumlah Setuju</label>
                                </FloatLabel>
                            </div>
                        </div>
                        <!-- Upload Bukti -->
                        <div class="flex flex-col h-10" v-if="editForm.status==='disetujui'">
                            <FileUpload mode="basic"  name="demo[]" accept=".jpg,.jpeg,.png"  invalidFileSizeMessage="Ukuran File Melebihi 1Mb" @uploader="onUpload($event)" auto customUpload chooseLabel="Upload Bukti" class="w-52" />
                            <span class="text-sm text-red-500" v-if="!!editForm.errors.bukti_permo">
                                {{ editForm.errors.bukti_permo }}
                            </span>
                        </div>
                        <!-- button lihat bukti -->
                        <Button @click="lihatBukti=true" label="Lihat Bukti" icon="pi pi-eye" severity="success" v-if="previewImg"/>
                        <div class="flex items-center gap-x-2 w-full">
                            <Button @click="hideForm()" label="Batal" severity="danger"/>
                            <Button @click="submitPermo()" label="Submit" v-if="formType!=='Edit Permohonan'" :disabled="disableSubmit"/>
                            <Button @click="updatePermo()" label="Update" v-else :disabled="disableSubmit"/>
                            <Button @click="tambahField()" label="Tambah Field" severity="success" :disabled="permoForm.forms.length === props.dataBarang.length || !permoForm.forms[0].id_brg" v-if="formType==='Tambah Permohonan'"/>
                        </div>
                    </form>
                </Dialog>
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable exportFilename="permohonan-barang" @filter="showProsesData()"scrollable  removable-sort striped-rows :value="dataPermoFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
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
                                {{ formatTanggal(data.permohonan.tgl_permo) }}
                            </template>
                        </Column>
                        <Column sortable header="Tanggal Diterima" field="permohonan.tgl_diterima" class="w-4">
                            <template #body="{data}">
                                {{ data?.permohonan.tgl_diterima?formatTanggal(data?.permohonan.tgl_diterima):'Belum diterima' }}
                            </template>
                        </Column>
                        <Column sortable header="Nama Barang" field="barang.nama_brg" filterField="barang.nama_brg" class="w-4">
                             <template #body="{data}">
                                {{ data.barang.nama_brg}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Permohonan" field="jumlah_per" filterField="jumlah_per" class="w-4">
                            <template #body="{data}">
                               {{ data.jumlah_per}}
                            </template>
                        </Column>
                        <Column sortable header="Jumlah Disetujui" field="permohonan.jumlah_setuju" filterField="permohonan.jumlah_setuju" class="w-4">
                            <template #body="{data}">
                                {{ data.jumlah_setuju??'Menunggu Validasi Bendahara'}}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" field="barang.satuan" filterField="barang.satuan" class="w-4">
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
                        <Column sortable header="Status" field="permohonan.status" class="w-4">
                            <template #body="{data}">
                                <Tag value="Diproses"  v-if="data.permohonan.status==='diproses'" severity="warn"/>
                                <Tag value="Diterima"  v-if="data.permohonan.status==='diterima'" severity="success"/>
                                <Tag value="Ditolak"  v-if="data.permohonan.status==='ditolak'" severity="danger"/>
                                <Tag value="Silahkan Upload Bukti" severity="info"  v-if="data.permohonan.status==='disetujui'"/>
                            </template>
                        </Column>
                        <Column sortable header="Keterangan" field="ket_permo">
                            <template #body="{data}">
                                {{ data.ket_permo??'Tidak ada keterangan' }}
                            </template>
                        </Column>
                        <Column header="Action" frozen alignFrozen="right">
                            <template #body="{data}">
                                <div class="flex items-center gap-x-2">
                                    <Button :disabled="data.permohonan.status==='diterima'||data.permohonan.status==='ditolak'" @click="editPermo(data.id_permo)" icon="pi pi-pen-to-square" outlined size="small"/>
                                    <Button :disabled="data.permohonan.status==='diterima'||data.permohonan.status==='disetujui'" @click="hapusPermo(data.id_permo)" severity="danger" icon="pi pi-trash" outlined size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>