<script setup>
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const showing = ref(false);

watch(() => usePage().props.flash, () => {
    showing.value = true;
});

onMounted(() => {
    showing.value = true;
});
</script>

<template>
    <Transition
        enter-from-class="opacity-0 scale-95"
        enter-active-class="duration-500 ease-out"
        enter-to-class="opacity-100 scale-100"
        leave-from-class="opacity-100 scale-100"
        leave-active-class="duration-500 ease-in"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-show="($page.props.flash.success || $page.props.flash.error) && showing"
            :class="['mb-6 p-4 flex items-center justify-between gap-6 text-white', $page.props.flash.error ? 'bg-red-600' : 'bg-green-600', 'rounded-md']"
        >
            <div>
                {{ $page.props.flash.success ? $page.props.flash.success : $page.props.flash.error }}
            </div>

            <div>
                <button
                    type="button"
                    class="px-2 text-white text-2xl"
                    @click="showing = false"
                >
                    &times;
                </button>
            </div>
        </div>
    </Transition>
</template>
