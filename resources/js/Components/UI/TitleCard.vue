<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div class="bg-[#1A2B4B] text-white text-center py-5 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold">{{ title }}</h2>
        <h3 v-if="subtitle" class="text-xl">{{ subtitle }}</h3>
    </div>
</template>