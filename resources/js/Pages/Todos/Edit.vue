<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps(['categories', 'todo', 'users']);

const form = useForm({
    name: props.todo.name,
    description: props.todo.description,
    category_id: props.todo.category_id,
    shares: props.todo.shares.map(share => share.id),
});

const showingDeleteTodoConfirmationModal = ref(false);

const deleteTodoForm = useForm({});

function handleDeleteTodo(id) {
    deleteTodoForm.delete(route('todos.destroy', id), {
        onFinish: () => {
            showingDeleteTodoConfirmationModal.value = false;
        }
    });
}

const restoreTodoForm = useForm({});

function handleRestoreTodo(id) {
    restoreTodoForm.put(route('todos.restore', id));
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Edit Todo" />

        <template #header>
            <div class="flex items-center justify-between gap-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Todo</h2>
                <Link :href="route('todos.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Cancel</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div
                        v-if="todo.deleted_at"
                        class="mb-6 p-4 flex items-center justify-between gap-6 text-white bg-red-600 rounded-md"
                    >
                        This todo has been deleted.
                        <PrimaryButton @click="handleRestoreTodo(todo.id)">Restore</PrimaryButton>
                    </div>

                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Edit Todo</h2>
                        </header>

                        <form @submit.prevent="form.patch(route('todos.update', todo.id))" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name *" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />

                                <textarea
                                    id="description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.description"
                                ></textarea>

                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <InputLabel for="category_id" value="Category *" />

                                <select
                                    id="category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.category_id"
                                    required
                                >
                                    <option disabled value="">Please select one</option>
                                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                                </select>

                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>

                            <div>
                                <InputLabel for="shares" value="Share With" />

                                <select
                                    id="shares"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.shares"
                                    multiple
                                >
                                    <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                                </select>

                                <InputError class="mt-2" :message="form.errors.shares" />
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <PrimaryButton
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Save
                                </PrimaryButton>

                                <DangerButton
                                    v-if="!todo.deleted_at"
                                    type="button"
                                    @click="showingDeleteTodoConfirmationModal = true"
                                >
                                    Delete
                                </DangerButton>

                                <Modal
                                    :show="showingDeleteTodoConfirmationModal"
                                    @close="showingDeleteTodoConfirmationModal = false"
                                >
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            Are you sure you want to delete this todo?
                                        </h2>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <SecondaryButton
                                                type="button"
                                                @click="showingDeleteTodoConfirmationModal = false"
                                            >
                                                Cancel
                                            </SecondaryButton>

                                            <PrimaryButton
                                                type="button"
                                                :class="{ 'opacity-25': deleteTodoForm.processing }"
                                                :disabled="deleteTodoForm.processing"
                                                @click="handleDeleteTodo(todo.id)"
                                            >
                                                Delete
                                            </PrimaryButton>
                                        </div>
                                    </div>

                                </Modal>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
