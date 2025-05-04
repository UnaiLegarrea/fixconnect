<script setup>
import { Head } from '@inertiajs/vue3';
import AppHeader from '@/Layouts/AppHeader.vue';
import AppFooter from '@/Layouts/AppFooter.vue';
import TitleCard from '@/Components/UI/TitleCard.vue';
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
</script>

<template>
    <Head title="Welcome"/>
    <div class="flex flex-col min-h-screen bg-gray-100">
        <AppHeader 
            title="FixConnect" 
        />

        <main class="flex-1 p-4">
            <div class="mb-4">
                <TitleCard 
                    title="Empresas" 
                    subtitle="Disponibles"
                />
            </div>
            <div class="space-y-4">
                <CompanyCard 
                    v-for="(company, index) in companies"
                    :key="company.id"
                    :name="company.name"
                    :description="company.description"
                />
            </div>
        </main>

        <!-- Footer Component -->
        <AppFooter />
    </div>
</template>