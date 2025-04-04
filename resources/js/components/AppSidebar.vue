<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { FileCheck2, FilePen, Calculator, FileDown, UserPen } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Templateuri',
        href: '/',
        icon: FileCheck2,
    },

    {
        title: 'Contracte',
        href: '#',
        icon: FilePen,
    },

    {
        title: 'Calculator Onorariu',
        href: '/calculator',
        icon: Calculator,
      
    },

    
    {
        title: 'Formular 28',
        href: '#',
        icon: FileDown,
    },

    {
        title: 'CRM Clienti',
        href: '#',
        icon: UserPen,
    },

    
];


</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="sm" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        
    </Sidebar>
    <slot />
</template>
