<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    stats: Object,
    latestFiles: Array,
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola dokumen dan data perusahaan dalam satu tempat.
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Statistik -->
                <div class="grid gap-6 md:grid-cols-3">

                    <!-- Total Folder -->
                    <div
                        class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Total Folder
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ stats.folders }}
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Folder tersimpan
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-2xl"
                            >
                                📁
                            </div>
                        </div>
                    </div>

                    <!-- Total File -->
                    <div
                        class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Total File
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ stats.files }}
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Dokumen tersimpan
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-2xl"
                            >
                                📄
                            </div>
                        </div>
                    </div>

                    <!-- Total Department -->
                    <div
                        class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">
                                    Total Department
                                </p>

                                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                                    {{ stats.departments }}
                                </p>

                                <p class="mt-1 text-xs text-gray-400">
                                    Department aktif
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-2xl"
                            >
                                🏢
                            </div>
                        </div>
                    </div>

                </div>

                <!-- File Terbaru -->
                <div
                    class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between border-b border-gray-100 px-6 py-5"
                    >
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                File Terbaru
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Dokumen yang baru ditambahkan ke dalam sistem.
                            </p>
                        </div>

                        <div
                            class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600"
                        >
                            {{ latestFiles.length }} File
                        </div>
                    </div>

                    <div
    v-if="latestFiles.length > 0"
    class="max-h-[400px] overflow-y-auto overflow-x-auto"
>
    <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th
                                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Judul
                                    </th>

                                    <th
                                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Department
                                    </th>

                                    <th
                                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Folder
                                    </th>

                                    <th
                                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Uploaded By
                                    </th>

                                    <th
                                        class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Tanggal
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="file in latestFiles"
                                    :key="file.id"
                                    class="border-b border-gray-100 last:border-b-0 transition hover:bg-gray-50"
                                >
                                    <!-- Judul -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-lg"
                                            >
                                                📄
                                            </div>

                                            <div class="min-w-0">
                                                <p
                                                    class="truncate font-medium text-gray-900"
                                                >
                                                    {{ file.title }}
                                                </p>

                                                <p
                                                    class="mt-0.5 max-w-xs truncate text-xs text-gray-400"
                                                >
                                                    {{ file.file_name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Department -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
                                        >
                                            {{ file.department?.name }}
                                        </span>
                                    </td>

                                    <!-- Folder -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                        >
                                            📁 {{ file.folder?.name ?? 'Root Folder' }}
                                        </span>
                                    </td>

                                    <!-- Uploaded By -->
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ file.uploader?.name }}
                                    </td>

                                    <!-- Tanggal -->
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ new Date(file.created_at).toLocaleDateString('id-ID') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="px-6 py-12 text-center"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-2xl"
                        >
                            📄
                        </div>

                        <p class="mt-3 text-sm font-medium text-gray-700">
                            Belum ada file
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            Belum ada dokumen yang tersimpan di dalam sistem.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>