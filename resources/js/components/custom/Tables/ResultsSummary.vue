<!-- components/custom/TableResultsSummary.vue -->
<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    meta: {
        current_page: number;
        per_page: number;
        total: number;
    };
    itemName?: string;
    itemNamePlural?: string;
    showFilterIndicator?: boolean;
    hasActiveFilters?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    itemName: 'item',
    itemNamePlural: 'items',
    showFilterIndicator: false,
    hasActiveFilters: false,
});

const displayRange = computed(() => {
    const { current_page, per_page, total } = props.meta;
    const start = (current_page - 1) * per_page + 1;
    const end = Math.min(current_page * per_page, total);
    return { start, end, total };
});

const itemLabel = computed(() => {
    return displayRange.value.total === 1 ? props.itemName : props.itemNamePlural;
});
</script>

<template>
    <div class="table-results-summary">
        <p>
            Showing {{ displayRange.start }} to {{ displayRange.end }}
            of {{ displayRange.total }} {{ itemLabel }}
        </p>
        <p v-if="showFilterIndicator && hasActiveFilters" class="filtered-results">
            Filtered results
        </p>
    </div>
</template>
