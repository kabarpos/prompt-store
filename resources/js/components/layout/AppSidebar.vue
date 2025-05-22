<script setup lang="ts">
import { defineComponent as _defineComponent } from "vue";
import NavMain from "@/components/layout/NavMain.vue";
import NavUser from "@/components/layout/NavUser.vue";
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, UsersIcon, ShieldIcon, KeyIcon, Settings, Package, FolderTree, ShoppingBag, BarChart3, Receipt, Wallet, Mail, CreditCard, CheckSquare, TicketPercent, MessageSquare } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

// Ambil informasi user dari usePage
const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);

// Cek apakah user memiliki role tertentu
const hasRole = (role: string) => {
    if (!user.value || !user.value.roles) return false;
    return user.value.roles.some((r: { name: string }) => r.name === role);
};

// Cek apakah user memiliki permission tertentu
const hasPermission = (permission: string) => {
    if (!user.value || !user.value.permissions) return false;
    return user.value.permissions.includes(permission);
};

// Filter item navigasi berdasarkan persyaratan akses
const filterNavItems = (items: NavItem[]) => {
    return items.filter(item => {
        // Jika tidak ada requirement, semua orang bisa akses
        if (!item.requiresRole && !item.requiresPermission) return true;
        
        // Cek role
        if (item.requiresRole && !hasRole(item.requiresRole)) return false;
        
        // Cek permission
        if (item.requiresPermission && !hasPermission(item.requiresPermission)) return false;
        
        return true;
    });
};

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'My Orders',
        href: route('orders.index'),
        icon: ShoppingBag,
    },
    {
        title: 'My Digital Products',
        href: '/digital-products',
        icon: Package,
    },
    {
        title: 'Documents',
        href: route('my-documents'),
        icon: Mail,
    },
    {
        title: 'Confirmations',
        href: route('orders.payment.index'),
        icon: CheckSquare,
    }
];

// Menu untuk administrasi
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutGrid,
        requiresRole: 'admin',
    },
    {
        title: 'Orders User',
        href: route('admin.orders.index'),
        icon: ShoppingBag,
        requiresRole: 'admin',
        requiresPermission: 'view orders',
    },
    {
        title: 'Statistics Orders',
        href: route('admin.orders.statistics'),
        icon: BarChart3,
        requiresRole: 'admin',
        requiresPermission: 'view orders',
    },
    {
        title: 'Document Orders',
        href: route('admin.documents.all'),
        icon: Receipt,
        requiresRole: 'admin',
        requiresPermission: 'view orders',
    },
    {
        title: 'Products',
        href: route('admin.products.index'),
        icon: Package,
        requiresRole: 'admin',
        requiresPermission: 'manage_products',
    },
    {
        title: 'Payment Methods',
        href: route('admin.payment-methods.index'),
        icon: CreditCard,
        requiresRole: 'admin',
    },
    {
        title: 'Coupons',
        href: route('admin.coupons.index'),
        icon: TicketPercent,
        requiresRole: 'admin',
        requiresPermission: 'manage_settings',
    },
    {
        title: 'Confirmations',
        href: route('admin.payment-confirmations.index'),
        icon: Receipt,
        requiresRole: 'admin',
        requiresPermission: 'manage payments',
    },
    {
        title: 'WhatsApp Templates',
        href: route('admin.notifications.index'),
        icon: MessageSquare,
        requiresRole: 'admin',
        requiresPermission: 'manage_settings',
    },
    {
        title: 'Expenses',
        href: route('admin.expenses.index'),
        icon: Wallet,
        requiresRole: 'admin',
        requiresPermission: 'view expenses',
    },
    {
        title: 'Users',
        href: route('admin.users.index'),
        icon: UsersIcon,
        requiresRole: 'admin',
        requiresPermission: 'manage_users',
    },
    {
        title: 'Role & Permissions',
        href: route('admin.roles.index'),
        icon: ShieldIcon,
        requiresRole: 'admin',
        requiresPermission: 'manage_roles',
    },
    {
        title: 'Settings',
        href: route('admin.settings.index'),
        icon: Settings,
        requiresRole: 'admin',
        requiresPermission: 'manage_settings',
    }
];

// Computed untuk menu yang difilter
const filteredMainNavItems = computed(() => filterNavItems(mainNavItems));
const filteredAdminNavItems = computed(() => filterNavItems(adminNavItems));
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader class="sidebar-header">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="logo-container">
                        <Link :href="route('dashboard')" class="flex items-center w-full px-2 py-1">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Menu Utama User-->
            <NavMain :items="filteredMainNavItems" label="Navigasi User" />
            <!-- Menu admin -->
            <NavMain v-if="filteredAdminNavItems.length > 0" :items="filteredAdminNavItems" label="Admin Panel" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
/* Styling untuk header sidebar */
:deep(.sidebar-header) {
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* Styling untuk container logo */
:deep(.logo-container) {
    padding: 0.5rem;
    height: auto;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    transition: background-color 0.2s;
}

:deep(.logo-container:hover) {
    background-color: rgba(255, 255, 255, 0.05);
}

:deep(.logo-container a) {
    width: 100%;
    display: flex;
    align-items: center;
}
</style>
