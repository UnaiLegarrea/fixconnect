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
                <div class="bg-white dark:bg-dark-surface shadow-sm rounded-lg overflow-hidden">
                    <!-- Datos de la solicitud -->
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ solicitud.titulo }}
                                </h3>                                <div class="mb-4 flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-white dark:bg-primary-dark  mr-2">
                                        {{ solicitud.categoria }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Publicada el {{ solicitud.fecha }}
                                    </span>
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
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 flex flex-wrap gap-2 justify-between border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="route('solicitud.busqueda')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-neutral dark:hover:bg-dark-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Volver a la búsqueda
                        </Link>

                        <div class="flex gap-2">
                            <!-- Botón de Chat -->
                            <Link
                                :href="route('chat.show', solicitud.id)"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-neutral dark:hover:bg-dark-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                </svg>
                                {{ solicitud.estado !== 'abierta' ? 'Continuar Chat' : 'Iniciar Chat' }}
                            </Link>

                            <!-- Botón de Aceptar Solicitud -->
                            <form v-if="$page.props.auth.user.rol === 'empresa' || $page.props.auth.user.rol === 'admin'" @submit.prevent="aceptarSolicitud">
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
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

const props = defineProps({
    solicitud: Object
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
