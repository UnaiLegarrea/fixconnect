<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-neutral dark:bg-dark-primary pt-6 sm:justify-center sm:pt-0"
    >
        <div class="mb-6">
            <Link href="/" class="flex flex-col items-center">
                <div class="w-24 h-24 flex items-center justify-center">
                    <ApplicationLogo class="max-w-full max-h-full" />
                </div>
                <span class="mt-3 text-2xl font-bold text-primary dark:text-white">FixConnect</span>
            </Link>
        </div>

        <div
            class="w-full max-w-md overflow-hidden bg-white dark:bg-dark-surface px-8 py-6 shadow-lg sm:rounded-lg border-t-4 border-primary"
        >
            <slot />
        </div>
        
        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ new Date().getFullYear() }} FixConnect. Todos los derechos reservados.
        </div>
    </div>
</template>
