<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categorias: Array,
});

const form = useForm({
    titulo: '',
    descripcion: '',
    categoria: '',
});

const submit = () => {
    form.post(route('solicitudes.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Nueva Solicitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Nueva Solicitud
            </h2>
        </template>

        <div class="py-6 bg-neutral dark:bg-dark-primary">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-dark-card shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Crea una nueva solicitud
                    </h3>
                    
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                            <input 
                                id="titulo" 
                                v-model="form.titulo" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required
                            >
                            <div v-if="form.errors.titulo" class="text-red-500 text-sm mt-1">{{ form.errors.titulo }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                            <select 
                                id="categoria" 
                                v-model="form.categoria" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required
                            >
                                <option value="" disabled>Selecciona una categoría</option>
                                <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.nombre">
                                    {{ categoria.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.categoria" class="text-red-500 text-sm mt-1">{{ form.errors.categoria }}</div>
                        </div>

                        <div class="mb-6">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea 
                                id="descripcion" 
                                v-model="form.descripcion" 
                                rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required
                            ></textarea>
                            <div v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</div>
                        </div>

                        <div class="flex items-center justify-end">
                            <Link 
                                :href="route('dashboard')" 
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 mr-4"
                            >
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-white hover:bg-primary-dark focus:outline-none focus:border-primary focus:ring ring-primary focus:ring-opacity-50 transition ease-in-out duration-150"
                                :disabled="form.processing"
                            >
                                Crear Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
