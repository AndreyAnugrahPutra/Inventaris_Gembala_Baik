<script setup>
import { onMounted,ref } from 'vue'
import { Head, useForm ,router} from '@inertiajs/vue3'

// import komponen primevue
import { 
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
    useToast
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
    unitUsers : Object,
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
    id_user : null,
    username : null,
    email : null,
    password : null,
    id_role : null,
    id_unit : null,
})

const exportCSV = () => dt.value.exportCSV() 

const checkNotif = () =>
{
    if(props.flash.notif_status)
    {
        toast.add({
            severity : props.flash.notif_status,
            summary : 'notifikasi',
            detail : props.flash.notif_message,
            life : 4000,
            group : 'tr'
        })
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
    router.visit(route('admin.users.page'))
}

const submitUser = () => {
    confirm.require({
        message: `Tambah User ${userForm.username??''}?`,
        header: 'Peringatan',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Tambah',
            severity: 'info'
        },
        accept: () => {
            userForm.post(route('admin.users.tambah'), {
                onSuccess : () => refreshPage()
            })
        },

    }) 
}

const updateUser = () => {
    confirm.require({
        message: `Update Data ${userForm.username??''}?`,
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
            userForm.post(route('admin.users.update'), {
                onSuccess : () => refreshPage()
            })
        },

    }) 
}
const editUser = idx => 
{
    formType.value = 'editData'
    showForm.value = true
    userForm.id_user = dataUsersFix.value[idx-1]['id_user']
    userForm.username = dataUsersFix.value[idx-1]['username']
    userForm.nama = dataUsersFix.value[idx-1]['nama']
    userForm.email = dataUsersFix.value[idx-1]['email']
    userForm.id_role = dataUsersFix.value[idx-1]['id_role']
    userForm.id_unit = dataUsersFix.value[idx-1]['id_unit']
}

const hapusUser = (idx,username) => 
{
    userForm.id = dataUsersFix.value[idx-1]['id_user']

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
            userForm.post(route('admin.users.hapus'), {
                onSuccess : () => refreshPage()
            })
        },

    }) 
}

</script>

<template>
    <Head :title="pageTitle"/>
    <AuthLayout :page-title="pageTitle">
        <template #pageContent>
            <Dialog @hide="hideForm()" v-model:visible="showForm" modal :header="formType == 'tambahData' ? 'Tambah User' : 'Edit User'" class="w-[52rem]" >
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
                            <Select class="w-[14rem]" inputId="role_user" v-model="userForm.id_role" :options="props.roleUsers" optionValue="id_role" optionLabel="nama_role"/>
                            <label for="role_user">Role</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.id_role">
                            {{ userForm.errors.id_role }}
                        </span>
                    </div>
                    <!-- role -->
                    <div class="flex flex-col h-10">
                        <FloatLabel variant="on">
                            <Select class="w-[14rem]" inputId="unit_user" v-model="userForm.id_unit" :options="props.unitUsers" optionValue="id_unit" optionLabel="nama_unit"/>
                            <label for="unit_user">Unit</label>
                        </FloatLabel>
                        <span class="text-sm text-red-500" v-if="!!userForm.errors.id_unit">
                            {{ userForm.errors.id_unit }}
                        </span>
                    </div>
                    <!-- submit button -->
                    <div class="flex items-center gap-x-2 w-full">
                        <Button label="Batal" @click="hideForm()" severity="danger"/>
                        <Button label="Submit" @click="submitUser()" type="submit" v-if="formType=='tambahData'"/>
                        <Button label="Update" @click="updateUser()" v-if="formType=='editData'"/>
                    </div>
                </form>
            </Dialog>

            <div class="flex flex-col gap-[1rem]">
                <Button label="Tambah User" severity="success" @click="showForm = true, formType = 'tambahData'" class="w-[fit-content]" icon="pi pi-user-plus" size="small"/>
                <!-- datatabel Users -->
                <div class="rounded-lg size-full overflow-hidden">
                    <DataTable exportFilename="data-users" removable-sort striped-rows :value="dataUsersFix" v-model:filters="filters" ref="dt" :rows="5" paginator>
                        <template #header>
                            <div class="flex justify-between items-center gap-x-2">
                                <IconField class="w-full">
                                    <InputIcon>
                                        <i class="pi pi-search me-4" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Cari Data Users" size="small" fluid/>
                                </IconField>
                                <Button icon="pi pi-print" severity="contrast" @click="exportCSV()" label="Export" size="small"/>
                            </div>
                        </template>
                        <template #empty>
                            <span class="flex justify-center">Tidak Ada User</span>
                        </template>
                        <Column :exportable="false" sortable header="No" field="index" class="w-4"/>
                        <Column sortable header="Username" field="username"/>
                        <Column sortable header="Email" field="email"/>
                        <Column sortable header="Role" field="role.nama_role"/>
                        <Column sortable header="Unit" field="unit.nama_unit"/>
                        <Column header="Action">
                            <template #body="{data}" v-if="$attrs.auth.user.id_role===1">
                                <div class="flex items-center gap-x-2"  v-if="$attrs.auth.user.username!==data.username&&$attrs.auth.user.id_role!==data.role.id_role">
                                    <Button icon="pi pi-pen-to-square" @click="editUser(data.index)" size="small" outlined/>
                                    <Button @click="hapusUser(data.index, data.username)" severity="danger" icon="pi pi-trash" size="small" outlined />
                                </div>
                                <Button disabled severity="danger" icon="pi pi-ban" size="small" outlined v-else-if="$attrs.auth.user.username!==data.username&&$attrs.auth.user.id_role===data.role.id_role"/>
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