<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import DarkModeToggle from '@/Components/DarkModeToggle.vue';

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

const page = usePage();
const canLogin = page.props.canLogin || false;
const canRegister = page.props.canRegister || false;
</script>

<template>
    <header class="bg-primary dark:bg-dark-primary text-white p-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <img src="/storage/logoTFG2.png" alt="FixConnect" class="h-12 w-12 mr-2" />
                <span class="text-xl font-bold">{{ title }}</span>
            </div>
            <div class="flex items-center gap-4">
                <DarkModeToggle />
                <template v-if="$page.props.auth && $page.props.auth.user">
                    <Link :href="route('dashboard')" class="text-white hover:text-gray-200">
                        Panel de control
                    </Link>
                </template>
                <template v-else>
                    <Link v-if="canLogin" :href="route('login')"
                        class="bg-transparent hover:bg-white/10 rounded-lg px-4 py-2 text-white transition">
                        Iniciar Sesión
                    </Link>
                    <Link v-if="canRegister" :href="route('register')"
                        class="bg-white text-primary hover:bg-gray-100 rounded-lg px-4 py-2 transition">
                        Registrarse
                    </Link>
                </template>
            </div>
        </div>
        <div v-if="subtitle" class="text-xs text-neutral dark:text-gray-300 mt-1">
            {{ subtitle }}
        </div>
    </header>
</template>