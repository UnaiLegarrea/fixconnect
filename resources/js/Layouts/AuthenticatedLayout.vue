<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NotificacionBadge from '@/Components/NotificacionBadge.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;
const mensajesNoLeidos = ref(usePage().props.mensajesNoLeidos);

// Configurar Pusher para notificaciones de mensajes
onMounted(() => {
    // Suscribirse al canal privado del usuario
    window.Echo.private(`App.Models.User.${user.id}`)
        .listen('.mensaje.recibido', (e) => {
            // Incrementar el contador de mensajes no leídos
            mensajesNoLeidos.value += 1;
            
            // Opcionalmente mostrar una notificación
            if (Notification.permission === 'granted') {
                new Notification('Nuevo mensaje', {
                    body: 'Has recibido un nuevo mensaje'
                });
            }
        });
});

// Limpiar suscripción cuando se desmonta el componente
onUnmounted(() => {
    window.Echo.leave(`App.Models.User.${user.id}`);
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-neutral dark:bg-dark-secondary">
            <nav
                class="border-b border-gray-100 bg-white dark:bg-dark-surface dark:border-dark-primary"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-neutral"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Panel de Control
                                </NavLink>
                                <NavLink
                                    v-if="user.rol === 'empresa' || user.rol === 'admin'"
                                    :href="route('solicitud.busqueda')"
                                    :active="route().current('solicitud.busqueda') || route().current('solicitud.busqueda.show')"
                                >
                                    Buscar Solicitudes
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Indicador de mensajes no leídos -->
                            <div class="relative mr-3">
                                <Link :href="route('chat.list')" class="relative inline-flex items-center p-2 text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-primary-light transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                                    </svg>
                                    <NotificacionBadge :contador-mensajes="mensajesNoLeidos" />
                                </Link>
                            </div>
                            
                            <!-- Settings Dropdown -->
                            <DarkModeToggle class="mr-3" />
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white dark:bg-dark-surface px-3 py-2 text-sm font-medium leading-4 text-gray-500 dark:text-gray-300 transition duration-150 ease-in-out hover:text-gray-700 dark:hover:text-white focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.nombre }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Perfil
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Cerrar sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-primary dark:text-neutral transition duration-150 ease-in-out hover:bg-neutral dark:hover:bg-dark-primary hover:text-primary-dark dark:hover:text-white focus:bg-neutral dark:focus:bg-dark-primary focus:text-primary-dark dark:focus:text-white focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Panel de Control
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="user.rol === 'empresa' || user.rol === 'admin'"
                            :href="route('solicitud.busqueda')"
                            :active="route().current('solicitud.busqueda') || route().current('solicitud.busqueda.show')"
                        >
                            Buscar Solicitudes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('chat.list')"
                            :active="route().current('chat.list') || route().current('chat.show')"
                            class="flex items-center"
                        >
                            <span>Mensajes</span>
                            <div v-if="mensajesNoLeidos > 0" class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                                {{ mensajesNoLeidos }}
                            </div>
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 dark:border-dark-primary pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-neutral"
                            >
                                {{ $page.props.auth.user.nombre }}
                            </div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Cerrar sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white dark:bg-dark-surface shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
