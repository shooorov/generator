<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
    XMarkIcon,
    ArrowTopRightOnSquareIcon
} from '@heroicons/vue/24/solid'

import {
    CheckBadgeIcon,
    DocumentCheckIcon,
    CogIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
    desk: Object,
    desks: Array,
    pillar_types: Array,
})

const getColumn = () => {
    form.group_pillars.forEach(item => {
        let split_name = item.title.toLowerCase().replace(/[^a-z ]/g, '').split(' ');
        item.column = split_name.join('_');
    });
}

const removePillar = (index)=> {
    if (confirm('Are you sure you want to delete this element?')) {
        form.group_pillars.splice(index, 1);
    }
}

const addPillar = () => {
    form.group_pillars.push({
        title: null,
        column: null,
        unique: false,
        default: null,
        indexing: null,
        filtering: null,
        requisite: true,
        pillar_type_id: '',
    });
}

const dressUp = (strings) => {
    strings = strings.split('_');
    for(let i = 0; i < strings.length && strings[i].length > 1; i++){
        strings[i] = strings[i].charAt(0).toUpperCase() + strings[i].slice(1);
    }
    return strings.join(' ');
}

const setPillar = (index)=> {
    let selected = form.group_pillars[index];
    selected.title = dressUp(selected.pillar_name);
    selected.attribute = selected.pillar_name.toLowerCase();

    let found_pillar = pillar_types.find(item => item.name == selected.pillar_name)
    if(found_pillar){
        selected.title = dressUp(found_pillar.name)
        selected.attribute = found_pillar.name.toLowerCase()
    }
}

