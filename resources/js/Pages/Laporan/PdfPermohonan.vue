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

console.log(props.data)

</script>

<template>
    <div class="w-full m-4 flex flex-col gap-4">
        <span class="text-xl">Laporan Permohonan Barang Masuk</span>
        <span class="text-lg">Tanggal : {{ props.tanggal }}</span>
        <table class="border border-black mr-12">
            <thead>
                <tr>
                    <th class="p-2 w-[5%] border border-black text-center">No</th>
                    <th class="p-2 w-4 border border-black text-left">Tanggal Permohonan</th>
                    <th class="p-2 w-4 border border-black text-left">Nama Barang</th>
                    <th class="p-2 w-4 border border-black text-left">Jumlah Permohonan</th>
                    <th class="p-2 w-4 border border-black text-left">Jumlah Setuju</th>
                    <th class="p-2 w-4 border border-black text-left">Satuan</th>
                    <th class="p-2 w-4 border border-black text-left">Status</th>
                    <th class="p-2 w-4 border border-black text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody class="border border-black p-4">
                <tr v-for="(item, index) in props?.data" :key="index">
                    <td class="p-2 border border-black text-center">{{ item.index}}</td>
                    <td class="p-2 border border-black">{{ item.permohonan.tgl_permo }}</td>
                    <td class="p-2 border border-black">{{ item.barang.nama_brg }}</td>
                    <td class="p-2 border border-black">{{ item.jumlah_per }}</td>
                    <td class="p-2 border border-black">{{ item.jumlah_setuju??'-' }}</td>
                    <td class="p-2 border border-black">{{ item.barang.satuan }}</td>
                    <td class="p-2 border border-black">{{ item.permohonan.status }}</td>
                    <td class="p-2 border border-black">{{ item.ket_permo??'-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
</style>