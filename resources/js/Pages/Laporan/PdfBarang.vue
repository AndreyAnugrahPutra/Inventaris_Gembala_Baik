<script setup>
import { onMounted } from 'vue'

onMounted(() =>
{
    setTimeout(()=>
     window.print() ,
    1000)
})

const props = defineProps({
    data : Object,
    tanggal : String,
})

</script>

<template>
    <div class="w-full m-4 flex flex-col gap-4">
        <span class="text-xl">Laporan Stok Barang</span>
        <span class="text-lg">Tanggal : {{ props.tanggal }}</span>
        <table class="border border-black mr-12">
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
    </div>
</template>

<style scoped>
</style>