<template>
    <Head title="Detalle de Solicitud" />
    
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detalle de Solicitud
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                
                <div class="bg-white dark:bg-dark-surface shadow-sm rounded-lg overflow-hidden">
                    <!-- Datos de la solicitud -->
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 break-words">
                                    {{ solicitud.titulo }}
                                </h3>                                <div class="mb-4 flex flex-wrap gap-2 items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-white dark:bg-primary-dark">
                                        {{ solicitud.categoria }}
                                    </span>
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': solicitud.estado === 'aceptada',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': solicitud.estado === 'abierta',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': solicitud.estado === 'cerrada'
                                        }"
                                    >
                                        {{ solicitud.estado.charAt(0).toUpperCase() + solicitud.estado.slice(1) }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Publicada el {{ solicitud.fecha }}
                                    </span>
                                </div>

                                <!-- Indicador de solicitud cerrada/resuelta -->
                                <div v-if="solicitud.estado === 'cerrada'" class="mt-4 mb-6 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Solicitud resuelta</h3>
                                            <div class="mt-1 text-sm text-green-700 dark:text-green-400">
                                                <p>Esta solicitud ha sido marcada como resuelta por el cliente.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Descripción
                                    </h4>
                                    <div class="text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                                        <p class="whitespace-pre-wrap">{{ solicitud.descripcion }}</p>
                                    </div>
                                </div>
                                
                                <!-- Mostrar imagen si existe -->
                                <div v-if="solicitud.imagen_url" class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Imagen
                                    </h4>
                                    <div class="mt-2">
                                        <img 
                                            :src="solicitud.imagen_url" 
                                            alt="Imagen de la solicitud" 
                                            class="max-h-96 rounded-lg object-cover shadow-md"
                                        >
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Datos del cliente
                                    </h4>
                                    <div class="text-gray-700 dark:text-gray-300">
                                        <p><span class="font-medium">Nombre:</span> {{ solicitud.cliente.nombre }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Botones de acción -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex flex-col sm:flex-row gap-3 justify-between border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="solicitud.estado === 'aceptada' ? route('dashboard') : route('solicitud.busqueda')"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-neutral dark:hover:bg-dark-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800 w-full sm:w-auto"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            {{ solicitud.estado === 'aceptada' ? 'Volver al Dashboard' : 'Volver a la búsqueda' }}
                        </Link>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <!-- Botón de Chat -->
                            <Link
                                :href="route('chat.show', solicitud.id)"
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary w-full sm:w-auto"
                                v-if="solicitud.estado === 'aceptada'"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                </svg>
                                {{ 'Continuar Chat' }}
                            </Link>

                            <!-- Botón de Aceptar Solicitud -->
                            <form v-if="$page.props.auth.user.rol === 'empresa' || $page.props.auth.user.rol === 'admin'" @submit.prevent="aceptarSolicitud" class="w-full sm:w-auto">
                                <button
                                    type="submit"
                                    class="inline-flex w-full justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="processing || solicitud.estado !== 'abierta'"
                                >
                                    <svg v-if="!processing" class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Procesando...' : 'Aceptar Solicitud' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    solicitud: Object,
    empresa: Object
});

const processing = ref(false);

// Función para aceptar la solicitud
const aceptarSolicitud = () => {
    processing.value = true;
    
    router.post(route('solicitud.busqueda.aceptar', props.solicitud.id), {}, {
        onSuccess: () => {
            // La redirección se maneja en el controlador
        },
        onError: () => {
            processing.value = false;
        }
    });
};
</script>
