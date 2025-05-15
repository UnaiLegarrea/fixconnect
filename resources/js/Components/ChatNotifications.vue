<template>
    <div>
        <!-- Este componente no tiene interfaz visual, es para la escucha de eventos -->
    </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

let chatListeners = [];

onMounted(() => {
    // Escuchar el evento global de mensajes nuevos para actualizar el conteo de mensajes no leídos
    if (window.Echo) {
        // Escuchar canal de usuario privado para notificaciones
        const userChannel = window.Echo.private(`App.Models.User.${props.userId}`);
        
        // Evento de nuevo mensaje recibido
        chatListeners.push(userChannel.listen('.mensaje.recibido', (data) => {
            // Recargar solo los datos compartidos de Inertia para actualizar el conteo de mensajes
            router.reload({ only: ['mensajesNoLeidos'] });
        }));
    }
});

onUnmounted(() => {
    // Detener todos los listeners
    if (window.Echo) {
        chatListeners.forEach(listener => {
            if (listener && typeof listener.stopListening === 'function') {
                listener.stopListening('.mensaje.recibido');
            }
        });
    }
});

const props = defineProps({
    userId: {
        type: Number,
        required: true
    }
});
</script>
