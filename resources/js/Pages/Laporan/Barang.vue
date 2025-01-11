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
    dataBarangFix.value = props.dataBarang?.map((p,i) => ({index:i+1,...p}))
    loading.value = false
})

const props = defineProps({
    flash : Object,
    dataBarang : Object,
    dataKategori : Object,
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

const dataBarangFix = ref([])

const pageTitle = 'Laporan Stok Barang'

const filterKategori = () => 
{
    if(filterData.kategori!==null)
    {
       dataBarangFix.value = props.dataBarang.filter((barang) => barang.kategori.nama_kategori === filterData.kategori).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangFix.value = props.dataBarang?.map((p,i) => ({index:i+1,...p}))
}

const filterSatuan = () => 
{
    if(filterData.satuan!==null)
    {
       dataBarangFix.value = props.dataBarang.filter((barang) => barang.satuan === filterData.satuan).map((p,i) => ({index:i+1,...p}))
    }
    else dataBarangFix.value = props.dataBarang?.map((p,i) => ({index:i+1,...p}))
}

watch(() => 

    filterData.nama_barang, 
    (namaBarang) => {
        if(namaBarang)
        {
            dataBarangFix.value = props.dataBarang.filter((barang) => barang.nama_brg.toLowerCase().includes(namaBarang.toLowerCase())).map((p,i) => ({index:i+1,...p}))
        }
        else dataBarangFix.value = props.dataBarang?.map((p,i) => ({index:i+1,...p}))
    }
)

const exportPDF = () => {
    router.post(route('laporan.barang.pdf'),{data : dt.value.processedData})
}
</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :pageTitle="pageTitle">
        <template #pageContent>
            <div class="flex flex-col gap-4">
                <!-- Datatable Barang -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable :loading="loading" filter-display="row" removable-sort striped-rows :value="dataBarangFix" dataKey="index" v-model:filters="filters" ref="dt" :rows="5" paginator :globalFilterFields="['index','kategori.nama_kategori']">
                        <template #header>
                            <div class="flex justify-center items-center gap-x-2">
                                <InputText placeholder="Nama Barang" v-model="filterData.nama_barang" />
                                <Select showClear @change="filterKategori()" v-model="filterData.kategori" :options="props.dataKategori" optionLabel="nama_kategori" optionValue="nama_kategori" placeholder="Kategori" style="min-width: 14rem">
                                     <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{ slotProps.option.nama_kategori }}</span>
                                        </div>
                                    </template>
                                </Select>
                                <Select showClear @change="filterSatuan()" v-model="filterData.satuan" :options="props.dataBarang" optionLabel="satuan" optionValue="satuan" placeholder="Satuan" style="min-width: 14rem">
                                     <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{ slotProps.option.satuan }}</span>
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
                        <Column sortable header="Nama Barang" field="nama_brg"/>
                        <Column sortable header="Kategori Barang" field="kategori.nama_kategori"/>
                        <Column sortable header="Jumlah Barang" field="stok_brg"/>
                        <Column sortable header="Satuan" field="satuan"/>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>
