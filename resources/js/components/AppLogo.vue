<script setup lang="ts">
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance } = useAppearance();

const logoSrc = computed(() => resolvedAppearance.value === 'dark' ? '/img/ta-logo-dark.svg' : '/img/ta-logo.svg');
</script>

<template>
     <div>
         <img :src="logoSrc" alt="TaxesAccountant Logo" class="h-12 w-auto" />
     </div>
</template>
