<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import axios from 'axios'

const files = ref([])
const folders = ref([])
const expandedFolders = ref(new Set())
const selectedFolderId = ref(null)
const departments = ref([])
const searchQuery = ref('')
const selectedDepartmentId = ref('')
const selectedFile = ref(null)

const pagination = ref(null)

const fetchFiles = async (url = '/files/data') => {
    const response = await axios.get(url, {
        params: {
            search: searchQuery.value || undefined,
            department_id: selectedDepartmentId.value || undefined,
            folder_id: selectedFolderId.value || undefined,
        },
    })

    files.value = response.data.data
    pagination.value = response.data
}
const goToPage = (url) => {
    if (!url) {
        return
    }

    fetchFiles(url)
    selectedFile.value = null
}
const fetchFolders = async () => {
    const response = await axios.get('/folders/data')
    folders.value = response.data
}
const fetchDepartments = async () => {
    const response = await axios.get('/departments/data')
    departments.value = response.data
}
const getAllFolders = (folders, level = 0) => {
    let result = []

    folders.forEach((folder) => {
        result.push({
            ...folder,
            level: level,
        })

        if (
            expandedFolders.value.has(folder.id) &&
            folder.children_recursive?.length
        ) {
            result = result.concat(
                getAllFolders(folder.children_recursive, level + 1)
            )
        }
    })

    return result
}
const toggleFolder = (folderId) => {
    if (expandedFolders.value.has(folderId)) {
        expandedFolders.value.delete(folderId)

        if (selectedFolderId.value === folderId) {
            selectedFolderId.value = null
        }
    } else {
        expandedFolders.value.add(folderId)
    }

    expandedFolders.value = new Set(expandedFolders.value)
}
const selectFolder = (folderId) => {
    if (selectedFolderId.value === folderId) {
        selectedFolderId.value = null
    } else {
        selectedFolderId.value = folderId
    }

    selectedFile.value = null
    fetchFiles()
}
const getFolderPath = (folders, folderId, path = []) => {
    for (const folder of folders) {
        const currentPath = [...path, folder]

        if (folder.id === folderId) {
            return currentPath
        }

        if (folder.children_recursive?.length) {
            const result = getFolderPath(
                folder.children_recursive,
                folderId,
                currentPath
            )

            if (result.length > 0) {
                return result
            }
        }
    }

    return []
}

const selectedFolderPath = computed(() => {
    if (selectedFolderId.value === null) {
        return []
    }

    return getFolderPath(folders.value, selectedFolderId.value)
})
const selectFile = (file) => {
    selectedFile.value = file
}
const visibleFiles = computed(() => {
    return files.value.filter((file) => {
        const matchesFolder =
            selectedFolderId.value === null ||
            file.folder_id === selectedFolderId.value

        const matchesSearch =
            searchQuery.value === '' ||
            file.title
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            file.file_name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())

        const matchesDepartment =
            selectedDepartmentId.value === '' ||
            file.department_id === Number(selectedDepartmentId.value)

        return (
            matchesFolder &&
            matchesSearch &&
            matchesDepartment
        )
    })
})

watch(
    [searchQuery, selectedDepartmentId],
    () => {
        selectedFile.value = null
        fetchFiles()
    }
)

onMounted(() => {
    fetchFiles()
    fetchFolders()
    fetchDepartments()
})
</script>



