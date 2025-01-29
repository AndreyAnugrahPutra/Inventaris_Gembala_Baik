<script setup>
import { onMounted } from 'vue'
import KopSuratLayout from '@/Layouts/KopSuratLayout.vue'

onMounted(() =>
{
    setTimeout(() =>
     window.print() ,
    1000)
})

const props = defineProps({
    data : Object,
    tanggal : String,
})

</script>

<template>
    <div class="m-4 flex flex-col gap-4 px-8">
        <KopSuratLayout :tanggal="props.tanggal">
            <span class="text-lg text-center mb-4">Laporan Barang</span>
            <table class="border border-black">
                <thead>
                    <tr>
                        <th class="p-2 w-[5%] border border-black text-center">No</th>
                        <th class="p-2 w-4 border border-black text-left">Nama Barang</th>
                        <th class="p-2 w-4 border border-black text-left">Kategori Barang</th>
                        <th class="p-2 w-4 border border-black text-left">Jumlah Barang</th>
                        <th class="p-2 w-4 border border-black text-left">Satuan</th>
                    </tr>
                </thead>
                <tbody class="border border-black p-4">
                    <tr v-for="(item, index) in props.data?.data" :key="index">
                        <td class="p-2 border border-black text-center">{{ item.index}}</td>
                        <td class="p-2 border border-black">{{ item.nama_brg }}</td>
                        <td class="p-2 border border-black">{{ item.kategori.nama_kategori }}</td>
                        <td class="p-2 border border-black">{{ item.stok_brg }}</td>
                        <td class="p-2 border border-black">{{ item.satuan }}</td>
                    </tr>
                </tbody>
            </table>
        </KopSuratLayout>
    </div>
</template>

<style scoped>
</style>