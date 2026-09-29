<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    meta: PaginationMeta;
}>();
</script>

<template>
    <div v-if="meta?.last_page > 1" class="pagination-container mt-8 flex justify-center gap-1">
        <div class="pagination-wrapper flex items-center gap-2">
            <Link
                v-for="link in meta.links"
                :key="link.label"
                :href="link.url || '#'"
                v-html="link.label"
                preserve-scroll
                class="pagination-link rounded-md border px-3 py-1 text-sm transition-all hover:border-foreground"
                :class="{
                    'pagination-link-active bg-blue-600 text-white border-blue-600 hover:bg-blue-700': link.active,
                    'pagination-link-disabled opacity-50 cursor-not-allowed hover:border hover:border-red-600': !link.url
                }"
            />
        </div>
    </div>
</template>