<script setup lang="ts">
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance } = useAppearance();

const logoSrc = computed(() => resolvedAppearance.value === 'dark' ? '/img/logo-dark.svg' : '/img/logo.svg');
</script>

<template>
     <div>
         <img :src="logoSrc" alt="TaxesAccountant Logo" class="h-6 w-auto" />
     </div>
</template>
