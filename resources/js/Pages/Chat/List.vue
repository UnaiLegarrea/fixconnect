<template>
    <Head title="Mis Conversaciones" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-primary dark:text-neutral">
                Mis Conversaciones
            </h2>
        </template>

        <div class="py-12 bg-neutral dark:bg-dark-secondary">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filtros de búsqueda para chats -->
                        <div class="mb-6">
                            <div class="flex flex-wrap gap-4">
                                <div class="relative flex-grow">
                                    <input
                                        v-model="busqueda"
                                        type="text"
                                        placeholder="Buscar por título o participante..."
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300  dark:bg-dark-secondary focus:border-primary focus:ring-primary dark:bg-dark-input dark:text-white dark:border-gray-700 text-sm"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <select
                                    v-model="filtroEstado"
                                    class="px-4 py-2 rounded-lg border border-gray-300 focus:border-primary dark:bg-dark-secondary focus:ring-primary dark:bg-dark-input dark:text-white dark:border-gray-700 text-sm"
                                >
                                    <option value="todos">Todos los estados</option>
                                    <option value="abierta">Abiertos</option>
                                    <option value="aceptada">En proceso</option>
                                    <option value="cerrada">Resueltos</option>
                                </select>
                            </div>
                        </div>

                        <!-- Lista de chats -->
                        <div v-if="chatsFiltrados.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p>No se encontraron conversaciones</p>
                            <p class="mt-2 text-sm">
                                <template v-if="busqueda || filtroEstado !== 'todos'">
                                    Prueba a cambiar los filtros de búsqueda
                                </template>
                                <template v-else>
                                    Inicia una conversación desde una solicitud
                                </template>
                            </p>
                        </div>

                        <div v-else class="space-y-4">
                            <div 
                                v-for="chat in chatsFiltrados" 
                                :key="chat.id" 
                                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150"
                                :class="{'border-l-4 border-l-primary': chat.mensajesNoLeidos > 0}"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white flex items-center">
                                            <Link :href="route('chat.show', chat.id)" class="hover:text-primary dark:hover:text-primary-light">
                                                {{ chat.titulo }}
                                            </Link>
                                            <span v-if="chat.mensajesNoLeidos > 0" class="ml-2 px-2 py-0.5 bg-primary text-white text-xs font-bold rounded-full">
                                                {{ chat.mensajesNoLeidos }}
                                            </span>
                                        </h3>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            <span 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="{
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': chat.estado === 'abierta',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': chat.estado === 'aceptada',
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': chat.estado === 'cerrada'
                                                }"
                                            >
                                                {{ estadoFormateado(chat.estado) }}
                                            </span>
                                            <span class="mx-2">•</span>
                                            <span>{{ chat.categoria }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ chat.fecha }}</span>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ chat.ultimoMensaje ? chat.ultimoMensaje.fecha : 'Sin mensajes' }}
                                    </div>
                                </div>

                                <div class="flex justify-between items-end mt-3">
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        <div v-if="$page.props.auth.user.rol === 'cliente'">
                                            <span v-if="chat.empresa" class="font-medium">
                                                {{ chat.empresa.nombre }}
                                                <template v-if="chat.empresa.verificada">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </template>
                                            </span>
                                            <span v-else>Sin empresa asignada</span>
                                        </div>
                                        <div v-else-if="chat.otherUser">
                                            <span class="font-medium">{{ chat.otherUser.nombre }}</span>
                                        </div>
                                    </div>

                                    <div v-if="chat.ultimoMensaje" class="flex items-center">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 italic max-w-xs truncate">
                                            <span v-if="chat.ultimoMensaje.esPropio">Tú: </span>
                                            {{ chat.ultimoMensaje.contenido }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 flex justify-end">
                                    <Link 
                                        :href="route('chat.show', chat.id)" 
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md bg-primary-light hover:bg-primary-light/80 text-white dark:bg-primary-dark dark:hover:bg-primary-dark/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800"
                                    >
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                        </svg>
                                        Ver conversación
                                    </Link>
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
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    chats: Array,
});

// Filtros
const busqueda = ref('');
const filtroEstado = ref('todos');

// Conversiones de estado más amigables
const estadoFormateado = (estado) => {
    const estados = {
        'abierta': 'Abierta',
        'aceptada': 'En proceso',
        'cerrada': 'Resuelta'
    };
    return estados[estado] || estado;
};

// Filtrar chats en función de los criterios de búsqueda
const chatsFiltrados = computed(() => {
    return props.chats.filter(chat => {
        // Filtro por texto en título y participante
        const matchBusqueda = busqueda.value === '' || (
            (chat.titulo && chat.titulo.toLowerCase().includes(busqueda.value.toLowerCase())) ||
            (chat.empresa && chat.empresa.nombre && chat.empresa.nombre.toLowerCase().includes(busqueda.value.toLowerCase())) ||
            (chat.otherUser && chat.otherUser.nombre && chat.otherUser.nombre.toLowerCase().includes(busqueda.value.toLowerCase()))
        );
        
        // Filtro por estado
        const matchEstado = filtroEstado.value === 'todos' || chat.estado === filtroEstado.value;
        
        return matchBusqueda && matchEstado;
    });
});
</script>
