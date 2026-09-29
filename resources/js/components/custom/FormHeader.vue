<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';

interface Props {
    backUrl: string;
    title: string;
    backLabel?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="form_header">
        <Link :href="backUrl" :title="backLabel || 'Go back'">
            <ArrowLeft />
        </Link>
        <h2>{{ title }}</h2>
    </div>
</template>