<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'
import Breadcrumb from '../Components/Breadcrumb.vue';
import Combobox from '../Components/Combobox.vue';

import {
    ArrowTopRightOnSquareIcon,
    PlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/solid'

import {
    CheckBadgeIcon,
    DocumentCheckIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
    desk: Object,
    pillar_types: Array,
})

const breadcrumbs = [
    { name: 'Pillars', href: route('generator.pillar.index'), current: false },
    { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
    { name: 'Desks', href: route('generator.desk.index'), current: false },
    { name: 'Decorate Page', href: '#', current: false },
]

const form = useForm({
    has_filter: props.desk.has_filter,
    has_opening: props.desk.has_opening,
    has_polymorphic: props.desk.has_polymorphic,
    has_description: props.desk.has_description,
    has_remark: props.desk.has_remark,
    has_soft_deletes: props.desk.has_soft_deletes,

    generate_cache: props.desk.generate_cache,
    generate_pages: props.desk.generate_pages,
    generate_model: props.desk.generate_model,
    generate_seeder: props.desk.generate_seeder,
    generate_migration: props.desk.generate_migration,
    generate_resources: props.desk.generate_resources,
    generate_controller: props.desk.generate_controller,

    group_pillars: props.desk.group_pillars
})

const submit = () => {
    form.post(route('generator.desk.decoration', props.desk.id), {
        onFinish: () => {
        }
    });
}
</script>
<template>
    <Head title="Decorate Desk"></Head>

    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <Breadcrumb :breadcrumbs="breadcrumbs" />
                </div>
                <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                    <Link :href="route('generator.desk.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                        <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                        Create
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <div class="py-5">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="flex justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
                    <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> {{ desk.name }} Desk Decorate</p>

                    <div class="flex-shrink-0 flex space-x-3">
                        <Link :href="route('generator.desk.edit', desk.id)" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500">
                            <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Edit
                        </Link>

                        <Link v-show="desk.route" :href="desk.route?.index" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500">
                            <ArrowTopRightOnSquareIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            To&nbsp;Index
                        </Link>

                        <Link :href="route('generator.desk.generate_files', desk.id)" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500">
                            <DocumentCheckIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Generate&nbsp;Files
                        </Link>

                        <button type="submit" @click="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <CheckBadgeIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                            Update
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <dl class="space-y-8 py-6">
                        <div class="max-w-3xl mx-auto">
                            <div class="py-2 sm:grid sm:grid-cols-8 sm:gap-2">
                                <!-- <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"></dt> -->
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.has_filter" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="has_filter" @click="form.has_filter = !(form.has_filter)" class="ml-5 block cursor-pointer"> Filter </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.has_opening" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="has_opening" @click="form.has_opening = !(form.has_opening)" class="ml-5 block cursor-pointer"> Opening </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.has_description" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="has_description" @click="form.has_description = !(form.has_description)" class="ml-5 block cursor-pointer"> Description </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.has_remark" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="has_remark" @click="form.has_remark = !(form.has_remark)" class="ml-5 block cursor-pointer"> Remark </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_cache" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_cache" @click="form.generate_cache = !(form.generate_cache)" class="ml-5 block cursor-pointer"> Cache </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_pages" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_pages" @click="form.generate_pages = !(form.generate_pages)" class="ml-5 block cursor-pointer"> Pages </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_model" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_model" @click="form.generate_model = !(form.generate_model)" class="ml-5 block cursor-pointer"> Model </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_seeder" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_seeder" @click="form.generate_seeder = !(form.generate_seeder)" class="ml-5 block cursor-pointer"> Seeder </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_migration" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_migration" @click="form.generate_migration = !(form.generate_migration)" class="ml-5 block cursor-pointer"> Migration </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_controller" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_controller" @click="form.generate_controller = !(form.generate_controller)" class="ml-5 block cursor-pointer"> Controller </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.generate_resources" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="generate_resources" @click="form.generate_resources = !(form.generate_resources)" class="ml-5 block cursor-pointer"> Resources </label>
                                    </div>
                                </dd>

                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                        <input v-model="form.has_soft_deletes" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                        <label for="has_soft_deletes" @click="form.has_soft_deletes = !(form.has_soft_deletes)" class="ml-5 block cursor-pointer"> SoftDeletes </label>
                                    </div>
                                </dd>
                            </div>
                        </div>

                        <div class="max-w-3xl mx-auto">
                            <table class="p-5 table-auto sm:table-fixed min-w-full divide-y divide-gray-200 border-2 rounded">
                                <thead class="bg-gray-50 rounded">
                                    <tr>
                                        <th scope="col" class="w-full px-3 py-2 text-left text-xs border-x font-medium text-gray-500 uppercase tracking-wider">Column</th>
                                        <th scope="col" class="w-28 px-3 py-2 text-left text-xs border-x font-medium text-gray-500 uppercase tracking-wider">Input Position</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs border-x font-medium text-gray-500 uppercase tracking-wider">Column in Row</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs border-x font-medium text-gray-500 uppercase tracking-wider">Empty Column</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(pillar, index) in form.group_pillars" :key="index">
                                        <th>
                                            <label class="block w-full p-2 text-left hover:bg-gray-100 sm:text-sm border border-gray-300 rounded">{{ pillar.title }} </label>
                                        </th>

                                        <td>
                                            <input v-model="pillar.decorating" placeholder="Position" type="text" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                        </td>

                                        <td>
                                            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                                <button type="button" @click="pillar.columns_in_row = 1" :class="[ pillar.columns_in_row == 1? 'bg-slate-200' : 'hover:bg-gray-50', 'relative inline-flex items-center px-4 py-2 rounded-l border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500']">1</button>
                                                <button type="button" @click="pillar.columns_in_row = 2" :class="[ pillar.columns_in_row == 2? 'bg-slate-200' : 'hover:bg-gray-50', '-ml-px relative inline-flex items-center px-4 py-2 rounded-r border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500']">2</button>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                                <button type="button" @click="pillar.empty_column = 'before'" :class="[ pillar.empty_column == 'before'? 'bg-slate-200' : 'hover:bg-gray-50', 'relative inline-flex items-center px-4 py-2 rounded-l border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500']">Before</button>
                                                <button type="button" @click="pillar.empty_column = 'none'" :class="[ pillar.empty_column == 'none'? 'bg-slate-200' : 'hover:bg-gray-50', '-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500']">None</button>
                                                <button type="button" @click="pillar.empty_column = 'after'" :class="[ pillar.empty_column == 'after'? 'bg-slate-200' : 'hover:bg-gray-50', '-ml-px relative inline-flex items-center px-4 py-2 rounded-r border border-gray-300 bg-white text-sm font-medium text-gray-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500']">After</button>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true"> <div class="w-full border-t border-gray-300" /> </div>
                            <div class="relative flex justify-center">
                                <span class="px-3 bg-white text-lg font-medium text-gray-900"></span>
                            </div>
                        </div>

                        <div class="max-w-xl mx-auto">
                            <div class="flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <CheckBadgeIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                    Update
                                </button>
                            </div>
                        </div>
                    </dl>
                </form>
            </div>
        </div>
    </div>
</template>
