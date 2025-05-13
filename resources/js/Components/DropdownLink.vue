<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-primary dark:text-neutral transition duration-150 ease-in-out hover:bg-neutral dark:hover:bg-dark-primary focus:bg-neutral dark:focus:bg-dark-primary focus:outline-none"
    >
        <slot />
    </Link>
</template>
