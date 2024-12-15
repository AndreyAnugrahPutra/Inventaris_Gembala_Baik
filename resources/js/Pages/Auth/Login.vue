<script setup>
import { onMounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// import komponen primevue
import {
    InputText,
    Password,
    FloatLabel,
    Button,
    Toast,
    useToast
} from 'primevue'

onMounted(()=>
{
    checkNotif()
})

const props = defineProps({
    flash : Object
})

const toast = useToast()

const isLoading = ref(false)
const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        setTimeout(() =>
        {
            toast.add({
                severity : props.flash.notif_status,
                summary : 'Notifikasi',
                detail : props.flash.notif_message,
                life : 2000,
                group : 'tc'
            })
        },1000)
    }
}

const loginForm = useForm({
    username: null,
    password: null,
});

const submitForm = () => {
    isLoading.value = true
    loginForm.post(route('login.submit'), {
        onFinish : () => {
            checkNotif()
            isLoading.value = false
        },
        // onError: () => {
        //     toast.add({
        //         severity : 'error',
        //         summary : 'notifikasi',
        //         detail : 'Gagal Melakukan Login :(',
        //         life : 2000,
        //         group : 'tc'
        //     })
        //     isLoading.value = false
        // },
    });
};
</script>

<template>
    <Toast position="top-center" group="tc"/>
    <GuestLayout>
        <Head title="Login" />
        <form @submit.prevent="submitForm" class="flex flex-col gap-6" autocomplete="off">
            <!-- Username -->
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <InputText id="on_label" class="w-full" v-model="loginForm.username" :invalid="loginForm.errors.username?true:false"/>
                    <label for="on_label">Username</label>
                </FloatLabel>
                <span class="text-red-500" v-if="loginForm.errors.username">
                {{ loginForm.errors.username }}
                </span>
            </div>
            <!-- Password -->
            <div class="flex flex-col gap-y-2">
                <FloatLabel variant="on">
                    <Password :feedback="false" id="on_label" class="w-full" fluid toggleMask v-model="loginForm.password" :invalid="loginForm.errors.password?true:false"/>
                    <label for="on_label">Password</label>
                </FloatLabel>
                <span class="text-red-500" v-if="loginForm.errors.password">
                    {{ loginForm.errors.password }}
                </span>
            </div>
            <Button type="submit" :label="!isLoading?'LOGIN':null" :loading="isLoading" :disabled="!loginForm.username||!loginForm.password||isLoading"/>
        </form>
    </GuestLayout>
</template>
