<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div v-if="page.props.flash && page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 dark:bg-green-900 dark:text-green-100">
        <p>{{ page.props.flash.success }}</p>
    </div>
    <div v-if="page.props.flash && page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 dark:bg-red-900 dark:text-red-100">
        <p>{{ page.props.flash.error }}</p>
    </div>
</template>
