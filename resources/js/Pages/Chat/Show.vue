<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-primary dark:text-neutral">
                Chat de Soporte: {{ solicitud.titulo }}
            </h2>
        </template>

        <div class="py-12 bg-neutral dark:bg-dark-secondary">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md sm:rounded-lg">
                    <!-- Información de solicitud y participantes -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-secondary">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h3 class="text-lg font-medium text-primary dark:text-white">
                                    {{ solicitud.titulo }}
                                </h3>
                                <div class="mt-1 flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-white dark:bg-primary-dark">
                                        {{ solicitud.categoria }}
                                    </span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': solicitud.estado === 'cerrada',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': solicitud.estado === 'aceptada',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100': solicitud.estado === 'abierta'
                                          }">
                                        {{ estadoFormateado }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-2 md:mt-0">
                                <Link :href="route('solicitud.busqueda.show', solicitud.id)" class="text-sm text-primary dark:text-primary-light hover:text-primary-dark">
                                    Ver detalles de la solicitud
                                </Link>
                            </div>
                        </div>
                        
                        <div class="mt-3 flex flex-wrap gap-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Cliente:</span>
                                <span class="ml-1">{{ cliente.nombre }}</span>
                            </div>
                            
                            <div v-if="empresa" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Empresa:</span>
                                <span class="ml-1">{{ empresa.nombre }}</span>
                                <div v-if="empresa.verificada" class="ml-1 text-primary" title="Empresa verificada">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mensajes del chat -->
                    <div class="p-4 h-[500px] flex flex-col">
                        <div class="flex-1 overflow-y-auto mb-4 p-2" ref="mensajesContainer">
                            <div v-if="!chat.mensajes.length" class="flex items-center justify-center h-full">
                                <p class="text-gray-500 dark:text-gray-400 text-center">
                                    No hay mensajes aún. ¡Sé el primero en escribir!
                                </p>
                            </div>
                            
                            <template v-else>
                                <div v-for="mensaje in chat.mensajes" :key="mensaje.id" class="mb-4">
                                    <div
                                        class="flex"
                                        :class="mensaje.remitente.esUsuarioActual ? 'justify-end' : 'justify-start'"
                                    >
                                        <div
                                            class="px-4 py-2 rounded-lg max-w-[70%] sm:max-w-md break-words"
                                            :class="mensaje.remitente.esUsuarioActual ? 
                                                'bg-primary text-white' : 
                                                'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'"
                                        >
                                            <div class="text-sm">{{ mensaje.contenido }}</div>
                                            <div class="mt-1 text-xs flex justify-between items-center" 
                                                :class="mensaje.remitente.esUsuarioActual ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'">
                                                <span>{{ mensaje.fecha }}</span>
                                                <div v-if="mensaje.remitente.esUsuarioActual" class="ml-2">
                                                    <svg v-if="mensaje.leido" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Formulario para enviar mensajes -->
                        <form @submit.prevent="enviarMensaje" class="mt-auto border-t pt-3 dark:border-gray-700">
                            <div class="flex">
                                <textarea
                                    v-model="nuevoMensaje"
                                    placeholder="Escribe un mensaje..."
                                    class="flex-grow rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-input dark:text-white dark:border-gray-700 resize-none"
                                    rows="2"
                                    @keydown.enter.prevent="enviarMensaje"
                                ></textarea>
                                <button
                                    type="submit"
                                    class="ml-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg self-end disabled:opacity-50"
                                    :disabled="!nuevoMensaje.trim() || enviando"
                                >
                                    <svg v-if="!enviando" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                    </svg>
                                    <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    chat: Object,
    solicitud: Object,
    empresa: Object,
    cliente: Object,
    usuarioActual: Object
});

const nuevoMensaje = ref('');
const mensajesContainer = ref(null);
const enviando = ref(false);

const estadoFormateado = computed(() => {
    const estados = {
        'abierta': 'Abierta',
        'aceptada': 'En Proceso',
        'cerrada': 'Resuelta'
    };
    return estados[props.solicitud.estado] || props.solicitud.estado;
});

const scrollToBottom = () => {
    nextTick(() => {
        if (mensajesContainer.value) {
            mensajesContainer.value.scrollTop = mensajesContainer.value.scrollHeight;
        }
    });
};

const enviarMensaje = () => {
    if (!nuevoMensaje.value.trim() || enviando.value) return;
    
    enviando.value = true;
    
    router.post(route('chat.enviar-mensaje', props.solicitud.id), {
        contenido: nuevoMensaje.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            nuevoMensaje.value = '';
            scrollToBottom();
            enviando.value = false;
        },
        onError: () => {
            enviando.value = false;
        }
    });
};

// Scroll al fondo cuando se montan los mensajes iniciales
onMounted(() => {
    scrollToBottom();
});

// Recargar el chat cada 10 segundos para recibir nuevos mensajes
// Esto es un enfoque temporal antes de implementar WebSockets
const refrescarChat = () => {
    router.reload({ only: ['chat'], preserveScroll: true });
};

let intervalId = null;

onMounted(() => {
    scrollToBottom();
    // Refrescar cada 10 segundos
    intervalId = setInterval(refrescarChat, 10000);
});

// Limpiar el intervalo cuando el componente se desmonta
onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});

// Observar cambios en los mensajes para hacer scroll al fondo
watch(() => props.chat.mensajes, () => {
    scrollToBottom();
}, { deep: true });

</script>

<style scoped>
/* Estilo personalizado para el scrollbar de los mensajes */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Modo oscuro */
.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #333;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #555;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #777;
}
</style>
