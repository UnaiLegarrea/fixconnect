<template>
    <Head title="Búsqueda de Solicitudes" />
    
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Búsqueda de Solicitudes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="bg-white dark:bg-dark-surface shadow-sm rounded-lg p-6 mb-6">
                    <form @submit.prevent="buscar">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Filtro por categoría -->
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                                <select
                                    id="categoria"
                                    v-model="filtros.categoria"
                                    @change="handleCategoriaChange"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark-input rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white dark:bg-dark-secondary"
                                >                                    <option v-for="categoria in categorias" :key="categoria">{{ categoria }}</option>
                                </select>
                            </div>
                            
                            <!-- Búsqueda por texto -->
                            <div>
                                <label for="busqueda" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Búsqueda</label>
                                <input
                                    id="busqueda"
                                    type="text"
                                    v-model="filtros.busqueda"
                                    placeholder="Buscar por título o descripción"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark-input rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm dark:text-white dark:bg-dark-secondary"
                                />
                            </div>
                            
                            <!-- Botón de búsqueda -->
                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Resultados de la búsqueda -->
                <div class="bg-white dark:bg-dark-surface shadow-sm rounded-lg overflow-hidden">
                    <!-- Mensaje si no hay resultados -->
                    <div v-if="solicitudes.data.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No se encontraron solicitudes con los criterios de búsqueda actuales.
                    </div>
                    
                    <!-- Lista de solicitudes -->
                    <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="solicitud in solicitudes.data" :key="solicitud.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <div class="flex justify-between items-start">
                                <div class="flex">
                                    <!-- Miniatura de la imagen si existe -->
                                    <div v-if="solicitud.imagen_url" class="mr-4 flex-shrink-0">
                                        <img 
                                            :src="solicitud.imagen_url" 
                                            alt="Miniatura" 
                                            class="h-24 w-24 object-cover rounded-md shadow-sm"
                                        >
                                    </div>
                                    
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ solicitud.titulo }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ solicitud.descripcion }}
                                        </p>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-white dark:bg-primary-dark ">
                                                {{ solicitud.categoria }}
                                            </span>
                                            <span class="ml-4">Cliente: {{ solicitud.cliente.nombre }}</span>
                                            <span class="ml-4">Fecha: {{ solicitud.fecha }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <Link
                                        :href="route('solicitud.busqueda.show', solicitud.id)"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md bg-primary-light hover:bg-primary-light/80 text-white dark:bg-primary-dark dark:hover:bg-primary-dark/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
                                    >
                                        Ver detalles
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Paginación -->
                    <div v-if="solicitudes.data.length > 0" class="bg-white dark:bg-dark-surface px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">                                <a
                                    v-if="solicitudes.prev_page_url"
                                    :href="solicitudes.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-surface hover:bg-neutral dark:hover:bg-dark-primary"
                                >
                                    Anterior
                                </a>
                                <a
                                    v-if="solicitudes.next_page_url"
                                    :href="solicitudes.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-surface hover:bg-neutral dark:hover:bg-dark-primary"
                                >
                                    Siguiente
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Mostrando
                                        <span class="font-medium">{{ solicitudes.from }}</span>
                                        a
                                        <span class="font-medium">{{ solicitudes.to }}</span>
                                        de
                                        <span class="font-medium">{{ solicitudes.total }}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">                                        <Link
                                            v-if="solicitudes.prev_page_url"
                                            :href="solicitudes.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark-surface text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-neutral dark:hover:bg-dark-primary"
                                        >
                                            <span class="sr-only">Anterior</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                        <Link
                                            v-if="solicitudes.next_page_url"
                                            :href="solicitudes.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-dark-surface text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-neutral dark:hover:bg-dark-primary"
                                        >
                                            <span class="sr-only">Siguiente</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </Link>
                                    </nav>
                                </div>
                            </div>
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
    solicitudes: Object,
    categorias: Array,
    filtros: Object
});

// Referencia reactiva para los filtros
const filtros = ref({
    categoria: props.filtros.categoria || 'Todas',
    busqueda: props.filtros.busqueda || ''
});

// Función para realizar la búsqueda
const buscar = () => {
    router.get(route('solicitud.busqueda'), {
        categoria: filtros.value.categoria,
        busqueda: filtros.value.busqueda
    }, {
        preserveState: true,
        replace: true
    });
};

// Escuchar cambios en el filtro de categoría y actualizar automáticamente
const handleCategoriaChange = () => {
    buscar();
};
</script>