<template>
    <div class="min-h-screen bg-gray-50 p-6">

        <!-- Header -->
        <div class="mb-6">
            <a
                href="/viewer/dashboard"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
            >
                ← Kembali ke Dashboard
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                My Documents
            </h1>

            <p class="mt-2 text-gray-500">
                Lihat dan download dokumen perusahaan.
            </p>
        </div>

        <!-- Breadcrumb -->
        <div
            v-if="selectedFolderPath.length > 0"
            class="mb-6 flex flex-wrap items-center gap-1 text-sm text-gray-600"
        >
            <span>📁</span>

            <template
                v-for="(folder, index) in selectedFolderPath"
                :key="folder.id"
            >
                <span
                    v-if="index > 0"
                    class="mx-1 text-gray-400"
                >
                    /
                </span>

                <button
                    type="button"
                    @click="selectFolder(folder.id)"
                    :class="
                        folder.id === selectedFolderId
                            ? 'font-semibold text-blue-700'
                            : 'text-gray-600 hover:text-blue-700'
                    "
                >
                    {{ folder.name }}
                </button>
            </template>
        </div>

        <!-- MAIN LAYOUT -->
        <div
            class="grid items-start gap-6"
            style="grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);"
        >

            <!-- ================================================= -->
            <!-- LEFT : FOLDER -->
            <!-- ================================================= -->
            <div class="min-w-0">

                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >

                    <!-- Folder Header -->
                    <div
                        class="border-b border-gray-100 bg-white px-5 py-4"
                    >
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-lg"
                                >
                                    📁
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-gray-900">
                                        Daftar Folder
                                    </h2>

                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Pilih folder untuk melihat dokumen.
                                    </p>
                                </div>
                            </div>

                            <span
                                class="shrink-0 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600"
                            >
                                {{ folders.length }}
                            </span>

                        </div>
                    </div>

                    <!-- Folder List -->
                    <div
                        class="h-[500px] overflow-y-auto overflow-x-auto p-4"
                    >

                        <div
                            v-for="folder in getAllFolders(folders)"
                            :key="folder.id"
                            class="mb-2 min-w-max"
                            :style="{
                                marginLeft: `${folder.level * 20}px`
                            }"
                        >

                            <div
                                class="rounded-xl border p-3 transition"
                                :class="
                                    selectedFolderId === folder.id
                                        ? 'border-blue-200 bg-blue-50 shadow-sm'
                                        : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm'
                                "
                            >

                                <div class="flex items-center gap-2">

                                    <!-- Expand -->
                                    <button
                                        v-if="folder.children_recursive?.length"
                                        type="button"
                                        @click="toggleFolder(folder.id)"
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs text-gray-500 transition hover:bg-gray-100 hover:text-gray-800"
                                    >
                                        {{
                                            expandedFolders.has(folder.id)
                                                ? '▼'
                                                : '▶'
                                        }}
                                    </button>

                                    <span
                                        v-else
                                        class="h-7 w-7 shrink-0"
                                    ></span>

                                    <!-- Folder Button -->
                                    <button
                                        type="button"
                                        @click="selectFolder(folder.id)"
                                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-left text-sm transition"
                                        :class="
                                            selectedFolderId === folder.id
                                                ? 'font-semibold text-blue-700'
                                                : 'text-gray-700 hover:text-blue-700'
                                        "
                                    >
                                        <span>📁</span>
                                        <span>{{ folder.name }}</span>
                                    </button>

                                </div>

                            </div>
                        </div>

                        <!-- Empty Folder -->
                        <div
                            v-if="folders.length === 0"
                            class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center"
                        >
                            <div class="text-3xl">
                                📁
                            </div>

                            <p class="mt-3 text-sm font-medium text-gray-700">
                                Belum ada folder
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Folder yang tersedia akan muncul di sini.
                            </p>
                        </div>

                    </div>
                </div>

            </div>


            <!-- ================================================= -->
            <!-- RIGHT : DOCUMENT -->
            <!-- ================================================= -->
            <div class="min-w-0">

                <!-- Search -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >

                    <div class="border-b border-gray-100 px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-lg"
                            >
                                🔎
                            </div>

                            <div>
                                <h2 class="text-base font-semibold text-gray-900">
                                    Cari Dokumen
                                </h2>

                                <p class="mt-0.5 text-xs text-gray-500">
                                    Gunakan pencarian atau filter department.
                                </p>
                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <div class="grid gap-4 md:grid-cols-2">

                            <!-- Search Input -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Cari Dokumen
                                </label>

                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Judul atau nama file..."
                                    class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Department -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Department
                                </label>

                                <select
                                    v-model="selectedDepartmentId"
                                    class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        Semua Department
                                    </option>

                                    <option
                                        v-for="department in departments"
                                        :key="department.id"
                                        :value="department.id"
                                    >
                                        {{ department.name }}
                                    </option>
                                </select>
                            </div>

                        </div>

                    </div>
                </div>


                <!-- ================================================= -->
                <!-- DOCUMENT AREA -->
                <!-- ================================================= -->
                <div
                    class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >

                    <!-- Document Header -->
                    <div
                        class="flex items-center justify-between border-b border-gray-100 px-6 py-4"
                    >

                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ selectedFolderId !== null
                                    ? 'File dalam Folder'
                                    : 'Dokumen'
                                }}
                            </h2>

                            <p class="mt-0.5 text-xs text-gray-500">
                                {{
                                    selectedFolderId !== null
                                        ? 'Dokumen pada folder yang dipilih.'
                                        : 'Pilih folder atau gunakan pencarian untuk melihat dokumen.'
                                }}
                            </p>
                        </div>

                        <span
                            v-if="selectedFolderId !== null || searchQuery.trim() !== '' || selectedDepartmentId !== ''"
                            class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600"
                        >
                            {{ visibleFiles.length }} File
                        </span>

                    </div>


                    <!-- ================================================= -->
                    <!-- FILE LIST -->
                    <!-- ================================================= -->
                    <div
                        v-if="
                            selectedFolderId !== null ||
                            searchQuery.trim() !== '' ||
                            selectedDepartmentId !== ''
                        "
                        class="h-[500px] overflow-y-auto overflow-x-auto p-5"
                    >

                        <div
                            v-for="file in visibleFiles"
                            :key="file.id"
                            class="mb-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-gray-300 hover:shadow-md"
                        >

                            <div
                                class="flex items-center justify-between gap-4"
                            >

                                <!-- File Information -->
                                <div class="min-w-0">

                                    <button
                                        type="button"
                                        @click="selectFile(file)"
                                        class="block max-w-full truncate text-left font-semibold text-blue-700 transition hover:text-blue-800 hover:underline"
                                    >
                                        📄 {{ file.title }}
                                    </button>

                                    <p
                                        class="mt-1 max-w-full truncate text-sm text-gray-500"
                                    >
                                        {{ file.file_name }}
                                    </p>

                                    <div
                                        class="mt-2 flex flex-wrap gap-2"
                                    >

                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
                                        >
                                            {{ file.department?.name }}
                                        </span>

                                        <span
                                            class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                        >
                                            📁
                                            {{ file.folder?.name ?? 'Root Folder' }}
                                        </span>

                                        <span
                                            class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                        >
                                            👤
                                            {{ file.uploader?.name }}
                                        </span>

                                    </div>

                                </div>

                                <!-- Download -->
                                <a
                                    :href="`/files/${file.id}/download`"
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-green-700"
                                >
                                    ↓ Download
                                </a>

                            </div>

                        </div>


                        <!-- No Result -->
                        <div
                            v-if="visibleFiles.length === 0"
                            class="flex h-full min-h-[360px] items-center justify-center"
                        >
                            <div class="text-center">

                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-2xl"
                                >
                                    📄
                                </div>

                                <p class="mt-4 text-sm font-semibold text-gray-700">
                                    Tidak ada dokumen
                                </p>

                                <p class="mt-1 text-sm text-gray-400">
                                    Tidak ditemukan dokumen yang sesuai.
                                </p>

                            </div>
                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- EMPTY STATE : BELUM PILIH FOLDER -->
                    <!-- ================================================= -->
                    <div
                        v-else
                        class="flex min-h-[500px] items-center justify-center px-6"
                    >

                        <div class="max-w-sm text-center">

                            <div
                                class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 text-4xl"
                            >
                                📁
                            </div>

                            <h3 class="mt-5 text-lg font-semibold text-gray-900">
                                Pilih Folder
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Pilih salah satu folder di sebelah kiri
                                untuk melihat dokumen yang tersedia.
                                Kamu juga dapat menggunakan pencarian
                                atau filter department.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PAGINATION -->
                <!-- ================================================= -->
                <div
                    v-if="
                        (selectedFolderId !== null ||
                            searchQuery.trim() !== '' ||
                            selectedDepartmentId !== '') &&
                        pagination &&
                        pagination.last_page > 1
                    "
                    class="mt-5 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm"
                >

                    <button
                        type="button"
                        @click="goToPage(pagination.prev_page_url)"
                        :disabled="!pagination.prev_page_url"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        ← Previous
                    </button>

                    <span class="text-sm font-medium text-gray-600">
                        Halaman {{ pagination.current_page }}
                        dari {{ pagination.last_page }}
                    </span>

                    <button
                        type="button"
                        @click="goToPage(pagination.next_page_url)"
                        :disabled="!pagination.next_page_url"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Next →
                    </button>

                </div>


                <!-- ================================================= -->
                <!-- FILE DETAIL -->
                <!-- ================================================= -->
                <div
                    v-if="selectedFile"
                    class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >

                    <div
                        class="flex items-center justify-between border-b border-gray-100 px-6 py-4"
                    >

                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                File Detail
                            </h2>

                            <p class="mt-0.5 text-xs text-gray-500">
                                Informasi lengkap dokumen.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="selectedFile = null"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                        >
                            ✕ Tutup
                        </button>

                    </div>

                    <div class="grid gap-5 p-6 md:grid-cols-2">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Folder
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-800">
                                📁
                                {{ selectedFile.folder?.name ?? 'Root Folder' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                File Name
                            </p>
                            <p class="mt-1 break-all text-sm font-medium text-gray-800">
                                {{ selectedFile.file_name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Title
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{ selectedFile.title }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Department
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{ selectedFile.department?.name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Uploaded By
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{ selectedFile.uploader?.name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Upload Date
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-800">
                                {{
                                    new Date(
                                        selectedFile.created_at
                                    ).toLocaleDateString('id-ID')
                                }}
                            </p>
                        </div>

                    </div>

                    <div class="border-t border-gray-100 px-6 py-5">
                        <a
                            :href="`/files/${selectedFile.id}/download`"
                            class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"
                        >
                            ↓ Download File
                        </a>
                    </div>

                </div>

            </div>
        </div>

    </div>
</template>


