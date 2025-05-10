<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Layouts/AppHeader.vue';
import AppFooter from '@/Layouts/AppFooter.vue';
import TitleCard from '@/Components/UI/TitleCard.vue';
import RequestCard from '@/Components/UI/RequestCard.vue';
import CompanyCard from '@/Components/UI/CompanyCard.vue';
import { ref } from 'vue';

const props = defineProps({
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
    empresas: {
        type: Array,
        required: true,
    },
    solicitudes: {
        type: Array,
        required: true,
    },
    categorias: {
        type: Array,
        required: true,
    },
});

const activeTab = ref('empresas');
const searchQuery = ref('');
const selectedCategoria = ref('Todas');

const filteredCompanies = () => {
    let results = props.empresas;
    
    // Filtrar por texto de búsqueda
    if (searchQuery.value) {
        results = results.filter(company => 
            company.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
            company.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            company.categoria.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    
    // Filtrar por categoría seleccionada
    if (selectedCategoria.value !== 'Todas') {
        results = results.filter(company => 
            company.categoria === selectedCategoria.value
        );
    }
    
    return results;
};

const filteredRequests = () => {
    let results = props.solicitudes;
    
    // Filtrar por texto de búsqueda
    if (searchQuery.value) {
        results = results.filter(request => 
            request.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
            request.descripcion.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            request.categoria.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    
    // Filtrar por categoría seleccionada
    if (selectedCategoria.value !== 'Todas') {
        results = results.filter(request => 
            request.categoria === selectedCategoria.value
        );
    }
    
    return results;
};

const selectCategoria = (categoria) => {
    selectedCategoria.value = categoria;
};
</script>

<template>
    <Head title="FixConnect - Conectamos tus problemas con soluciones profesionales"/>
    <div class="flex flex-col min-h-screen bg-neutral dark:bg-dark-primary">
        <AppHeader 
            title="FixConnect" 
        />

        <!-- Sección Hero -->
        <section class="bg-primary dark:bg-dark-secondary text-white px-4 py-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Conectamos tus problemas con soluciones profesionales</h1>
                <p class="text-lg mb-6">Encuentra rápidamente empresas especializadas para tus reparaciones o publica tu problema para recibir ayuda</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/register" class="bg-white text-primary dark:bg-dark-surface dark:text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-card transition duration-200">
                        Registrarme ahora
                    </Link>
                    <Link href="#buscar" class="bg-transparent border-2 border-white py-3 px-6 rounded-lg hover:bg-white/10 transition duration-200">
                        Buscar servicios
                    </Link>
                </div>
            </div>
        </section>

        <main class="flex-1 p-4 max-w-6xl mx-auto w-full dark:bg-dark-primary" id="buscar">
            <!-- Barra de búsqueda -->
            <div class="my-6">
                <div class="relative">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Buscar empresas o solicitudes..." 
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-primary focus:ring-primary dark:bg-dark-surface dark:text-white"
                    />
                    <div class="absolute right-3 top-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Tabs para alternar entre empresas y solicitudes -->
            <div class="mb-6">
                <div class="flex border-b border-gray-300 dark:border-gray-700">
                    <button 
                        @click="activeTab = 'empresas'" 
                        :class="[
                            'px-4 py-2 font-medium text-sm md:text-base',
                            activeTab === 'empresas' 
                                ? 'border-b-2 border-primary text-primary dark:text-white' 
                                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                        ]"
                    >
                        Empresas Disponibles
                    </button>
                    <button 
                        @click="activeTab = 'solicitudes'" 
                        :class="[
                            'px-4 py-2 font-medium text-sm md:text-base',
                            activeTab === 'solicitudes' 
                                ? 'border-b-2 border-primary text-primary dark:text-white' 
                                : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                        ]"
                    >
                        Solicitudes Activas
                    </button>
                </div>
            </div>

            <!-- Chips de categorías -->
            <div class="mb-6 flex flex-wrap gap-2">
                <button 
                    v-for="categoria in categorias" 
                    :key="categoria.id"
                    @click="selectCategoria(categoria.nombre)"
                    :class="[
                        'px-3 py-1 text-sm rounded-full border transition-colors',
                        selectedCategoria === categoria.nombre 
                            ? 'bg-primary text-white border-primary' 
                            : 'bg-white dark:bg-dark-surface dark:text-white border-gray-300 dark:border-gray-700 hover:bg-primary hover:text-white hover:border-primary dark:hover:bg-primary'
                    ]"
                >
                    {{ categoria.nombre }}
                </button>
            </div>

            <!-- Sección de Empresas -->
            <section v-if="activeTab === 'empresas'" class="mb-8">
                <div v-if="filteredCompanies().length === 0" class="text-center py-10">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron empresas que coincidan con tu búsqueda</p>
                </div>
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <CompanyCard 
                        v-for="company in filteredCompanies()"
                        :key="company.id"
                        :name="company.name"
                        :description="company.description"
                        class="h-full"
                    />
                </div>
            </section>
            
            <!-- Sección de Solicitudes/Problemas -->
            <section v-if="activeTab === 'solicitudes'">
                <div v-if="filteredRequests().length === 0" class="text-center py-10">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron solicitudes que coincidan con tu búsqueda</p>
                </div>
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <RequestCard 
                        v-for="request in filteredRequests()"
                        :key="request.id"
                        :nombre="request.nombre"
                        :descripcion="request.descripcion"
                        :direccion="request.direccion"
                        :imagen="request.imagen"
                        class="h-full"
                    />
                </div>
            </section>

            <!-- Sección CTA -->
            <section class="my-12 bg-neutral-blue dark:bg-dark-card rounded-lg shadow-lg p-6 text-white text-center">
                <h2 class="text-2xl font-bold mb-4">¿Tienes un problema que necesita solución?</h2>
                <p class="mb-6">Publica tu solicitud y conecta con profesionales cualificados en minutos</p>
                <Link href="/register" class="bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-primary-dark transition duration-200 inline-block">
                    Publicar un problema
                </Link>
            </section>
        </main>

        <!-- Footer Component -->
        <AppFooter />
    </div>
</template>

<style scoped>
/* Animaciones y transiciones para mejor experiencia de usuario */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>