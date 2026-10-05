<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import AlertModal from '@/Components/AlertModal.vue'

const folders = ref([])

const name = ref('')
const parentId = ref(null)
const expandedFolders = ref(new Set())

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

const fetchFolders = async () => {
    const response = await axios.get('/folders/data')
    folders.value = response.data
}

const createFolder = async () => {
    if (!name.value) {
        showAlert('Nama folder wajib diisi.', 'warning')
        return
    }

    try {
        await axios.post('/folders', {
            name: name.value,
            parent_id: parentId.value,
        })

        name.value = ''
        parentId.value = null

        await fetchFolders()

        showAlert('Folder berhasil ditambahkan.', 'success')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)?.[0]?.[0]

            showAlert(
                firstError || 'Data folder tidak valid.',
                'error'
            )
            return
        }

        showAlert(
            'Terjadi kesalahan saat menambahkan folder.',
            'error'
        )
    }
}

const editingFolder = ref(null)
const editFolderName = ref('')

const renameFolder = (folder) => {
    editingFolder.value = folder
    editFolderName.value = folder.name
}
const saveFolderRename = async () => {
    if (!editingFolder.value) {
        return
    }

    if (!editFolderName.value.trim()) {
        showAlert('Nama folder wajib diisi.', 'warning')
        return
    }

    if (
        editFolderName.value.trim() ===
        editingFolder.value.name
    ) {
        editingFolder.value = null
        return
    }

    try {
        await axios.put(
            `/folders/${editingFolder.value.id}`,
            {
                name: editFolderName.value.trim(),
                parent_id: editingFolder.value.parent_id,
            }
        )

        editingFolder.value = null
        editFolderName.value = ''

        await fetchFolders()

        showAlert('Folder berhasil diperbarui.', 'success')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)?.[0]?.[0]

            showAlert(
                firstError || 'Nama folder tidak valid.',
                'error'
            )
            return
        }

        showAlert(
            'Terjadi kesalahan saat memperbarui folder.',
            'error'
        )
    }
}

const deleteFolder = (folder) => {
    showConfirm(
        `Apakah kamu yakin ingin menghapus folder "${folder.name}"?`,
        async () => {
            try {
                await axios.delete(`/folders/${folder.id}`)

                await fetchFolders()

                showAlert('Folder berhasil dihapus.', 'success')
            } catch (error) {
                showAlert(
                    'Terjadi kesalahan saat menghapus folder.',
                    'error'
                )
            }
        }
    )
}
const getAllFolders = (folders, level = 0) => {
    let result = []

    folders.forEach((folder) => {
        result.push({
            ...folder,
            level: level,
        })

        if (folder.children_recursive) {
            result = result.concat(
                getAllFolders(folder.children_recursive, level + 1)
            )
        }
    })

    return result
}
const toggleFolder = (folderId) => {
    const updated = new Set(expandedFolders.value)

    if (updated.has(folderId)) {
        updated.delete(folderId)
    } else {
        updated.add(folderId)
    }

    expandedFolders.value = updated
}
const getVisibleFolders = (folders, level = 0) => {
    let result = []

    folders.forEach((folder) => {
        result.push({
            ...folder,
            level: level,
        })

        if (
            folder.children_recursive &&
            expandedFolders.value.has(folder.id)
        ) {
            result = result.concat(
                getVisibleFolders(folder.children_recursive, level + 1)
            )
        }
    })

    return result
}
onMounted(() => {
    fetchFolders()
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
                Folder Management
            </h1>

            <p class="mt-2 text-gray-500">
                Kelola struktur folder dokumen perusahaan.
            </p>
        </div>

        <!-- Form + Daftar Folder -->
        <div
            class="grid items-start gap-6"
            style="grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);"
        >

            <!-- Form Tambah Folder -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            >
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-xl"
                        >
                            📁
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                Tambah Folder
                            </h2>

                            <p class="text-sm text-gray-500">
                                Buat folder baru untuk dokumen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-6">

                    <!-- Nama Folder -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Folder
                        </label>

                        <input
                            v-model="name"
                            type="text"
                            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Dokumen"
                        />
                    </div>

                    <!-- Parent Folder -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Parent Folder
                        </label>

                        <select
                            v-model="parentId"
                            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option :value="null">
                                Root Folder
                            </option>

                            <option
                                v-for="folder in getAllFolders(folders)"
                                :key="folder.id"
                                :value="folder.id"
                            >
                                {{ folder.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="button"
                            @click="createFolder"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            + Tambah Folder
                        </button>
                    </div>
                </div>
            </div>

            <!-- Daftar Folder -->
            <div class="min-w-0">

                <!-- Header Daftar -->
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Daftar Folder
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Struktur folder dokumen dalam sistem.
                        </p>
                    </div>

                    <div
                        class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600"
                    >
                        {{ folders.length }} Folder
                    </div>
                </div>

                <!-- Scroll Area -->
                <div
                    class="h-[400px] overflow-y-auto overflow-x-auto pr-2"
                >
                    <div
                        v-for="folder in getVisibleFolders(folders)"
                        :key="folder.id"
                        class="mb-2 rounded-xl border border-gray-200 bg-white p-3 shadow-sm transition hover:shadow-md"
                        :style="{ marginLeft: `${folder.level * 24}px` }"
                    >
                        <div class="flex items-center justify-between gap-4">

                            <!-- Folder Name -->
                            <div class="flex min-w-0 items-center gap-2">
                                <button
                                    v-if="folder.children_recursive?.length"
                                    type="button"
                                    @click="toggleFolder(folder.id)"
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs text-gray-500 transition hover:bg-gray-100 hover:text-gray-800"
                                >
                                    {{ expandedFolders.has(folder.id) ? '▼' : '▶' }}
                                </button>

                                <span
                                    v-else
                                    class="h-7 w-7 shrink-0"
                                ></span>

                                <div class="truncate font-medium text-gray-800">
                                    📁 {{ folder.name }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex shrink-0 gap-2">
                                <button
                                    type="button"
                                    @click="renameFolder(folder)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-yellow-600"
                                >
                                    ✎ Rename
                                </button>

                                <button
                                    type="button"
                                    @click="deleteFolder(folder)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-700"
                                >
                                    🗑 Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <p
                        v-if="folders.length === 0"
                        class="rounded-xl border border-gray-200 bg-white px-6 py-10 text-center text-sm text-gray-500"
                    >
                        Belum ada folder.
                    </p>
                </div>
            </div>
        </div>
<AlertModal
    :show="alertModal.show"
    :type="alertModal.type"
    :message="alertModal.message"
    :confirm="alertModal.confirm"
    @close="closeAlert"
    @confirm="handleAlertConfirm"
/>
<div
    v-if="editingFolder"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
>
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900">
            Rename Folder
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Masukkan nama folder baru.
        </p>

        <input
            v-model="editFolderName"
            type="text"
            class="mt-4 w-full rounded-md border-gray-300 shadow-sm"
            placeholder="Nama Folder"
        />

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                @click="editingFolder = null"
            >
                Batal
            </button>

            <button
                type="button"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                @click="saveFolderRename"
            >
                Simpan
            </button>
        </div>
    </div>
</div>
    </div>
</template>