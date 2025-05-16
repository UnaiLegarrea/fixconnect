<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    solicitud: Object,
    cliente: Object,
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
    <Head title="Detalle de Servicio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Detalle de Servicio
            </h2>
        </template>

        <div class="py-6 bg-neutral dark:bg-dark-primary">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('dashboard')" class="text-primary dark:text-blue-400 hover:underline">
                        &larr; Volver al Dashboard
                    </Link>
                </div>
                
                <div class="bg-white dark:bg-dark-card shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ solicitud.titulo }}</h1>
                            <div class="mt-2 flex items-center">
                                <span :class="['px-2 py-1 text-xs rounded-full mr-2', estadoClases(solicitud.estado)]">
                                    {{ solicitud.estado }}
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Aceptado el {{ solicitud.fecha }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-sm text-gray-800 dark:text-gray-200">
                                {{ solicitud.categoria }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">Descripción</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ solicitud.descripcion }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">Cliente</h3>
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ cliente.nombre }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ cliente.email }}</p>
                                <p v-if="cliente.telefono" class="text-sm text-gray-500 dark:text-gray-400">Tel: {{ cliente.telefono }}</p>
                            </div>
                        </div>
                    </div>
                      <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-end">                        <Link 
                            v-if="solicitud.estado === 'aceptada'" 
                            :href="route('chat.show', solicitud.id)" 
                            class="px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-white hover:bg-primary-dark transition"
                        >
                            Contactar Cliente
                        </Link>
                        <Link 
                            v-if="solicitud.estado === 'aceptada'" 
                            :href="route('servicios.completar', solicitud.id)" 
                            method="post"
                            as="button"
                            class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition ml-2"
                        >
                            Marcar como Completado
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
