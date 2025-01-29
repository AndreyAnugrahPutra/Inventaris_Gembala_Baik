<script setup>
const props = defineProps({
    tanggal : String,
})
</script>

<template>
    <div class="flex flex-col gap-y-0">
        <!-- Kop surat -->
        <div class="flex items-end justify-between border-b-[3px] border-black pb-5 mb-8">
            <!-- logo ayasan -->
            <img src="/logo_yayasan-transparent.png" alt="Logo yayasan" class="size-[120px]">
            <div class="text-center w-full">
                <span class="text-sm">
                    YAYASAN PENDIDIKAN DAN PERSEKOLAHAN KATOLIK <br>
                    FRANSISKUS ASISI KAB/KOTA JAYAPURA <br>
                </span>
                <span class="text-lg font-bold">
                    SD YPPK GEMBALA BAIK <br>
                    Ter-akreditasi =A= <br>
                </span>
                <span class="text-xs">
                    Jalan Gerilyawan No.62 Telp.(0967)581784 Abepura <br>
                    Email:sdgembalabaik@gmail.com Website:www.sdyypkgembalabaik.sch.id
                </span>
            </div>
            <img src="/logo_sd_gembala_baik.png" alt="Logo yayasan"  class="size-[120px]">
        </div>
        <slot/>
        <span class="self-end mt-8 mr-4">
            Jayapura, {{ props.tanggal }}
        </span>
        <!-- Kolom  TTD -->
        <div class="flex justify-between px-4">
            <div class="flex flex-col gap-y-24">
                <span>
                    Mengetahui,<br>
                    Kepala Sekolah<br>
                </span>
                <div>
                    <span class="border-b-2 border-black font-bold">
                        Clara Setiani, S.Pd<br>
                    </span>
                    <span class="text-sm">196803141999102001</span>
                </div>
            </div>
            <div class="flex flex-col gap-y-24">
                <span>
                    Penanggung Jawab <br>
                    Staff Tata Usaha
                </span>
                <div>
                    <span class="border-b-2 border-black font-bold">
                        Albertus Tethool, S.M<br>
                    </span>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
</style>