<template>
    <div
        v-if="contadorMensajes > 0"
        class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 flex items-center justify-center text-xs text-white font-bold"
    >
        {{ formatearContador(contadorMensajes) }}
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    contadorMensajes: {
        type: Number,
        default: 0
    }
});

// Formatea el contador para que muestre "9+" cuando hay más de 9 mensajes
const formatearContador = (contador) => {
    return contador > 9 ? '9+' : contador;
};
</script>
