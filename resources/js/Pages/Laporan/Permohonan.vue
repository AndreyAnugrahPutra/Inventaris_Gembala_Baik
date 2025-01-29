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
    dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
    loading.value = false
})

const props = defineProps({
    flash : Object,
    dataBarang : Object,
    dataKategori : Object,
    dataPermo : Object,
    dataSatuan : Object,
})

const dt = ref()

const loading = ref(true)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'kategori.nama_kategori': { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const filterData = useForm({
    kategori : null,
    nama_barang : null,
    satuan : null,
})

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

const dataPermoFix = ref([])

const pageTitle = 'Laporan Permohonan Barang'

const filterKategori = () => 
{
    if(filterData.kategori!==null)
    {
       dataPermoFix.value = props.dataPermo.filter((permo) => permo.barang.kategori.nama_kategori === filterData.kategori).map((p,i) => ({index:i+1,...p}))
    }
    else dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
}

const filterSatuan = () => 
{
    if(filterData.satuan!==null)
    {
       dataPermoFix.value = props.dataPermo.filter((permo) => permo.barang.satuan === filterData.satuan).map((p,i) => ({index:i+1,...p}))
    }
    else dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
}

watch(() => 

    filterData.nama_barang, 
    (namaBarang) => {
        if(namaBarang)
        {
            dataPermoFix.value = props.dataPermo.filter((permo) => permo.barang.nama_brg.toLowerCase().includes(namaBarang.toLowerCase())).map((p,i) => ({index:i+1,...p}))
        }
        else dataPermoFix.value = props.dataPermo?.map((p,i) => ({index:i+1,...p}))
    }
)

const exportPDF = () => {
     router.post(route('laporan.permohonan.pdf'),{data : dt.value.processedData})
}
</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- Datatable Permohonan -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable :loading="loading" filter-display="row" removable-sort striped-rows :value="dataPermoFix" dataKey="index" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-center items-center gap-x-2">
                                <InputText placeholder="Nama Barang" v-model="filterData.nama_barang" />
                                <Select showClear @change="filterKategori()" v-model="filterData.kategori" :options="props.dataKategori" optionLabel="nama_kategori" optionValue="nama_kategori" placeholder="Kategori" style="min-width: 14rem"/>
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
                        <Column sortable header="Hari/Tanggal Permohonan" field="permohonan.tgl_permo">
                            <template #body="{data}">
                                {{ formatTanggal(data.permohonan.tgl_permo) }}
                            </template>
                        </Column>
                        <Column sortable header="Hari/Tanggal Terima" field="permohonan.tgl_diterima">
                            <template #body="{data}">
                                {{ data.permohonan.tgl_diterima?formatTanggal(data.permohonan.tgl_diterima):'Belum diterima' }}
                            </template>
                        </Column>
                        <Column sortable header="Nama Barang" field="barang.nama_brg"/>
                        <Column sortable header="Jumlah Permohonan" field="jumlah_per"/>
                        <Column sortable header="Jumlah Disetujui" field="jumlah_setuju">
                            <template #body="{data}">
                                {{ data.jumlah_setuju??'Menunggu validasi bendahara' }}
                            </template>
                        </Column>
                        <Column sortable header="Satuan" field="barang.satuan"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
