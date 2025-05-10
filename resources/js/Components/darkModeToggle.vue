<script setup>
import { ref, onMounted, watch } from 'vue';

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    
    // Guardar preferencia en localStorage
    localStorage.setItem('darkMode', isDarkMode.value);
    
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

onMounted(() => {
    const savedDarkMode = localStorage.getItem('darkMode');
    
    // Verificar preferencia del sistema si no hay guardada
    if (savedDarkMode === null) {
        const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        isDarkMode.value = prefersDarkMode;
    } else {
        isDarkMode.value = savedDarkMode === 'true';
    }
    
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    }
    
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (localStorage.getItem('darkMode') === null) {
            isDarkMode.value = e.matches;
            if (isDarkMode.value) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
});
</script>

<template>
    <button 
        @click="toggleDarkMode" 
        class="flex items-center justify-center w-10 h-10 rounded-full focus:outline-none transition-colors"
        :class="[
            isDarkMode 
                ? 'bg-gray-800 text-yellow-300 hover:bg-gray-700' 
                : 'bg-blue-100 text-blue-900 hover:bg-blue-200'
        ]"
        aria-label="Alternar modo oscuro"
    >
        <!-- Icono del sol (modo claro) -->
        <svg 
            v-if="isDarkMode" 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" 
            />
        </svg>
        
        <!-- Icono de la luna (modo oscuro) -->
        <svg 
            v-else 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" 
            />
        </svg>
    </button>
</template>