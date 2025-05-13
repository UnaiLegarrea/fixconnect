<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Estado para almacenar los datos del dashboard
const stats = ref({
    solicitudesPendientes: 0,
    solicitudesCompletadas: 0,
    solicitudesRecientes: []
});

// Cargar datos del dashboard
onMounted(async () => {
    try {
        // En una implementación real, estos datos vendrían de una API
        // Por ahora usamos datos de ejemplo
        stats.value = {
            solicitudesPendientes: 5,
            solicitudesCompletadas: 12,
            solicitudesRecientes: [
                { id: 1, titulo: 'Reparación de ordenador', categoria: 'Informática', estado: 'abierta', fecha: '2025-05-12' },
                { id: 2, titulo: 'Problema con la red wifi', categoria: 'Redes', estado: 'aceptada', fecha: '2025-05-11' },
                { id: 3, titulo: 'Instalación de software', categoria: 'Software', estado: 'cerrada', fecha: '2025-05-10' }
            ]
        };
    } catch (error) {
        console.error('Error al cargar datos del dashboard:', error);
    }
});

// Función para obtener clase de color según el estado
const getStatusColor = (estado) => {
    switch (estado) {
        case 'abierta': return 'text-warning bg-warning/10 dark:text-warning dark:bg-warning/20';
        case 'aceptada': return 'text-secondary bg-secondary/10 dark:text-secondary-light dark:bg-secondary/20';
        case 'cerrada': return 'text-success bg-success/10 dark:text-success dark:bg-success/20';
        default: return 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-700';
    }
};
</script>

<template>
    <Head title="Panel de Control" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-primary dark:text-neutral">
                Panel de Control FixConnect
            </h2>
        </template>

        <div class="py-12 bg-neutral dark:bg-dark-secondary">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Tarjetas de estadísticas -->
                <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
                    <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg border-l-4 border-warning dark:border-warning">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-primary dark:text-neutral">Solicitudes Pendientes</h3>
                            <p class="mt-2 text-3xl font-bold text-warning">{{ stats.solicitudesPendientes }}</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Solicitudes por atender</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg border-l-4 border-success dark:border-success">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-primary dark:text-neutral">Solicitudes Completadas</h3>
                            <p class="mt-2 text-3xl font-bold text-success">{{ stats.solicitudesCompletadas }}</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Solicitudes resueltas</p>
                        </div>
                    </div>
                </div>
                
                <!-- Solicitudes recientes -->
                <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-primary dark:text-neutral">Solicitudes Recientes</h3>
                        
                        <div v-if="stats.solicitudesRecientes.length === 0" class="p-4 text-gray-500 dark:text-gray-400 bg-neutral-dark dark:bg-dark-primary rounded-lg">
                            No hay solicitudes recientes para mostrar
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-dark dark:divide-dark-primary">
                                <thead class="bg-primary text-white dark:bg-dark-primary dark:text-neutral">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Título
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-dark-surface divide-y divide-neutral-dark dark:divide-dark-primary">
                                    <tr v-for="solicitud in stats.solicitudesRecientes" :key="solicitud.id" 
                                        class="hover:bg-neutral dark:hover:bg-dark-primary transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-primary-dark dark:text-neutral">{{ solicitud.titulo }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-primary-light dark:text-gray-400">{{ solicitud.categoria }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                :class="getStatusColor(solicitud.estado)">
                                                {{ solicitud.estado.charAt(0).toUpperCase() + solicitud.estado.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-light dark:text-gray-400">
                                            {{ solicitud.fecha }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Botón para crear nueva solicitud -->
                <div class="mt-6 flex justify-end">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-primary dark:bg-secondary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-dark dark:hover:bg-secondary-light focus:bg-secondary-dark dark:focus:bg-secondary-light active:bg-secondary-dark dark:active:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-dark-primary transition ease-in-out duration-150">
                        Nueva Solicitud
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
