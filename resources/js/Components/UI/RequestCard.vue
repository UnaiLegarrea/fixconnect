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
    <div class="bg-[#1A2B4B] text-white rounded-lg shadow-md p-4">
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
        <div class="text-center mb-2">
            <h2 class="text-xl font-bold">{{ nombre }}</h2>
        </div>
        
        <!-- Imagen -->
        <div class="bg-white rounded-lg p-2 flex justify-center items-center mb-2">
            <img v-if="imagen" :src="imagen" alt="Imagen del problema" class="max-h-32 object-cover rounded" />
            <div v-else class="w-full h-32 border-2 border-gray-300 rounded-lg flex flex-col justify-center items-center">
                <div class="w-10 h-10 border-2 border-gray-500 rounded-full mb-2 flex justify-center items-center">
                    <div class="w-5 h-5 bg-gray-500 rounded-full"></div>
                </div>
                <div class="w-3/4 h-px bg-gray-500 mb-2"></div>
                <div class="w-1/2 h-px bg-gray-500 transform rotate-45"></div>
            </div>
        </div>
        
        <!-- Descripción -->
        <div class="text-sm text-center">
            <p>{{ descripcion }}</p>
        </div>
    </div>
</template>