<script setup>
import { computed } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'success',
    },
    title: {
        type: String,
        default: '',
    },
    message: {
        type: String,
        required: true,
    },
    confirm: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close', 'confirm'])

const config = computed(() => {
    const configs = {
        success: {
            icon: '✓',
            title: props.title || 'Berhasil',
            iconClass: 'bg-green-100 text-green-600',
            buttonClass: 'bg-green-600 hover:bg-green-700',
        },
        error: {
            icon: '!',
            title: props.title || 'Terjadi Kesalahan',
            iconClass: 'bg-red-100 text-red-600',
            buttonClass: 'bg-red-600 hover:bg-red-700',
        },
        warning: {
            icon: '!',
            title: props.title || 'Peringatan',
            iconClass: 'bg-yellow-100 text-yellow-600',
            buttonClass: 'bg-yellow-500 hover:bg-yellow-600',
        },
    }

    return configs[props.type] || configs.success
})
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
            @click.self="emit('close')"
        >
            <div
                class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg font-bold"
                        :class="config.iconClass"
                    >
                        {{ config.icon }}
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ config.title }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            {{ message }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
    <button
        v-if="confirm"
        type="button"
        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
        @click="emit('close')"
    >
        Batal
    </button>

    <button
        type="button"
        class="rounded-lg px-4 py-2 text-sm font-medium text-white transition"
        :class="config.buttonClass"
        @click="confirm ? emit('confirm') : emit('close')"
    >
        {{ confirm ? 'Hapus' : 'OK' }}
    </button>
</div>
            </div>
        </div>
    </Teleport>
</template>