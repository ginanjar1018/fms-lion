<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import AlertModal from '@/Components/AlertModal.vue'

const departments = ref([])
const name = ref('')

const alertModal = ref({
    show: false,
    type: 'success',
    message: '',
    confirm: false,
})

const alertConfirmAction = ref(null)

const showAlert = (message, type = 'success') => {
    alertModal.value = {
        show: true,
        type,
        message,
        confirm: false,
    }

    alertConfirmAction.value = null
}

const showConfirm = (message, onConfirm) => {
    alertModal.value = {
        show: true,
        type: 'warning',
        message,
        confirm: true,
    }

    alertConfirmAction.value = onConfirm
}

const closeAlert = () => {
    alertModal.value.show = false
    alertConfirmAction.value = null
}

const handleAlertConfirm = async () => {
    const action = alertConfirmAction.value

    closeAlert()

    if (action) {
        await action()
    }
}

const fetchDepartments = async () => {
    const response = await axios.get('/departments/data')
    departments.value = response.data
}

const createDepartment = async () => {
    if (!name.value) {
        showAlert('Nama department wajib diisi.', 'warning')
        return
    }

    await axios.post('/departments', {
        name: name.value,
    })

    name.value = ''

    await fetchDepartments()

    showAlert('Department berhasil ditambahkan.', 'success')
}

const editingDepartment = ref(null)
const editDepartmentName = ref('')

const renameDepartment = (department) => {
    editingDepartment.value = department
    editDepartmentName.value = department.name
}
const saveDepartmentRename = async () => {
    if (!editingDepartment.value) {
        return
    }

    if (!editDepartmentName.value.trim()) {
        showAlert('Nama department wajib diisi.', 'warning')
        return
    }

    if (
        editDepartmentName.value.trim() ===
        editingDepartment.value.name
    ) {
        editingDepartment.value = null
        return
    }

    try {
        await axios.put(
            `/departments/${editingDepartment.value.id}`,
            {
                name: editDepartmentName.value.trim(),
            }
        )

        editingDepartment.value = null
        editDepartmentName.value = ''

        await fetchDepartments()

        showAlert('Department berhasil diperbarui.', 'success')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)?.[0]?.[0]

            showAlert(
                firstError || 'Nama department tidak valid.',
                'error'
            )
            return
        }

        showAlert(
            'Terjadi kesalahan saat memperbarui department.',
            'error'
        )
    }
}

const deleteDepartment = async (department) => {
    showConfirm(
        `Apakah kamu yakin ingin menghapus department "${department.name}"?`,
        async () => {
            try {
                await axios.delete(`/departments/${department.id}`)

                await fetchDepartments()

                showAlert('Department berhasil dihapus.', 'success')
            } catch (error) {
                showAlert(
                    'Terjadi kesalahan saat menghapus department.',
                    'error'
                )
            }
        }
    )
}

onMounted(() => {
    fetchDepartments()
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">

        <!-- Header -->
        <div class="mb-6">
            <a
                href="/dashboard"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
            >
                ← Kembali ke Dashboard
            </a>
        </div>

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Department Management
            </h1>

            <p class="mt-2 text-gray-500">
                Kelola department dokumen perusahaan.
            </p>
        </div>

        <!-- Form + Daftar Department -->
        <div
            class="grid items-start gap-6"
            style="grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);"
        >

            <!-- Form Tambah Department -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-xl"
                        >
                            🏢
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                Tambah Department
                            </h2>

                            <p class="text-sm text-gray-500">
                                Tambahkan department baru.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Department
                        </label>

                        <input
                            v-model="name"
                            type="text"
                            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: IT"
                        />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="button"
                            @click="createDepartment"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            + Tambah Department
                        </button>
                    </div>

                </div>
            </div>

            <!-- Daftar Department -->
            <div class="min-w-0">

                <!-- Header Daftar -->
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Daftar Department
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Department yang tersedia dalam sistem.
                        </p>
                    </div>

                    <div
                        class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600"
                    >
                        {{ departments.length }} Department
                    </div>
                </div>

                <!-- Scroll Area -->
                <div
                    class="h-[400px] overflow-y-auto overflow-x-hidden pr-2"
                >

                    <div
                        v-for="department in departments"
                        :key="department.id"
                        class="mb-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md"
                    >
                        <div class="flex items-center justify-between gap-4">

                            <!-- Department Name -->
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-lg"
                                >
                                    🏢
                                </div>

                                <div class="truncate font-medium text-gray-800">
                                    {{ department.name }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex shrink-0 gap-2">
                                <button
                                    type="button"
                                    @click="renameDepartment(department)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-yellow-600"
                                >
                                    ✎ Rename
                                </button>

                                <button
                                    type="button"
                                    @click="deleteDepartment(department)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-700"
                                >
                                    🗑 Delete
                                </button>
                            </div>

                        </div>
                    </div>

                    <p
                        v-if="departments.length === 0"
                        class="rounded-xl border border-gray-200 bg-white px-6 py-10 text-center text-sm text-gray-500"
                    >
                        Belum ada department.
                    </p>

                </div>
            </div>
        </div>

        <!-- Alert Modal -->
        <AlertModal
            :show="alertModal.show"
            :type="alertModal.type"
            :message="alertModal.message"
            :confirm="alertModal.confirm"
            @close="closeAlert"
            @confirm="handleAlertConfirm"
        />

        <!-- Rename Department Modal -->
        <div
            v-if="editingDepartment"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">

                <h3 class="text-lg font-semibold text-gray-900">
                    Rename Department
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Masukkan nama department baru.
                </p>

                <input
                    v-model="editDepartmentName"
                    type="text"
                    class="mt-4 w-full rounded-md border-gray-300 shadow-sm"
                    placeholder="Nama Department"
                />

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        @click="editingDepartment = null"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        @click="saveDepartmentRename"
                    >
                        Simpan
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>