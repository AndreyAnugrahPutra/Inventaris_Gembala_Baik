<script setup>
import { defineAsyncComponent, onMounted,ref } from 'vue'
import { Head, useForm ,router} from '@inertiajs/vue3'

// import komponen primevue
import { 
    Badge,
    Button,
    useConfirm,
    Dialog,
    DataTable, Column,
    FloatLabel,
    IconField,
    InputIcon,
    Password,
    InputText,
    Select,
    Toast,useToast
} from 'primevue'

import {FilterMatchMode} from '@primevue/core/api'
// import custom komponen
import AuthLayout from '@/Layouts/AuthLayout.vue'


onMounted(() =>
{
    checkNotif()
    dataUsersFix.value = props.dataUsers?.map((p,i) => ({index : i+1, ...p}))
})

const props = defineProps({
    flash : Object,
    dataUsers : Object,
    jkelUsers : Object,
    roleUsers : Object,
})

const pageTitle = 'Users'

const formType = ref()

const toast = useToast()
const dt = ref()

const showForm = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const confirm = useConfirm()

let dataUsersFix = ref([])

const userForm = useForm({
    id : null,
    username : null,
    nama : null,
    jkel : null,
    email : null,
    password : null,
    role : null,
})

const exportCSV = () => dt.value.exportCSV() 

const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        setTimeout(()=> 
            toast.add({
                severity : props.flash.notif_status,
                summary : 'notifikasi',
                detail : props.flash.notif_message,
                life : 4000,
                group : 'tr'
            })   
        ,1000)
    }
}

const hideForm = () =>
{
    showForm.value = false
    userForm.reset() 
    userForm.clearErrors()
}

const refreshPage = () => 
{
    checkNotif()
    showForm.value = false
    router.visit(route('users.page'))
}

const submitUser = () => userForm.post(route('users.tambah'), {
    onSuccess : () => refreshPage(),
    onError : () => { 
        showForm.value = true
        toast.add({
            severity : 'error',
            summary : 'notifikasi',
            detail : 'Gagal menambahkan user',
            life : 4000,
            group : 'tr'
        })
    } 
})

const updateUser = () => {
    confirm.require({
        message: `Update Data ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Update',
            severity: 'info'
        },
        accept: () => {
            showForm.value = false
            userForm.post(route('users.update'), {
                onSuccess : () => refreshPage(),
                onError : () => { 
                    formType.value = 'editData'
                    showForm.value = true
                    toast.add({
                        severity : 'error',
                        summary : 'notifikasi',
                        detail : 'Gagal update user',
                        life : 4000,
                        group : 'tr'
                    })
                } 
            })
        },

    }) 
}

const editUser = idx => 
{
    formType.value = 'editData'
    showForm.value = true
    userForm.id = dataUsersFix.value[idx-1]['id']
    userForm.username = dataUsersFix.value[idx-1]['username']
    userForm.nama = dataUsersFix.value[idx-1]['nama']
    userForm.email = dataUsersFix.value[idx-1]['email']
    userForm.jkel = dataUsersFix.value[idx-1]['jkel']
    userForm.role = dataUsersFix.value[idx-1]['role']
}

const hapusUser = (idx,username) => 
{
    userForm.id = dataUsersFix.value[idx-1]['id']

    confirm.require({
        message: `Hapus User ${username} ?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger'
        },
        accept: () => {
            showForm.value = false
            userForm.post(route('users.hapus'), {
                onSuccess : () => refreshPage(),
                onError : () => { 
                    userForm.reset()
                    toast.add({
                        severity : 'error',
                        summary : 'notifikasi',
                        detail : 'Gagal menghapus user',
                        life : 4000,
                        group : 'tr'
                    })
                } 
            })
        },

    }) 
}

