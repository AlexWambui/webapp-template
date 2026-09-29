<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 1000 1000"
        :class="className"
        v-bind="$attrs"
    >
        <rect x="100" y="700" width="200" height="200" fill="#58cc02" />
        <polygon
            points="100 500 100 100 900 100 900 900 500 900 500 500 100 500"
            fill="#58cc02"
        />
    </svg>
</template>
