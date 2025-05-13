<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    chats: Array
});
</script>

<template>
    <Head title="Mensajes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Mensajes
            </h2>
        </template>

        <div class="py-6 bg-neutral dark:bg-dark-primary">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('dashboard')" class="text-primary dark:text-blue-400 hover:underline">
                        &larr; Volver al Dashboard
                    </Link>
                </div>
                
                <div class="bg-white dark:bg-dark-card shadow-sm rounded-lg overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Conversaciones</h3>
                    </div>
                    
                    <div v-if="chats.length === 0" class="p-6 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No tienes conversaciones activas</p>
                    </div>
                    
                    <div v-else>
                        <div v-for="chat in chats" :key="chat.id" 
                            class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                            <Link 
                                :href="route('solicitudes.chat', chat.solicitud.id)" 
                                class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                                            {{ chat.otherUser.nombre }}
                                            <span v-if="chat.noLeidos > 0" 
                                                class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-500 text-white">
                                                {{ chat.noLeidos }}
                                            </span>
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Solicitud: {{ chat.solicitud.titulo }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ chat.ultimoMensaje.fecha }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 truncate">
                                    <span v-if="chat.ultimoMensaje.esPropio" class="text-gray-500 dark:text-gray-400">Tú: </span>
                                    {{ chat.ultimoMensaje.contenido }}
                                </p>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
