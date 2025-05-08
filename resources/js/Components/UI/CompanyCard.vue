<script setup>
defineProps({
    name: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: 'Descripción breve'
    }
});
</script>

<template>
    <div class="bg-neutral-blue dark:bg-dark-card text-white rounded-lg shadow-md p-4 flex">
        <div class="w-12 h-12 bg-gray-700 dark:bg-gray-800 mr-4 flex items-center justify-center rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h3 class="font-medium">{{ name }}</h3>
            <p class="text-gray-400 text-sm">{{ description }}</p>
        </div>
    </div>
</template>