<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance } = useAppearance();

const logoSrc = computed(() => resolvedAppearance.value === 'dark' ? '/img/ta-logo-dark.svg' : '/img/ta-logo.svg');

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <div>
        <img :src="logoSrc" alt="TaxesAccountant Logo" class="w-24" />
    </div>
</template>
