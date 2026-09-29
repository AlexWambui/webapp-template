<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

interface Props {
    resourceName: string;
    createUrl?: string;
    createLabel?: string;
    searchPlaceholder?: string;
    modelValue?: string;
}

interface Emits {
    (e: 'update:modelValue', value: string): void;
    (e: 'search', value: string): void;
}

const props = withDefaults(defineProps<Props>(), {
    createUrl: '',
    createLabel: 'New',
    searchPlaceholder: 'Search...',
    modelValue: '',
});

const emit = defineEmits<Emits>();

const search = ref(props.modelValue);

const debouncedSearch = useDebounceFn((value: string) => {
    emit('search', value);
    emit('update:modelValue', value);
}, 300);

watch(search, (value) => {
    debouncedSearch(value);
});

watch(() => props.modelValue, (value) => {
    search.value = value;
});
</script>

<template>
    <div class="AppPageHeader grid lg:grid-cols-3 gap-4 md:gap-6 mb-4">
        <div class="info">
            <h1 class="font-semibold">{{ resourceName }}</h1>
        </div>
        
        <div class="search lg:flex lg:justify-center">
            <Input
                v-model="search"
                type="text"
                :placeholder="searchPlaceholder"
                class="w-full"
            />
        </div>

        <div v-if="createUrl" class="action lg:justify-self-end">
            <Link :href="createUrl">
                <Button>New {{ createLabel }}</Button>
            </Link>
        </div>
    </div>
</template>