<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
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
                    Akses dan kelola dokumen yang tersedia untuk Anda.
                </p>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Welcome Hero -->
                <div
                    class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 shadow-sm"
                >
                    <div class="px-6 py-8 sm:px-8 sm:py-10">
                        <div
                            class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                        >

                            <div class="max-w-2xl text-white">

                                <div
                                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-medium text-blue-50 ring-1 ring-white/20"
                                >
                                    📁 File Management System
                                </div>

                                <h1
                                    class="text-2xl font-bold tracking-tight sm:text-3xl"
                                >
                                    Selamat Datang 👋
                                </h1>

                                <p
                                    class="mt-3 text-sm leading-6 text-blue-100 sm:text-base"
                                >
                                    Temukan, lihat, dan unduh dokumen perusahaan
                                    dengan mudah melalui satu tempat.
                                </p>

                                <div class="mt-6">
                                    <a
                                        :href="route('viewer.files')"
                                        class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50"
                                    >
                                        Buka My Documents
                                        <span>→</span>
                                    </a>
                                </div>

                            </div>

                            <div
                                class="hidden shrink-0 md:flex"
                            >
                                <div
                                    class="flex h-32 w-32 items-center justify-center rounded-3xl bg-white/10 text-7xl ring-1 ring-white/20"
                                >
                                    📂
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Quick Access -->
                <div class="mt-8">

                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Akses Cepat
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Gunakan fitur berikut untuk menemukan dokumen.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">

                        <!-- My Documents -->
                        <a
                            :href="route('viewer.files')"
                            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-2xl transition group-hover:bg-blue-100"
                            >
                                📄
                            </div>

                            <h3
                                class="mt-5 text-base font-semibold text-gray-900"
                            >
                                My Documents
                            </h3>

                            <p
                                class="mt-2 text-sm leading-6 text-gray-500"
                            >
                                Lihat folder dan dokumen yang tersedia
                                untuk Anda.
                            </p>

                            <div
                                class="mt-5 text-sm font-semibold text-blue-600"
                            >
                                Lihat Dokumen →
                            </div>
                        </a>


                        <!-- Search -->
                        <a
                            :href="route('viewer.files')"
                            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-purple-200 hover:shadow-md"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-2xl transition group-hover:bg-purple-100"
                            >
                                🔎
                            </div>

                            <h3
                                class="mt-5 text-base font-semibold text-gray-900"
                            >
                                Cari Dokumen
                            </h3>

                            <p
                                class="mt-2 text-sm leading-6 text-gray-500"
                            >
                                Cari dokumen berdasarkan judul atau
                                nama file.
                            </p>

                            <div
                                class="mt-5 text-sm font-semibold text-purple-600"
                            >
                                Mulai Pencarian →
                            </div>
                        </a>


                        <!-- Department -->
                        <a
                            :href="route('viewer.files')"
                            class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-2xl transition group-hover:bg-green-100"
                            >
                                🏢
                            </div>

                            <h3
                                class="mt-5 text-base font-semibold text-gray-900"
                            >
                                Filter Department
                            </h3>

                            <p
                                class="mt-2 text-sm leading-6 text-gray-500"
                            >
                                Gunakan filter department untuk menemukan
                                dokumen dengan lebih cepat.
                            </p>

                            <div
                                class="mt-5 text-sm font-semibold text-green-600"
                            >
                                Filter Dokumen →
                            </div>
                        </a>

                    </div>
                </div>


                <!-- Viewer Information -->
                <div
                    class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >

                    <div class="border-b border-gray-100 px-6 py-5">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Yang dapat Anda lakukan
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Fitur yang tersedia untuk akun Viewer.
                        </p>
                    </div>

                    <div class="grid gap-0 md:grid-cols-3">

                        <!-- View Folder -->
                        <div
                            class="border-b border-gray-100 p-6 md:border-b-0 md:border-r"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50"
                                >
                                    📁
                                </div>

                                <div>
                                    <h3
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Melihat Folder
                                    </h3>

                                    <p
                                        class="mt-1 text-xs text-gray-500"
                                    >
                                        Jelajahi struktur folder dokumen.
                                    </p>
                                </div>
                            </div>
                        </div>


                        <!-- View Detail -->
                        <div
                            class="border-b border-gray-100 p-6 md:border-b-0 md:border-r"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50"
                                >
                                    📄
                                </div>

                                <div>
                                    <h3
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Detail File
                                    </h3>

                                    <p
                                        class="mt-1 text-xs text-gray-500"
                                    >
                                        Lihat informasi lengkap dokumen.
                                    </p>
                                </div>
                            </div>
                        </div>


                        <!-- Download -->
                        <div class="p-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50"
                                >
                                    ↓
                                </div>

                                <div>
                                    <h3
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Download File
                                    </h3>

                                    <p
                                        class="mt-1 text-xs text-gray-500"
                                    >
                                        Unduh dokumen yang tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- Bottom CTA -->
                <div
                    class="mt-8 flex flex-col items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50 px-6 py-6 sm:flex-row"
                >

                    <div>
                        <h3 class="text-base font-semibold text-gray-900">
                            Siap mencari dokumen?
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Buka My Documents untuk mulai menjelajahi dokumen.
                        </p>
                    </div>

                    <a
                        :href="route('viewer.files')"
                        class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Buka My Documents
                        →
                    </a>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>