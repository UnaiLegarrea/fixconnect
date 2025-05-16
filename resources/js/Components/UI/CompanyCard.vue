<script setup>
defineProps({
    name: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: 'Descripción breve'
    },
    categoria: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div class="bg-neutral-blue dark:bg-dark-card text-white rounded-lg shadow-md p-4">
        <div class="flex flex-wrap mb-3">
            <div class="w-12 h-12 bg-gray-700 dark:bg-gray-800 mr-4 flex-shrink-0 flex items-center justify-center rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-medium break-words">{{ name }}</h3>
                <p class="text-gray-400 text-sm break-words">{{ description }}</p>
            </div>
        </div>
        <div v-if="categoria" class="mt-2">
            <span class="inline-block bg-primary bg-opacity-20 text-primary dark:bg-primary-dark dark:text-white px-2 py-1 rounded-full text-xs">
                {{ categoria }}
            </span>
        </div>
        
        <!-- Botón Ver detalles -->
        <div class="mt-4 flex justify-center">
            <button class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                Ver detalles
            </button>
        </div>
    </div>
</template>