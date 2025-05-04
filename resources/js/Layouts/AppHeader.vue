<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'FixConnect'
    },
    subtitle: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <header class="bg-[#1A2B4B] text-white p-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <img src="/img/logo.svg" alt="FixConnect" class="h-8 w-8 mr-2" />
                <span class="text-xl font-bold">{{ title }}</span>
            </div>
            <Link
                :href="route('login')"
                class="bg-transparent hover:bg-white/10 rounded-lg px-4 py-2 text-white transition"
            >
                Iniciar Sesion
            </Link>
        </div>
        <div v-if="subtitle" class="text-xs text-gray-300 mt-1">
            {{ subtitle }}
        </div>
    </header>
</template>