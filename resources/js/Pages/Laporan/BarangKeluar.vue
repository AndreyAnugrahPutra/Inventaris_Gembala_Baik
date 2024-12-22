<script setup>
import { onMounted, ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import {FilterMatchMode} from '@primevue/core/api'

import {
    Button,
    Column,
    DataTable,
    InputText,
    Select,
} from 'primevue'

import AuthLayout from '@/Layouts/AuthLayout.vue'
import { watch } from 'vue'

onMounted(() =>
{
    dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
    loading.value = false
})

const props = defineProps({
    flash : Object,
    dataBarangKeluar : Object,
})

const dt = ref()

const loading = ref(true)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'kategori.nama_kategori': { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const filterData = useForm({
    unit : null,
    nama_barang : null,
    satuan : null,
})

const dataBarangKeluarFix = ref([])

const pageTitle = 'Laporan Barang Keluar'

const filterUnit = () => 
{
    if(filterData.unit!==null)
    {
       dataBarangKeluarFix.value = props.dataBarangKeluar.filter((bk) => bk.user.unit.nama_unit === filterData.unit).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
}

const filterSatuan = () => 
{
    if(filterData.satuan!==null)
    {
       dataBarangKeluarFix.value = props.dataBarangKeluar.filter((permo) => permo.details.barang.satuan === filterData.satuan).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
}

watch(() => 

    filterData.nama_barang, 
    (namaBarang) => {
        if(namaBarang)
        {
            dataBarangKeluarFix.value = props.dataBarangKeluar.filter((bk) => bk.details.barang.nama_brg.toLowerCase().includes(namaBarang.toLowerCase())).map((p,i) => ({index:i+1,...p}))
        }
        else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
    }
)

const exportPDF = () => {
     router.post(route('laporan.barang_keluar.pdf'),{data : dt.value.processedData},{
        onFinish : () => setTimeout(() => console.clear(),500)
    })
}
</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable :loading="loading" filter-display="row" removable-sort striped-rows :value="dataBarangKeluarFix" dataKey="index" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-center items-center gap-x-2">
                                <InputText placeholder="Nama Barang" v-model="filterData.nama_barang" />
                                <Select showClear @change="filterUnit()" v-model="filterData.unit" :options="props.dataBarangKeluar" optionLabel="user.unit.nama_unit" optionValue="user.unit.nama_unit" placeholder="Unit" style="min-width: 14rem">
                                     <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{ slotProps.option.user.unit.nama_unit }}</span>
                                        </div>
                                    </template>
                                </Select>
                                <Select showClear @change="filterSatuan()" v-model="filterData.satuan" :options="props.dataBarangKeluar" optionLabel="details.barang.satuan" optionValue="details.barang.satuan" placeholder="Satuan" style="min-width: 14rem">
                                     <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{ slotProps.option.details.barang.satuan }}</span>
                                        </div>
                                    </template>
                                </Select>
                                <Button icon="pi pi-print" severity="contrast" @click="exportPDF()" label="PDF" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada Barang</span>
                        </template>
                        <template #loading>
                            <span class="flex justify-center">Sedang Memuat Data...</span>
                        </template>
                        <Column sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Hari/Tanggal" field="tgl_bk"/>
                        <Column sortable header="Nama User" field="user.username"/>
                        <Column sortable header="Nama Unit" field="user.unit.nama_unit"/>
                        <Column sortable header="Nama Barang" field="details.barang.nama_brg"/>
                        <Column sortable header="Jumlah Barang Keluar" field="details.jum_bk"/>
                        <Column sortable header="Jumlah Disetujui" field="details.jum_setuju_bk"/>
                        <Column sortable header="Satuan" field="details.barang.satuan"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
