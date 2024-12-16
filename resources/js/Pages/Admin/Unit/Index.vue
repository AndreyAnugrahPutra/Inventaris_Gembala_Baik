<script setup>
import { onMounted, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'

import {
    Badge,
    Button,
    Column,
    DataTable,
    Dialog,
    FloatLabel,
    IconField,
    InputText,
    InputIcon,
    useConfirm,
    useToast,
} from 'primevue'

import {FilterMatchMode} from '@primevue/core/api'

import AuthLayout from '@/Layouts/AuthLayout.vue'


onMounted(() => 
{
    checkNotif()
    dataUnitFix.value = props.dataUnit?.map((p,i) => ({index:i+1,...p}))
})

const props = defineProps({
    flash : Object,
    dataUnit : Object
})

const dataUnitFix = ref([])

const pageTitle = 'Unit'

const formType = ref()

const dt = ref()
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const showForm = ref(false)

const confirm = useConfirm()
const toast = useToast()


const unitForm = useForm({
    id_unit : null,
    nama_unit : null,
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
            group : 'tr'
        })
    }
}

const exportCSV = () => 
{
    dt.value.exportCSV()
}

const openForm = (type) =>
{
    showForm.value = true
    formType.value = type == 'tambahData'?'Tambah Unit':'Edit Unit'
} 

const hideForm = () => 
{
    showForm.value = false
    unitForm.reset()
    unitForm.clearErrors()
}

const refreshPage = () => 
{
    checkNotif()
    showForm.value = false
    router.visit(route('admin.unit.page'))
}

const submitUnit = () => {
    confirm.require({
        message: `Tambah Unit ${unitForm.nama_unit??''} ?`,
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
            unitForm.post(route('admin.unit.tambah'), {
                onSuccess : () => refreshPage(),
            })
        },
    }) 
}

const editUnit = (idx,type) => {
    formType.value = type == 'tambahData'?'Tambah Unit':'Edit Unit'
    showForm.value = true
    unitForm.id_unit = dataUnitFix.value[idx-1]['id_unit'] 
    unitForm.nama_unit = dataUnitFix.value[idx-1]['nama_unit']
}

const updateUnit = () => {
    confirm.require({
        message: `Update unit ${unitForm.nama_unit??''} ?`,
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
            unitForm.post(route('admin.unit.update'), {
                onSuccess : () => refreshPage(),
            })
        },
    }) 
}

const hapusUnit = (idx) => {
    unitForm.id_unit = dataUnitFix.value[idx-1]['id_unit']
    unitForm.nama_unit = dataUnitFix.value[idx-1]['nama_unit']

    confirm.require({
        message: `Hapus unit ${unitForm.nama_unit??''} ?`,
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
            unitForm.post(route('admin.unit.hapus'), {
                onSuccess : () => refreshPage(),
            })
        },
        reject : () => hideForm()
    }) 
}

</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <!-- dialog tambah unit -->
            <Dialog @hide="hideForm()" :header="formType" v-model:visible="showForm" class="w-fit" modal>
                <form @submit.prevent class="flex flex-wrap gap-[2rem] items-center my-1" autocomplete="off">
                    <!-- nama unit -->
                    <div class="flex flex-col w-full h-10">
                        <FloatLabel variant="on">
                            <InputText fluid inputId="custom" v-model="unitForm.nama_unit"/>
                            <label for="custom">Nama unit</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!unitForm.errors.nama_unit">
                            {{ unitForm.errors.nama_unit }}
                        </span>
                    </div>
                    <!-- submit button -->
                    <div class="flex items-center gap-x-2 w-full">
                        <Button label="Batal" @click="hideForm()" severity="danger"/>
                        <Button label="Submit" @click="submitUnit()" type="submit" v-if="formType!=='Edit Unit'"/>
                        <Button label="Update" @click="updateUnit()" v-else/>
                    </div>
                </form>
            </Dialog>
            <div class="flex flex-col gap-[1rem]">
                <!-- tambah unit -->
                <Button class="w-fit" severity="success" label="Tambah Unit" icon="pi pi-user-plus" @click="openForm('tambahData')" size="small"/>

                <!-- Datatable Unit -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable :globalFilterFields="['index','nama_unit','users_count']" removable-sort striped-rows :value="dataUnitFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Unit" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Unit</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Nama Unit" field="nama_unit"/>
                        <Column sortable header="Jumlah Anggota">
                            <template #body={data}>
                                <Badge :severity="data.users_count>0?'success':'danger'" :value="data.users_count"/>
                            </template>
                        </Column>
                        <Column sortable header="Action">
                            <template #body={data}>
                                <div class="flex items-center gap-x-2">
                                    <Button outlined icon="pi pi-pen-to-square" @click="editUnit(data.index,'editData')" size="small"/>
                                    <Button severity="danger" outlined icon="pi pi-trash" @click="hapusUnit(data.index)" size="small"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>