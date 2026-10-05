<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login - File Management System" />

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-800">
                Welcome Back
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Sign in to access your documents and files.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-5 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <!-- Email -->
            <div>
                <InputLabel
                    for="email"
                    value="Email Address"
                    class="font-medium text-slate-700"
                />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-lg border-slate-300 px-4 py-3 shadow-sm transition focus:border-slate-500 focus:ring-slate-500"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your email"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.email"
                />
            </div>

            <!-- Password -->
            <div class="mt-5">
                <InputLabel
                    for="password"
                    value="Password"
                    class="font-medium text-slate-700"
                />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full rounded-lg border-slate-300 px-4 py-3 shadow-sm transition focus:border-slate-500 focus:ring-slate-500"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password"
                />
            </div>

            <!-- Remember & Forgot -->
            <div class="mt-5">
                <label class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                        class="rounded border-slate-300 text-slate-800 focus:ring-slate-500"
                    />

                    <span class="ms-2 text-sm text-slate-600">
                        Remember me
                    </span>
                </label>

                
            </div>

            <!-- Login Button -->
            <div class="mt-6">
                <PrimaryButton
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-md transition hover:bg-slate-800 hover:shadow-lg focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 active:bg-slate-950"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </PrimaryButton>
            </div>
        </form>

        <!-- Login Information -->
        <div
            class="mt-6 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3"
        >
            <p class="text-center text-xs leading-relaxed text-slate-500">
                Authorized users only. Please use your registered account
                credentials to access the system.
            </p>
        </div>
    </GuestLayout>
</template>