<script setup>
import { onMounted } from 'vue'
import KopSuratLayout from '@/Layouts/KopSuratLayout.vue'
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

const formatTanggal = tgl => {
      const parts = tgl.split('-');
      return parts.reverse().join('-');
}

</script>

<template>
    <div class="m-2 flex flex-col gap-4 px-4">
        <KopSuratLayout :tanggal="props.tanggal">
            <span class="text-lg text-center mb-4">Laporan Barang Keluar</span>
            <table class="border border-black">
                <thead>
                    <tr class="text-xs">
                        <th class="p-2 w-[2%] border border-black text-center">No</th>
                        <th class="p-2 w-[5%] border border-black text-left">Tanggal Permohonan</th>
                        <th class="p-2 w-[20%] border border-black text-left">Tanggal Terima</th>
                        <th class="p-2 w-4 border border-black text-left">Pemohon</th>
                        <th class="p-2 w-[5%] border border-black text-left">Unit</th>
                        <th class="p-2 w-4 border border-black text-left">Barang</th>
                        <th class="p-2 w-[13%] border border-black text-left">Jumlah Permohonan</th>
                        <th class="p-2 w-4 border border-black text-left">Jumlah Disetujui</th>
                        <th class="p-2 w-4 border border-black text-left">Satuan</th>
                        <th class="p-2 w-[5%] border border-black text-left">Status</th>
                        <th class="p-2 w-[10%] border border-black text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="border border-black p-4">
                    <tr v-for="(item, index) in props.data?.data" :key="index" class="text-xs">
                        <td class="p-2 border border-black text-center">{{ item.index}}</td>
                        <td class="p-2 border border-black">{{ formatTanggal(item.barang_keluar.tgl_bk) }}</td>
                        <td class="p-2 border border-black">{{ item.barang_keluar.tgl_diterima?formatTanggal(item.barang_keluar.tgl_diterima):'-' }}</td>
                        <td class="p-2 border border-black">{{ item.barang_keluar.user.username }}</td>
                        <td class="p-2 border border-black">{{ item.barang_keluar.user.unit.nama_unit }}</td>
                        <td class="p-2 border border-black">{{ item.barang.nama_brg}}</td>
                        <td class="p-2 border border-black">{{ item.jum_bk }}</td>
                        <td class="p-2 border border-black">{{ item.jum_setuju_bk??'-' }}</td>
                        <td class="p-2 border border-black">{{ item.barang.satuan }}</td>
                        <td class="p-2 border border-black">{{ item.barang_keluar.status_bk }}</td>
                        <td class="p-2 border border-black">{{ item.ket_bk??'-' }}</td>
                    </tr>
                </tbody>
            </table>
        </KopSuratLayout>
    </div>
</template>

<style scoped>
</style>