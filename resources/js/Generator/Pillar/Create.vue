<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid'

const page = usePage()

const props = defineProps({
    pillar_types: Array,
})

const breadcrumbs = [
    { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
    { name: 'Desks', href: route('generator.desk.index'), current: false },
    { name: 'Pillars', href: route('generator.pillar.index'), current: false },
    { name: 'Create Page', href: '#', current: false },
]

const form = useForm({
    name: null,
    pillar_type_id: null,
})

const submit = () => {
    form.post(route('generator.pillar.store'), {
        onFinish: () => {
        }
    });
}
</script>
<template>
    <Head title="Create Pillar"></Head>
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <Breadcrumb :breadcrumbs="breadcrumbs" />
                </div>

                <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                </div>
            </div>
        </div>
    </div>

    <div class="py-5">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="flex justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
                    <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> Pillar Create</p>

                    <div class="flex-shrink-0 flex space-x-3">
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <dl class="space-y-8 py-6">
                        <div class="max-w-xl mx-auto">
                            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-2">
                                <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider sm:text-right pr-8"> Pillar Type <span class="text-red-500">*</span> </dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <Combobox v-model="form.pillar_type_id" :items="pillar_types" asText="name" asValue="id"/>
                                </dd>
                            </div>

                            <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-2">
                                <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Name <span class="text-red-500">*</span> </dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <input v-model="form.name" placeholder="Name" type="text" class="block w-full px-4 focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                                </dd>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-300" />
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-3 bg-white text-lg font-medium text-gray-900"></span>
                            </div>
                        </div>

                        <div class="max-w-xl mx-auto">
                            <div class="flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                    Create
                                </button>
                            </div>
                        </div>
                    </dl>
                </form>
            </div>
        </div>
    </div>
</template>
