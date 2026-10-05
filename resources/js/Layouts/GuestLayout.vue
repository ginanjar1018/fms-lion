<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-100 px-4 py-10"
    >
        <!-- Decorative background -->
        <div
            class="absolute -left-32 -top-32 h-80 w-80 rounded-full bg-blue-200/40 blur-3xl"
        ></div>

        <div
            class="absolute -bottom-32 -right-32 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"
        ></div>

        <div class="relative z-10 w-full max-w-md">
            <!-- FMS Branding -->
            <div class="mb-7 text-center">
                <Link href="/" class="inline-flex flex-col items-center">
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-900 shadow-lg shadow-slate-900/20"
                    >
                        <span
                            class="text-2xl font-extrabold tracking-wider text-white"
                        >
                            FMS
                        </span>
                    </div>

                    <h1
                        class="mt-4 text-2xl font-bold tracking-tight text-slate-800"
                    >
                        File Management System
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Secure Document Management Platform
                    </p>
                </Link>
            </div>

            <!-- Login Card -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-8"
            >
                <slot />
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-slate-400">
                FMS &bull; Document Management System
            </p>
        </div>
    </div>
</template>