<script setup>
defineProps({
    nombre: {
        type: String,
        required: true
    },
    descripcion: {
        type: String,
        default: 'Descripcion de el problema'
    },
    direccion: {
        type: String,
        default: 'Direccion:'
    },
    imagen: {
        type: String,
        default: null
    }
});
</script>

<template>
    <div class="bg-primary text-white rounded-lg shadow-md p-4">
        <!-- Encabezado con dirección y botón de edición -->
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm">{{ direccion }}</span>
            <button class="text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
        </div>
        
        <!-- Nombre del problema/solicitud -->
        <div class="text-center mb-3">
            <h2 class="text-xl font-bold">{{ nombre }}</h2>
        </div>
        
        <!-- Imagen - Adaptado al diseño de las capturas -->
        <div class="bg-white rounded-lg overflow-hidden mb-3">
            <img v-if="imagen" :src="imagen" alt="Imagen del problema" class="w-full h-[140px] object-cover" />
            <div v-else class="w-full h-[140px] flex flex-col justify-center items-center bg-neutral">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        
        <!-- Descripción -->
        <div class="text-sm text-center">
            <p>{{ descripcion }}</p>
        </div>
    </div>
</template>