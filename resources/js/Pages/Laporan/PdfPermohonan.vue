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

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

</script>

<template>
    <div class="m-4 flex flex-col gap-4 px-8">
        <KopSuratLayout :tanggal="props.tanggal">
            <span class="text-md text-center mb-4">Laporan Permohonan Barang Masuk</span>
            <table class="border border-black">
                <thead>
                    <tr class="text-sm">
                        <th class="p-2 w-[5%] border border-black text-center">No</th>
                        <th class="p-2 w-4 border border-black text-left">Tanggal Permohonan</th>
                        <th class="p-2 w-4 border border-black text-left">Tanggal Terima</th>
                        <th class="p-2 w-4 border border-black text-left">Nama Barang</th>
                        <th class="p-2 w-[10%] border border-black text-left">Jumlah Permohonan</th>
                        <th class="p-2 w-[5%] border border-black text-left">Jumlah Setuju</th>
                        <th class="p-2 w-[5%] border border-black text-left">Satuan</th>
                        <th class="p-2 w-[5%] border border-black text-left">Status</th>
                        <th class="p-2 w-[10%] border border-black text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="border border-black p-4">
                    <tr v-for="(item, index) in props?.data" :key="index" class="text-xs">
                        <td class="p-2 border border-black text-center">{{ item.index}}</td>
                        <td class="p-2 border border-black">{{ formatTanggal(item.permohonan.tgl_permo) }}</td>
                        <td class="p-2 border border-black">
                            {{ item.permohonan.tgl_diterima?formatTanggal(item.permohonan.tgl_diterima):'-' }}
                        </td>
                        <td class="p-2 border border-black">{{ item.barang.nama_brg }}</td>
                        <td class="p-2 border border-black">{{ item.jumlah_per }}</td>
                        <td class="p-2 border border-black">{{ item.jumlah_setuju??'-' }}</td>
                        <td class="p-2 border border-black">{{ item.barang.satuan }}</td>
                        <td class="p-2 border border-black">{{ item.permohonan.status }}</td>
                        <td class="p-2 border border-black">{{ item.ket_permo??'-' }}</td>
                    </tr>
                </tbody>
            </table>
        </KopSuratLayout>
    </div>
</template>

<style scoped>
</style>