<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';

// Acceder a las props recibidas desde el controlador
const props = defineProps({
    stats: Object
});

// Función para obtener clase de color según el estado
const getStatusColor = (estado) => {
    switch (estado) {
        case 'abierta': return 'text-blue-800 bg-blue-100 dark:text-blue-200 dark:bg-blue-900';
        case 'aceptada': return 'text-yellow-800 bg-yellow-100 dark:text-yellow-200 dark:bg-yellow-900';
        case 'cerrada': return 'text-green-800 bg-green-100 dark:text-green-200 dark:bg-green-900';
        default: return 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-700';
    }
};

// Función para traducir el estado a un texto más descriptivo
const getStatusText = (estado) => {
    switch (estado) {
        case 'abierta': return 'Abierta';
        case 'aceptada': return 'En Proceso';
        case 'cerrada': return 'Resuelta';
        default: return estado.charAt(0).toUpperCase() + estado.slice(1);
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
                <!-- Flash Messages -->
                <FlashMessage />
                
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
                                            <div class="text-sm font-medium text-primary-dark dark:text-neutral">
                                                <Link 
                                                    :href="
                                                        $page.props.auth.user.rol === 'empresa' && solicitud.estado === 'aceptada' ? 
                                                            route('solicitud.busqueda.show', solicitud.id) : 
                                                        $page.props.auth.user.rol === 'cliente' ? 
                                                            route('solicitudes.show', solicitud.id) : 
                                                        solicitud.estado === 'abierta' ?
                                                            route('solicitud.busqueda.show', solicitud.id) :
                                                            route('solicitudes.show', solicitud.id)"
                                                    class="hover:underline"
                                                >
                                                    {{ solicitud.titulo }}
                                                </Link>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-primary-light dark:text-gray-400">{{ solicitud.categoria }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                :class="getStatusColor(solicitud.estado)">
                                                {{ getStatusText(solicitud.estado) }}
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
                
                <!-- Chats activos -->
                <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg mt-8">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-primary dark:text-neutral">Chats Activos</h3>
                        
                        <div v-if="stats.chatsActivos.length === 0" class="p-4 text-gray-500 dark:text-gray-400 bg-neutral-dark dark:bg-dark-primary rounded-lg">
                            No hay chats activos para mostrar
                        </div>
                        
                        <div v-else>
                            <div class="space-y-4">
                                <div v-for="chat in stats.chatsActivos" :key="chat.id" 
                                     class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 flex flex-col gap-2">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                                        <div class="mb-2 sm:mb-0">
                                            <h4 class="font-medium text-primary-dark dark:text-white break-words">
                                                <Link :href="route('chat.show', chat.id)" class="hover:underline">
                                                    {{ chat.titulo }}
                                                </Link>
                                            </h4>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <span v-if="$page.props.auth.user.rol === 'cliente'">
                                                    Empresa: {{ chat.nombreEmpresa }}
                                                </span>
                                                <span v-else-if="$page.props.auth.user.rol === 'empresa'">
                                                    Cliente: {{ chat.nombreCliente }}
                                                </span>
                                                <span v-else>
                                                    Cliente: {{ chat.nombreCliente }} | Empresa: {{ chat.nombreEmpresa }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 sm:flex-shrink-0 mb-2 sm:mb-0 sm:ml-2">{{ chat.fecha }}</span>
                                    </div>
                                    
                                    <div v-if="chat.ultimoMensaje" class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ chat.ultimoMensaje.esPropio ? 'Tú' : 'Contacto' }}:
                                            </span>
                                            <span class="text-xs text-gray-500 mt-1 sm:mt-0">{{ chat.ultimoMensaje.fecha }}</span>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1 break-words">{{ chat.ultimoMensaje.contenido }}</p>
                                    </div>
                                    
                                    <div class="mt-2 flex justify-center sm:justify-end">
                                        <Link 
                                            :href="route('chat.show', chat.id)" 
                                            class="w-full sm:w-auto inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm leading-5 font-medium rounded-md bg-primary-light hover:bg-primary-light/80 text-white dark:bg-primary-dark dark:hover:bg-primary-dark/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
                                        >
                                            <svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                            </svg>
                                            Continuar chat
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón para crear nueva solicitud -->
                <div class="mt-6 flex justify-end">
                    <Link v-if="$page.props.auth.user.rol === 'cliente'" :href="route('solicitudes.create')" class="inline-flex items-center px-4 py-2 bg-primary dark:bg-secondary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-dark dark:hover:bg-secondary-light focus:bg-secondary-dark dark:focus:bg-secondary-light active:bg-secondary-dark dark:active:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-dark-primary transition ease-in-out duration-150">
                        Nueva Solicitud
                    </Link>
                    <Link v-else-if="$page.props.auth.user.rol === 'empresa' || $page.props.auth.user.rol === 'admin'" :href="route('solicitud.busqueda')" class="inline-flex items-center px-4 py-2 bg-primary dark:bg-secondary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-dark dark:hover:bg-secondary-light focus:bg-secondary-dark dark:focus:bg-secondary-light active:bg-secondary-dark dark:active:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-dark-primary transition ease-in-out duration-150">
                        Buscar Solicitudes
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
