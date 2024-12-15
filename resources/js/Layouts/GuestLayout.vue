<script setup>
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-slate-100 pt-6 sm:justify-center sm:pt-0"
    >
        <!-- <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div> -->
        <div
        class="flex flex-col gap-y-8 mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-xl sm:max-w-md sm:rounded-lg"
        >
        <h1 class="text-2xl self-center">INVENTARIS BARANG</h1>
            <slot />
        </div>
    </div>
</template>
