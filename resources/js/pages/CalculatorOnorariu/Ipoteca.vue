<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ipoteca',
        href: '/calculator/ipoteca',
        
    },
];

</script>




<template>
    <AppLayout :breadcrumbs="breadcrumbs">

           <!-- Sub-navigation links styled with Tailwind CSS -->
           <div class="navigation space-x-4 flex justify-start items-center pl-4 rounded-lg shadow-md">
            <Link :href="route('calculator')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Vanzari
            </Link>
            <Link :href="route('ipoteca')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Ipoteca
            </Link>
            <Link :href="route('succesiune')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Succesiune
            </Link>
        </div>



        <div class="calculator-onorariu-ipoteca">
            <h1>Test Screen</h1>
            <p>This is a test screen for the Ipoteca page.</p>
           
        </div>
    </AppLayout>
</template>
