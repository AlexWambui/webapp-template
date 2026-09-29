<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch, computed } from 'vue';
import { Pencil, Trash2 } from '@lucide/vue';
import AppPageHeader from '@/components/custom/AppPageHeader.vue';
import DeleteConfirmationDialog from '@/components/custom/DeleteConfirmation.vue';
import Pagination from '@/components/custom/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import usersRoutes from '@/routes/users';

interface User {
    id: number;
    uuid: string;
    name: string;
    email: string;
    phone: string;
    role_label: string;
    status: boolean;
    is_active: boolean;
}

interface Props {
    users: {
        data: User[];
        meta: {
            current_page: number;
            from: number;
            last_page: number;
            links: { url: string | null; label: string; active: boolean }[];
            path: string;
            per_page: number;
            to: number;
            total: number;
        };
        links: {
            first: string | null;
            last: string | null;
            prev: string | null;
            next: string | null;
        };
    };
    role_counts: Record<string, { count: number; label: string }>;
    filters: {
        search?: string;
        role?: string;
    };
}

const props = defineProps<Props>();

// ==================== State ====================
const search = ref(props.filters.search || '');

const handleSearch = (value: string) => {
    router.get(usersRoutes.index().url, {
        search: value,
        role: selectedRole.value,
    }, {
        preserveState: true,
        replace: true,
    });
};


const selectedRole = ref(props.filters.role || '');

const totalUsers = computed(() => 
    Object.values(props.role_counts).reduce((sum, role) => sum + role.count, 0)
);

const roleFilters = computed(() => 
    Object.entries(props.role_counts).map(([value, role]) => ({
        value,
        label: role.label,
        count: role.count,
    }))
);

const hasActiveFilters = computed(() => 
    !!(search.value || selectedRole.value)
);

const activeFilterText = computed(() => {
    if (selectedRole.value) {
        const role = roleFilters.value.find(r => r.value === selectedRole.value);

        return `${role?.label} role`;
    }

    if (search.value) {
        return `"${search.value}"`;
    }

    return '';
});

const roleBadgeConfig: Record<string, string> = {
    'Super Admin': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'Admin': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'Seller': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'Customer': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
};

const getRoleBadgeClass = (roleLabel: string): string => 
    roleBadgeConfig[roleLabel] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';

const getDisplayRange = computed(() => {
    const { current_page, per_page, total } = props.users.meta;
    const start = (current_page - 1) * per_page + 1;
    const end = Math.min(current_page * per_page, total);

    return { start, end, total };
});

