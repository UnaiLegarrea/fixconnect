<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const tipoUsuario = ref('cliente');

const form = useForm({
    name: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
    es_empresa: false,
    ubicacion: '',
    categoria: '',
});

const toggleTipoUsuario = (tipo) => {
    tipoUsuario.value = tipo;
    form.es_empresa = tipo === 'empresa';
};

const categorias = [
    'Hogar',
    'Tecnología',
    'Fontanería',
    'Electricidad',
    'Automóvil',
    'General'
];

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registro - FixConnect" />
        
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-primary">Crear Cuenta</h2>
            <p class="text-gray-600 mt-2">Únete a FixConnect</p>
        </div>

        <!-- Selector de tipo de usuario -->
        <div class="mb-6">
            <div class="flex justify-center">
                <div class="flex rounded-md shadow-sm">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium border border-r-0 rounded-l-md focus:outline-none"
                        :class="tipoUsuario === 'cliente' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        @click="toggleTipoUsuario('cliente')"
                    >
                        Soy Cliente
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium border rounded-r-md focus:outline-none"
                        :class="tipoUsuario === 'empresa' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        @click="toggleTipoUsuario('empresa')"
                    >
                        Soy Empresa
                    </button>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre" class="text-gray-700" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo Electrónico" class="text-gray-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="telefono" value="Teléfono" class="text-gray-700" />

                <TextInput
                    id="telefono"
                    type="tel"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    v-model="form.telefono"
                    required
                />

                <InputError class="mt-2" :message="form.errors.telefono" />
            </div>

            <!-- Campos específicos para empresas -->
            <div v-if="tipoUsuario === 'empresa'" class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="text-lg font-medium text-primary mb-4">Información de la Empresa</h3>
                
                <div class="mt-4">
                    <InputLabel for="ubicacion" value="Ubicación" class="text-gray-700" />
                    
                    <TextInput
                        id="ubicacion"
                        type="text"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                        v-model="form.ubicacion"
                        required
                        placeholder="Ej: Madrid, Barcelona, etc."
                    />
                    
                    <InputError class="mt-2" :message="form.errors.ubicacion" />
                </div>
                
                <div class="mt-4">
                    <InputLabel for="categoria" value="Categoría Principal" class="text-gray-700" />
                    
                    <select
                        id="categoria"
                        v-model="form.categoria"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                        required
                    >
                        <option value="" disabled>Selecciona una categoría</option>
                        <option v-for="categoria in categorias" :key="categoria" :value="categoria">
                            {{ categoria }}
                        </option>
                    </select>
                    
                    <InputError class="mt-2" :message="form.errors.categoria" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" class="text-gray-700" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                    class="text-gray-700"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-primary hover:text-primary-dark focus:outline-none underline"
                >
                    ¿Ya tienes cuenta? Inicia sesión
                </Link>
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center py-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Crear Cuenta
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
