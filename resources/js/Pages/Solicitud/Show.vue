<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    solicitud: Object,
    empresa: Object,
    clienteId: Number,
});

const estadoClases = (estado) => {
    switch(estado) {
        case 'abierta': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'aceptada': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'cerrada': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }
};
</script>

<template>
    <Head title="Detalle de Solicitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Detalle de Solicitud
            </h2>
        </template>

        <div class="py-6 bg-neutral dark:bg-dark-primary">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">                <div class="mb-6">
                    <Link :href="route('dashboard')" class="text-primary dark:text-blue-400 hover:underline">
                        &larr; Volver al Dashboard
                    </Link>
                </div>
                
                <FlashMessage />
                
                <div class="bg-white dark:bg-dark-card shadow-sm rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white break-words">{{ solicitud.titulo }}</h1>                            <div class="mt-2 flex flex-wrap gap-2 items-center">
                                <span :class="['px-2 py-1 text-xs rounded-full', estadoClases(solicitud.estado)]">
                                    {{ solicitud.estado.charAt(0).toUpperCase() + solicitud.estado.slice(1) }}
                                </span>
                                <!-- Indicador de estado cerrada -->
                                <div v-if="solicitud.estado === 'cerrada'" class="text-sm text-green-600 dark:text-green-400 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Solicitud resuelta
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Creada el {{ solicitud.fecha }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-sm text-gray-800 dark:text-gray-200">
                                {{ solicitud.categoria }}
                            </span>
                        </div>
                    </div>
                      <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">Descripción</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ solicitud.descripcion }}</p>
                    </div>
                    
                    <!-- Mostrar imagen si existe -->
                    <div v-if="solicitud.imagen_url" class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">Imagen</h3>
                        <div class="mt-2">
                            <img :src="solicitud.imagen_url" alt="Imagen de la solicitud" class="max-h-96 rounded-lg">
                        </div>
                    </div>
                    
                    <div v-if="empresa" class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">Empresa Asignada</h3>
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ empresa.nombre }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ empresa.ubicacion }}</p>
                            </div>
                            <div v-if="empresa.verificada" class="flex items-center text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm">Verificada</span>
                            </div>                        </div>                    </div>                      <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex flex-wrap gap-2 justify-end">
                        <!-- Botón para cancelar solicitudes en estado 'abierta' -->
                        <Link 
                            v-if="solicitud.estado === 'abierta'" 
                            :href="route('solicitudes.cancelar', solicitud.id)" 
                            method="delete"
                            as="button"
                            class="w-full sm:w-auto px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition text-center"
                        >
                            Cancelar Solicitud
                        </Link>
                        
                        <!-- Botón para reabrir solicitudes en estado 'aceptada' -->
                        <Link 
                            v-if="solicitud.estado === 'aceptada' && $page.props.auth.user.id === clienteId" 
                            :href="route('solicitudes.cambiar-estado', solicitud.id)" 
                            method="patch"
                            :data="{ estado: 'abierta' }"
                            as="button"
                            class="w-full sm:w-auto px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-white hover:bg-amber-700 transition text-center"
                        >
                            Reabrir Solicitud
                        </Link>
                        
                        <!-- Botón para marcar como 'cerrada' (resuelta) -->
                        <Link 
                            v-if="solicitud.estado === 'aceptada' && $page.props.auth.user.id === clienteId" 
                            :href="route('solicitudes.cambiar-estado', solicitud.id)" 
                            method="patch"
                            :data="{ estado: 'cerrada' }"
                            as="button"
                            class="w-full sm:w-auto px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition text-center"
                        >
                            Marcar como Resuelta
                        </Link>
                        
                        <!-- Botón para chatear con la empresa asignada -->
                        <Link 
                            v-if="solicitud.estado === 'aceptada' && empresa" 
                            :href="route('chat.show', solicitud.id)" 
                            class="w-full sm:w-auto px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-white hover:bg-primary-dark transition text-center"
                        >
                            Contactar Empresa
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