const debouncedRole = useDebounceFn(() => {
    router.get(usersRoutes.index().url, {
        role: selectedRole.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([selectedRole], () => {
    debouncedRole();
});

const filterByRole = (roleValue: string) => {
    selectedRole.value = selectedRole.value === roleValue ? '' : roleValue;
};

const clearFilters = () => {
    search.value = '';
    selectedRole.value = '';
};

const isRoleActive = (roleValue: string) => selectedRole.value === roleValue;

const getRowNumber = (index: number) => (props.users.meta.current_page - 1) * props.users.meta.per_page + index + 1;
</script>

<template>
    <Head title="Users" />

    <div class="app-container">
        <AppPageHeader
            resourceName="Users"
            v-model="search"
            search-placeholder="Search by name or email..."
            create-url="/users/create"
            create-label="User"
            @search="handleSearch"
        />

        <div class="stats-filters rounded-lg border bg-gray-50 p-4 mb-4 dark:bg-gray-800/50">
            <div class="stats-filters-wrapper flex flex-wrap items-center gap-2">
                <button
                    :class="{
                        'bg-blue-600 text-white': !selectedRole,
                        'hover:bg-gray-200 dark:hover:bg-gray-700': selectedRole
                    }"
                    class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                    @click="filterByRole('')"
                >
                    {{ totalUsers }} Users
                </button>

                <button
                    v-for="role in roleFilters"
                    :key="role.value"
                    :class="{
                        'bg-blue-600 text-white': isRoleActive(role.value),
                        'hover:bg-gray-200 dark:hover:bg-gray-700': !isRoleActive(role.value)
                    }"
                    class="rounded-md px-3 py-1.5 text-sm font-medium transition-all"
                    @click="filterByRole(role.value)"
                >
                    {{ role.count }} {{ role.label }}{{ role.count !== 1 ? 's' : '' }}
                </button>

                <button v-if="hasActiveFilters" @click="clearFilters" class="clear-filters ml-auto text-sm text-red-600 underline hover:text-red-800 dark:text-red-400;">
                    Clear Filters
                </button>
            </div>

            <p v-if="hasActiveFilters" class="active-filters-indicator mt-2 text-xs text-blue-600 dark:text-blue-400">
                <span class="font-medium">Active filter:</span> {{ activeFilterText }}
            </p>
        </div>

        <div class="md:hidden space-y-3">
            <div
                v-for="(user, index) in users.data"
                :key="user.id"
                class="border border-border rounded-lg p-4 bg-card shadow-sm"
            >
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs text-muted-foreground">
                            #{{ (users.meta.current_page - 1) * users.meta.per_page + index + 1 }}
                        </p>
                        <p class="font-semibold text-base">{{ user.name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link :href="usersRoutes.edit(user.uuid).url" class="action edit p-2">
                            <Pencil class="w-4 h-4 text-green-600" />
                        </Link>
                        <DeleteConfirmationDialog
                            :url="usersRoutes.destroy(user.uuid).url"
                            title="Delete User?"
                            description="This user will be deleted permanently!"
                            confirm-text="Delete User"
                        >
                            <template #trigger>
                                <button class="action delete p-2">
                                    <Trash2 class="w-4 h-4 text-red-600" />
                                </button>
                            </template>
                        </DeleteConfirmationDialog>
                    </div>
                </div>

                <div class="space-y-1.5 text-sm mb-3">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Email:</span>
                        <span class="font-medium text-right">{{ user.email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Phone:</span>
                        <span class="font-medium text-right">{{ user.phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Role:</span>
                        <span class="font-medium text-right">{{ user.role_label }}</span>
                    </div>
                </div>
            </div>

            <div
                v-if="users.data.length === 0"
                class="border border-border rounded-lg p-8 text-center text-muted-foreground"
            >
                No users found.
            </div>
        </div>

        <div class="table-wrapper hidden md:block">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="id">#</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead class="actions">Actions</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="(user, index) in props.users.data" :key="user.id">
                        <TableCell class="id">{{ getRowNumber(index) }}</TableCell>
                        <TableCell :class="{ 'text-red-500': !user.is_active }">
                            {{ user.name }}
                        </TableCell>
                        <TableCell>{{ user.email || '-' }}</TableCell>
                        <TableCell>{{ user.phone || '-' }}</TableCell>
                        <TableCell>
                            <span :class="getRoleBadgeClass(user.role_label)" class="pill">
                                {{ user.role_label }}
                            </span>
                        </TableCell>
                        <TableCell class="actions">
                            <div class="actions-wrapper">
                                <Link :href="usersRoutes.edit(user.uuid).url" class="action edit">
                                    Edit
                                </Link>
                                <span class="divider">|</span>
                                <DeleteConfirmationDialog :url="usersRoutes.destroy(user.uuid).url" title="Delete User?" description="This user will be deleted permanently!" confirm-text="Delete User">
                                    <template #trigger>
                                        <button class="action delete">
                                            Delete
                                        </button>
                                    </template>
                                </DeleteConfirmationDialog>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="props.users.data.length === 0">
                        <TableCell colspan="5" class="blank-table-row">
                            No users found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <Pagination :meta="users.meta" />

        <div class="table-results-summary mt-4 py-4 flex flex-col items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400 sm:flex-row sm:gap-4">
            <p>
                Showing {{ getDisplayRange.start }} to {{ getDisplayRange.end }}
                of {{ getDisplayRange.total }} users
            </p>
            <p v-if="hasActiveFilters" class="filtered-results text-blue-600 dark:text-blue-400">
                Filtered results
            </p>
        </div>
    </div>
</template>