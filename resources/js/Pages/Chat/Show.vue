<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    solicitud: Object,
    otherUser: Object,
    mensajes: Array,
    chat: Object
});

const form = useForm({
    contenido: ''
});

const chatContainer = ref(null);

const scrollToBottom = () => {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const enviarMensaje = () => {
    if (!form.contenido.trim()) return;
    
    form.post(route('chats.mensajes.store', props.chat.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            nextTick(() => {
                scrollToBottom();
            });
        }
    });
};

onMounted(() => {
    scrollToBottom();
});
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Chat
            </h2>
        </template>

        <div class="py-6 bg-neutral dark:bg-dark-primary">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('dashboard')" class="text-primary dark:text-blue-400 hover:underline">
                        &larr; Volver al Dashboard
                    </Link>
                </div>
                
                <div class="bg-white dark:bg-dark-card shadow-sm rounded-lg overflow-hidden">
                    <!-- Cabecera del chat -->
                    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ otherUser.nombre }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Solicitud: {{ solicitud.titulo }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Mensajes -->
                    <div ref="chatContainer" class="p-4 h-96 overflow-y-auto">
                        <div v-if="mensajes.length === 0" class="flex justify-center items-center h-full">
                            <p class="text-gray-500 dark:text-gray-400">No hay mensajes. ¡Comienza la conversación!</p>
                        </div>
                        
                        <div v-for="mensaje in mensajes" :key="mensaje.id" class="mb-4">
                            <div :class="[
                                'max-w-3/4 rounded-lg p-3',
                                mensaje.remitente.soyYo ? 
                                'bg-primary text-white ml-auto' : 
                                'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'
                            ]" style="max-width: 75%">
                                <p>{{ mensaje.contenido }}</p>
                                <p class="text-xs opacity-75 mt-1 text-right">
                                    {{ mensaje.fecha }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulario para escribir mensajes -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <form @submit.prevent="enviarMensaje" class="flex items-center">
                            <input 
                                v-model="form.contenido" 
                                type="text" 
                                placeholder="Escribe un mensaje..." 
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                :disabled="form.processing"
                            >
                            <button 
                                type="submit" 
                                class="ml-2 px-4 py-2 bg-primary border border-transparent rounded-lg text-white hover:bg-primary-dark focus:outline-none focus:border-primary focus:ring"
                                :disabled="form.processing || !form.contenido.trim()"
                            >
                                Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
