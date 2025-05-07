<script setup>
import { Head } from '@inertiajs/vue3';
import AppHeader from '@/Layouts/AppHeader.vue';
import AppFooter from '@/Layouts/AppFooter.vue';
import TitleCard from '@/Components/UI/TitleCard.vue';
import RequestCard from '@/Components/UI/RequestCard.vue';
import CompanyCard from '@/Components/UI/CompanyCard.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const companies = [
    { id: 1, name: 'Arreglos S.L', description: 'Descripción breve' },
    { id: 2, name: 'Nombre de Empresa', description: 'Descripción breve' },
    { id: 3, name: 'Nombre de Empresa', description: 'Descripción breve' },
    { id: 4, name: 'Nombre de Empresa', description: 'Descripción breve' }
];

const requests = [
    { 
        id: 1, 
        nombre: 'Nombre del problema/solicitud',
        descripcion: 'Descripción del problema/solicitud',
        direccion: 'Dirección:',
        imagen: '/storage/THE_SHUTDOWN_Camille_Unknown.png'
    },
    { 
        id: 2, 
        nombre: 'Reparación urgente',
        descripcion: 'Se necesita reparación de fontanería',
        direccion: 'Calle Principal 123',
        imagen: null
    },
    { 
        id: 3, 
        nombre: 'Instalación eléctrica',
        descripcion: 'Instalación de nuevas tomas',
        direccion: 'Avenida Central 45',
        imagen: null
    }
];
</script>

<template>
    <Head title="Welcome"/>
    <div class="flex flex-col min-h-screen bg-neutral">
        <AppHeader 
            title="FixConnect" 
        />

        <main class="flex-1 p-4">
            <!-- Sección de Empresas -->
            <section class="mb-8">
                <div class="mb-4">
                    <TitleCard 
                        title="Empresas" 
                        subtitle="Disponibles"
                    />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <CompanyCard 
                        v-for="company in companies"
                        :key="company.id"
                        :name="company.name"
                        :description="company.description"
                        class="h-full"
                    />
                </div>
            </section>
            
            <!-- Sección de Solicitudes/Problemas -->
            <section>
                <div class="mb-4">
                    <TitleCard 
                        title="Solicitudes" 
                        subtitle="Activas"
                    />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <RequestCard 
                        v-for="request in requests"
                        :key="request.id"
                        :nombre="request.nombre"
                        :descripcion="request.descripcion"
                        :direccion="request.direccion"
                        :imagen="request.imagen"
                        class="h-full"
                    />
                </div>
            </section>
        </main>

        <!-- Footer Component -->
        <AppFooter />
    </div>
</template>