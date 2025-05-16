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
                <FlashMessage />
                
                <div class="overflow-hidden bg-white dark:bg-dark-surface shadow-md sm:rounded-lg">
                    <!-- Información de solicitud y participantes -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-dark-secondary">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h3 class="text-lg font-medium text-primary dark:text-white break-words">
                                    {{ solicitud.titulo }}
                                </h3>
                                <div class="mt-1 flex flex-wrap gap-2 items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-white dark:bg-primary-dark">
                                        {{ solicitud.categoria }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': solicitud.estado === 'cerrada',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': solicitud.estado === 'aceptada',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100': solicitud.estado === 'abierta'
                                          }">
                                        {{ estadoFormateado }}
                                    </span>
                                    
                                    <!-- Indicador de estado cerrada o reabierta -->
                                    <div v-if="solicitud.estado === 'cerrada'" class="mt-1 text-sm text-green-600 dark:text-green-400 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Solicitud marcada como resuelta
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2 md:mt-0">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <Link :href="route('solicitud.busqueda.show', solicitud.id)" class="text-sm text-primary dark:text-primary-light hover:text-primary-dark">
                                        Ver detalles de la solicitud
                                    </Link>
                                    
                                    <Link 
                                        v-if="$page.props.auth.user.id === cliente.id && solicitud.estado === 'aceptada'"
                                        :href="route('solicitudes.show', solicitud.id)" 
                                        class="text-sm text-primary dark:text-primary-light hover:text-primary-dark flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Cambiar estado
                                    </Link>
                                </div>
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
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-0">
                                <textarea
                                    v-model="nuevoMensaje"
                                    placeholder="Escribe un mensaje..."
                                    class="w-full sm:flex-grow rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-input dark:text-white dark:border-gray-700 resize-none"
                                    rows="2"
                                    @keydown.enter.prevent="enviarMensaje"
                                    :disabled="solicitud.estado === 'cerrada'"
                                ></textarea>
                                <button
                                    type="submit"
                                    class="sm:ml-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg self-end disabled:opacity-50 w-full sm:w-auto"
                                    :disabled="!nuevoMensaje.trim() || enviando || solicitud.estado === 'cerrada'"
                                >
                                    <template v-if="solicitud.estado === 'cerrada'">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                    <template v-else>
                                        <svg v-if="!enviando" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                        </svg>
                                        <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            <div v-if="solicitud.estado === 'cerrada'" class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">
                                La solicitud ha sido marcada como resuelta. No se pueden enviar más mensajes.
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
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

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
    if (!nuevoMensaje.value.trim() || enviando.value || props.solicitud.estado === 'cerrada') return;
    
    enviando.value = true;
    
    // Crear un mensaje temporal optimista para la UI
    const mensajeTemporal = {
        id: 'temp-' + Date.now(),
        contenido: nuevoMensaje.value,
        fecha: 'Enviando...',
        remitente: {
            id: props.usuarioActual.id,
            nombre: 'Tú',
            esUsuarioActual: true
        },
        leido: false
    };
    
    // Agregar el mensaje temporal a la lista de mensajes
    if (props.chat.mensajes) {
        props.chat.mensajes.push(mensajeTemporal);
    }
    
    // Limpiar el campo de texto y hacer scroll
    nuevoMensaje.value = '';
    scrollToBottom();
    
    // Enviar el mensaje al servidor
    router.post(route('chat.enviar-mensaje', props.solicitud.id), {
        contenido: mensajeTemporal.contenido
    }, {
        preserveScroll: true,
        onSuccess: () => {
            enviando.value = false;
        },
        onError: () => {
            enviando.value = false;
            // Eliminar el mensaje temporal en caso de error
            if (props.chat.mensajes) {
                const index = props.chat.mensajes.findIndex(m => m.id === mensajeTemporal.id);
                if (index !== -1) {
                    props.chat.mensajes.splice(index, 1);
                }
            }
        }
    });
};

// Scroll al fondo cuando se montan los mensajes iniciales
onMounted(() => {
    scrollToBottom();
});

// Utilizar Pusher para recibir mensajes en tiempo real
const refrescarChat = () => {
    router.reload({ only: ['chat'], preserveScroll: true });
};

let channelChat = null;

onMounted(() => {
    scrollToBottom();
    
    // Suscribirse al canal privado del chat
    if (window.Echo) {
        console.log('Suscribiéndose al canal: chat.' + props.chat.id);
        channelChat = window.Echo.private(`chat.${props.chat.id}`)
            .listen('.nuevo.mensaje', (e) => {
                console.log('Nuevo mensaje recibido:', e);
                
                // Solo recargar si el mensaje no es del usuario actual
                if (e.remitente_id !== props.usuarioActual.id) {
                    refrescarChat();
                }
            });
    } else {
        console.error('Echo no está disponible');
    }
});

// Limpiar la suscripción cuando el componente se desmonta
onUnmounted(() => {
    if (channelChat) {
        channelChat.unsubscribe();
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