const breadcrumbs = [
    { name: 'Pillars', href: route('generator.pillar.index'), current: false },
    { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
    { name: 'Desks', href: route('generator.desk.index'), current: false },
    { name: 'Edit Page', href: '#', current: false },
]

const form = useForm({
    name: props.desk.name,
    directory: props.desk.directory,
    child_table: props.desk.child_table,
    parent_table: props.desk.parent_table,

    has_filter: props.desk.has_filter,
    has_opening: props.desk.has_opening,
    columns_in_row: props.desk.columns_in_row,
    has_polymorphic: props.desk.has_polymorphic,
    has_description: props.desk.has_description,
    has_remark: props.desk.has_remark,
    has_has_soft_deletes: props.desk.has_has_soft_deletes,

    group_pillars: props.desk.group_pillars.length? props.desk.group_pillars : [{
        title: null,
        table_id: null,
        column: null,
        unique: false,
        default: null,
        indexing: null,
        filtering: null,
        requisite: true,
        pillar_type_id: null,
    }],
})

const submit = () => {
    form.post(route('generator.desk.update', props.desk.id), {
        onFinish: () => {
        }
    });
}
</script>

<template>
    <Head title="Edit Desk"></Head>

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

    <div class="max-w-full mx-auto py-5 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="flex justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
                <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> {{ desk.name }} Desk Edit</p>

                <div class="flex-shrink-0 flex space-x-3">
                    <Link :href="route('generator.desk.decorate', desk.id)" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500">
                        <CogIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                        Decorate
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
                    <div class="max-w-6xl mx-auto">
                        <div class="py-2 sm:grid sm:grid-cols-4 sm:gap-2">
                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Form</dt>
                            <dd class="flex mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 gap-5">
                                <label class="my-2 inline-flex items-center">
                                    <input v-model="form.columns_in_row" value="2" type="radio" class="form-radio w-5 h-5 border-primary-500 text-primary-500 cursor-pointer focus:ring-primary-500">
                                    <span class="ml-2 tracking-wide cursor-pointer"> 2 in a Row</span>
                                </label><br>
                                <label class="my-2 inline-flex items-center">
                                    <input v-model="form.columns_in_row" value="3" type="radio" class="form-radio w-5 h-5 border-primary-500 text-primary-500 cursor-pointer focus:ring-primary-500">
                                    <span class="ml-2 tracking-wide cursor-pointer"> 3 in a Row</span>
                                </label><br>
                            </dd>
                        </div>

                        <div class="py-2 sm:grid sm:grid-cols-4 sm:gap-2">
                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Desk Name <span class="text-red-500">*</span> </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <input v-model="form.name" placeholder="Name" type="text" class="block w-full px-4 focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                            </dd>
                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-center pr-8 sm:col-span-2"> Example: Custom Table Name </dt>
                        </div>

                        <div class="py-2 sm:grid sm:grid-cols-4 sm:gap-2">
                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Parent Table </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <input v-model="form.parent_table" placeholder="Parent Table" type="text" class="block w-full px-4 focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                            </dd>

                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Child Table </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <input v-model="form.child_table" placeholder="Child Table" type="text" class="block w-full px-4 focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                            </dd>
                        </div>

                        <div class="py-2 sm:grid sm:grid-cols-4 sm:gap-2">
                            <dt class="text-sm leading-10 font-semibold text-gray-700 tracking-wider text-right pr-8"> Directory </dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <input v-model="form.directory" placeholder="Directory" type="text" class="block w-full px-4 focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded">
                            </dd>
                        </div>

                        <div class="py-2 sm:grid sm:grid-cols-4 sm:gap-2">
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_filter" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_filter" @click="form.has_filter = !(form.has_filter)" class="ml-5 block cursor-pointer">Has Filter </label>
                                </div>
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_opening" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_opening" @click="form.has_opening = !(form.has_opening)" class="ml-5 block cursor-pointer">Has Opening </label>
                                </div>
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_polymorphic" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_polymorphic" @click="form.has_polymorphic = !(form.has_polymorphic)" class="ml-5 block cursor-pointer">Has Polymorphic </label>
                                </div>
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_description" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_description" @click="form.has_description = !(form.has_description)" class="ml-5 block cursor-pointer">Has Description </label>
                                </div>
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_remark" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_remark" @click="form.has_remark = !(form.has_remark)" class="ml-5 block cursor-pointer">Has Remark </label>
                                </div>
                            </dd>

                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">
                                <div class="mt-1 inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                    <input v-model="form.has_soft_deletes" type="checkbox" class="group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer">
                                    <label for="has_soft_deletes" @click="form.has_soft_deletes = !(form.has_soft_deletes)" class="ml-5 block cursor-pointer">Has softDeletes </label>
                                </div>
                            </dd>
                        </div>
                    </div>

                    <div class="max-w-7xl mx-auto">
                        <table class="table-auto sm:table-fixed min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th rowspan="2" scope="col" class="w-36 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th rowspan="2" colspan="2" scope="col" class="p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <!-- <th rowspan="2" scope="col" class="w-32 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Column</th> -->
                                    <th colspan="2" scope="col" class="w-10 p-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Index</th>
                                    <th rowspan="2" scope="col" class="w-10 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique</th>
                                    <th rowspan="2" scope="col" class="w-80 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                    <th rowspan="2" scope="col" class="w-16 p-2 text-left text-5xl font-medium text-gray-500 uppercase tracking-wider"><!-- Required --><span class="text-red-500">*</span></th>
                                </tr>
                                <tr>
                                    <th scope="col" class="w-10 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filtering</th>
                                    <th scope="col" class="w-10 p-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(group_pillar, index) in form.group_pillars" :key="index">
                                    <td>
                                        <Combobox v-model="group_pillar.pillar_type_id" :items="pillar_types.map((item) => { return { id: item.id, name: item.name?? item.title } })" class="w-full" />
                                    </td>

                                    <td :colspan="group_pillar.pillar_type_id == pillar_types.find(i => i.name == 'foreignId')?.id ? 1 : 2">
                                        <input v-model="group_pillar.title" @keyup="getColumn()" placeholder="Title" type="text" :title="'column: ' + group_pillar.column" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </td>

                                    <td v-if="group_pillar.pillar_type_id == 10">
                                        <Combobox v-model="group_pillar.table_id" :items="desks.map((item) => { return { id: item.id, name: item.name?? item.title } })" class="w-full" />
                                    </td>

                                    <!-- <td>
                                        <input v-model="group_pillar.column" readonly placeholder="Column" type="text" class="block w-full px-2 focus:ring-none focus:ring-0 focus:ring-primary-400 focus:border-primary-400 bg-gray-100 sm:text-sm border-gray-300 rounded" />
                                    </td> -->

                                    <td>
                                        <input v-model="group_pillar.filtering" placeholder="Filtering" type="text" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </td>

                                    <td>
                                        <input v-model="group_pillar.indexing" placeholder="Indexing" type="text" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </td>

                                    <td>
                                        <div class="inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <input v-model="group_pillar.unique" type="checkbox" class="ml-6 group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer" />
                                        </div>
                                    </td>

                                    <td>
                                        <input v-model="group_pillar.default" placeholder="Default" type="text" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />
                                    </td>

                                    <td>
                                        <div class="inline-flex items-center py-2 text-sm font-medium rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            <input v-model="group_pillar.requisite" type="checkbox" class="mx-2 group-checkbox form-checkbox w-5 h-5 focus:ring-primary-600 focus:border-primary-600 text-primary-600 transition duration-150 ease-in-out rounded select-none cursor-pointer" />

                                            <button v-show="form.group_pillars.length > 1" @click="[removePillar(index), setPillar(0)]" type="button" class="p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="bg-white">
                                <tr>
                                    <th colspan="1" class="text-left pt-3">
                                        <button @click="addPillar()" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            Add Pillar
                                            <PlusIcon class="ml-2 -mr-0.5 h-4 w-4" aria-hidden="true" />
                                        </button>
                                    </th>
                                    <th colspan="6" class="py-2 text-center text-lg font-medium">Foreign Table ID: Table Name ID / table_name_id</th>
                                </tr>
                            </tfoot>
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
</template>