</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :page-title="pageTitle">
        <template #pageContent>
            <!-- toast notifikasi -->
            <Toast position="top-right" group="tr" />
            <!-- form tambah & edit user -->
            <Dialog v-model:visible="showForm" modal :header="formType == 'tambahData' ? 'Tambah User' : 'Edit User'" class="w-[52rem]">
                <!-- form -->
                <form @submit.prevent="submitUser || updateUser" class="flex flex-wrap gap-[2rem] items-center my-1" autocomplete="off">
                    <!-- username -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <InputText class="w-[14rem]" inputId="custom" v-model="userForm.username"/>
                            <label for="custom">Username</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.username">
                            {{ userForm.errors.username }}
                        </span>
                    </div>
                    <!-- nama -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <InputText class="w-[14rem]" inputId="nama" v-model="userForm.nama"/>
                            <label for="nama">Nama</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.nama">
                            {{ userForm.errors.nama }}
                        </span>
                    </div>
                    <!-- jkel -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <Select class="w-[14rem]" inputId="jkel_user" editable v-model="userForm.jkel" :options="props.jkelUsers"/>
                            <label for="jkel_user">Jenis Kelamin</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.jkel">
                            {{ userForm.errors.jkel }}
                        </span>
                    </div>
                    <!-- email -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <InputText class="w-[14rem]" inputId="email" v-model="userForm.email" :invalid="!!userForm.errors.email"/>
                            <label for="email">Email</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500 text-clip" v-if="!!userForm.errors.email">
                            {{ userForm.errors.email }}
                        </span>
                    </div>
                    <!-- password -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <Password class="w-[14rem]" fluid inputId="password" v-model="userForm.password" toggleMask/>
                            <label for="password">password</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.password">
                            {{ userForm.errors.password }}
                        </span>
                    </div>
                    <!-- role -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <Select class="w-[14rem]" inputId="role_user" editable v-model="userForm.role" :options="props.roleUsers"/>
                            <label for="role_user">Role</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.role">
                            {{ userForm.errors.role }}
                        </span>
                    </div>
                    <!-- submit button -->
                     <div class="flex items-center gap-x-2">
                         <Button label="Batal" @click="hideForm()" severity="danger"/>
                         <Button label="Submit" @click="submitUser()" type="submit" v-if="formType=='tambahData'"/>
                         <Button label="Update" @click="updateUser()" v-if="formType=='editData'"/>
                     </div>

                </form>
            </Dialog>

            <div class="flex flex-col gap-[1rem]">
                <Button label="Tambah User" severity="success" @click="showForm = true, formType = 'tambahData'" class="w-[fit-content]" icon="pi pi-user-plus" size="small"/>
                <!-- Tabel Users -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable removable-sort striped-rows :value="dataUsersFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Users" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-external-link" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada User</span>
                        </template>
                        <Column sortable header="No" field="index"/>
                        <Column sortable header="Username" field="username"/>
                        <Column sortable header="Nama" field="nama"/>
                        <Column sortable header="Email" field="email"/>
                        <Column sortable header="Role" field="role">
                            <template #body="{data}">
                                <span class="rounded p-2 text-white text-sm capitalize" :class="{'bg-sky-500':data.role=='admin','bg-emerald-500':data.role=='manager','bg-slate-500':data.role=='officer','bg-amber-500':data.role=='accounting'}">{{ data.role }}</span>
                            </template>
                        </Column>
                        <Column header="Status">
                            <template #body="{data}">
                                <Badge :severity="data.is_login?'success':'danger'"/>
                            </template>
                        </Column>
                        <Column header="Action">
                            <template #body="{data}" v-if="$attrs.auth.user.role==='admin'">
                                <div class="flex items-center gap-x-2"  v-if="$attrs.auth.user.username!==data.username&&$attrs.auth.user.role!==data.role">
                                    <Button icon="pi pi-pen-to-square" @click="editUser(data.index)" size="small" outlined/>
                                    <Button @click="hapusUser(data.index, data.username)" severity="danger" icon="pi pi-trash" size="small" outlined />
                                </div>
                                <Button disabled severity="danger" icon="pi pi-ban" size="small" outlined v-else-if="$attrs.auth.user.username!==data.username&&$attrs.auth.user.role===data.role"/>
                                <Button icon="pi pi-pen-to-square" @click="editUser(data.index)" severity="success" size="small" v-else/>
                            </template>
                            <template #body="{data}" v-else>
                                <Button v-if="$attrs.auth.user.username===data.username && $attrs.auth.user.role===data.role" icon="pi pi-pen-to-square" @click="editUser(data.index)" severity="success" size="small"/>
                                <Button disabled severity="danger" icon="pi pi-ban" size="small" outlined v-else/>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </template>
    </AuthLayout>
</template>