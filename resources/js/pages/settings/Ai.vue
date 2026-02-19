<script setup lang="ts">
import AiController from '@/actions/App/Http/Controllers/Settings/AiController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/ai';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';

type Props = {
    aiEnabled: boolean;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'AI settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user as { role?: string };
const isAccountantOrAdmin = user.role === 'accountant' || user.role === 'admin';

const form = useForm({
    ai_enabled: props.aiEnabled,
});

const submit = () => {
    form.patch(AiController.update().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="AI settings" />

        <h1 class="sr-only">AI Settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="AI document processing"
                    description="Configure automatic AI analysis of uploaded tax documents"
                />

                <template v-if="isAccountantOrAdmin">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="flex items-start space-x-3">
                            <Checkbox
                                id="ai_enabled"
                                :checked="form.ai_enabled"
                                @update:checked="(val: boolean) => form.ai_enabled = val"
                            />
                            <div class="space-y-1">
                                <Label for="ai_enabled" class="font-medium cursor-pointer">
                                    Enable AI document analysis
                                </Label>
                                <p class="text-sm text-muted-foreground">
                                    When enabled, uploaded tax documents (W-2s, 1099s, etc.) are automatically analyzed by AI to extract
                                    income, deductions, and tax details. The extracted data pre-fills the tax return for your review.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                Save
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    Saved.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </template>

                <template v-else>
                    <p class="text-sm text-muted-foreground">
                        Only accountants and administrators can manage AI settings.
                    </p>
                </template>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
