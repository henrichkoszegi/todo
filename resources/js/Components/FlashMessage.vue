<script setup></script>

<template>
    <div
        v-if="$page.props.flash.success || $page.props.flash.error"
        :class="['mb-6 p-4 flex items-center justify-between gap-6 text-white', $page.props.flash.error ? 'bg-red-600' : 'bg-green-600', 'rounded-md']"
    >
        {{ $page.props.flash.success || $page.props.flash.error }}
    </div>
</template>
