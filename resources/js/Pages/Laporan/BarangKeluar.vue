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
    dataUnit : Object,
    dataSatuan : Object,
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

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

const filterUnit = () => 
{
    if(filterData.unit!==null)
    {
       dataBarangKeluarFix.value = props.dataBarangKeluar.filter((bk) => bk.barang_keluar.user.unit.nama_unit === filterData.unit).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
}

const filterSatuan = () => 
{
    if(filterData.satuan!==null)
    {
       dataBarangKeluarFix.value = props.dataBarangKeluar.filter((permo) => permo.barang.satuan === filterData.satuan).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
}

watch(() => 

    filterData.nama_barang, 
    (namaBarang) => {
        if(namaBarang)
        {
            dataBarangKeluarFix.value = props.dataBarangKeluar.filter((bk) => bk.barang.nama_brg.toLowerCase().includes(namaBarang.toLowerCase())).map((p,i) => ({index:i+1,...p}))
        }
        else dataBarangKeluarFix.value = props.dataBarangKeluar?.map((p,i) => ({index:i+1,...p}))
    }
)

const exportPDF = () => {
     router.post(route('laporan.barang_keluar.pdf'),{data : dt.value.processedData})
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
                                <Select showClear @change="filterUnit()" v-model="filterData.unit" :options="props.dataUnit" optionLabel="nama_unit" optionValue="nama_unit" placeholder="Unit" style="min-width: 14rem"/>
                                <Select showClear @change="filterSatuan()" v-model="filterData.satuan" :options="props.dataSatuan" optionLabel="satuan" optionValue="satuan" placeholder="Satuan" style="min-width: 14rem"/>
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
                        <Column sortable header="Hari/Tanggal Permohonan" field="barang_keluar.tgl_bk">
                            <template #body="{data}">
                                {{ formatTanggal(data.barang_keluar.tgl_bk) }}
                            </template>
                        </Column>
                        <Column sortable header="Hari/Tanggal Terima" field="barang_keluar.tgl_diterima">
                            <template #body="{data}">
                                {{ data.barang_keluar.tgl_diterima?formatTanggal(data.barang_keluar.tgl_diterima):'Belum diterima' }}
                            </template>
                        </Column>
                        <Column sortable header="Nama User" field="barang_keluar.user.username"/>
                        <Column sortable header="Nama Unit" field="barang_keluar.user.unit.nama_unit"/>
                        <Column sortable header="Nama Barang" field="barang.nama_brg"/>
                        <Column sortable header="Jumlah Barang Keluar" field="jum_bk"/>
                        <Column sortable header="Jumlah Disetujui" field="jum_setuju_bk">
                             <template #body="{data}">
                                {{ data.jum_setuju_bk??'-' }}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" field="barang.satuan"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
