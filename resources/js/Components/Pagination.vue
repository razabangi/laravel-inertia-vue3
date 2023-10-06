<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object
    }
})
</script>

<template>
    <nav class="relative flex justify-end p-2">
        <template v-for="link in data.links" :key="link.label">
            <Link :href="link.url ?? ''" v-html="link.label" class="flex items-center justify-center px-3 py-2 text-sm rounded-lg text-gray-600" :class="{ 'bg-gray-200' : link.active, '!text-gray-400' : !link.url }" :disabled="!link.url"></Link>
        </template>
    </nav>

</template>