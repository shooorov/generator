<template>
    <Head title="PillarTypes"></Head>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>

                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                        <Link :href="route('generator.pillar_type.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                            <PlusIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                            Create
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="flex justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
                        <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> Pillar Type </p>

                        <div class="flex-shrink-0 flex space-x-3">
                        </div>
                    </div>

                    <table class="table-auto sm:table-fixed min-w-full w-full" id="table">
                        <thead>
                            <tr>
                                <th class="w-20 p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center"> S.No </th>
                                <th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name - Validate</th>
                                <th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Validation - Migration</th>
                                <th class="w-32 p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="(pillar_type, index) in pillar_types" :key="pillar_type.id" :class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
                                <td class="p-2 text-center"> {{ (index + 1) }} </td>
                                <td class="p-2 whitespace-no-wrap">{{ pillar_type.name }}<br>{{ pillar_type.validate }}</td>
                                <td class="p-2 whitespace-no-wrap">
                                    'number' => [{{ pillar_type.validation }}]<br>$table->decimal({{ pillar_type.migration }});
                                </td>
                                <td class="p-2 whitespace-no-wrap">
                                    <div class="flex justify-end">
                                        <Link :href="route('generator.pillar_type.edit', pillar_type.id)" class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
                                            <PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                        <Link :href="route('generator.pillar_type.destroy', pillar_type.id)" class="text-red-600 hover:text-red-800 ml-3" title="destroy">
                                            <TrashIcon class="w-6 h-6" aria-hidden="true" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

</template>

<script>
import { router, Head, Link } from '@inertiajs/vue3'
import Breadcrumb from '@/Components/Breadcrumb.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid'

import {
    ArrowTopRightOnSquareIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline'

export default {
    components: {
        Breadcrumb,
        Head,
        Link,

        PlusIcon,
        ArrowTopRightOnSquareIcon,
        PencilSquareIcon,
        TrashIcon,
    },

    props:{
        pillar_types: Array,
        errors: Object,
        alertMessage: Object,
    },

    mounted() {
        $('#table').DataTable({
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            length: 10,
            dom: "<'flex justify-center sm:justify-end mb-3'><'flex flex-col sm:flex-row justify-between'lf><'block overflow-auto'rt><'flex flex-col sm:flex-row justify-between'ip>",
        });
    },

    methods: {
        deleteRecord(record_id) {
            if(confirm("Do you really want to delete this pillar_type?")) {
                router.get(route('generator.pillar_type.destroy', record_id));
            }
        }
    },

    setup () {
        const breadcrumbs = [
            { name: 'Desks', href: route('generator.desk.index'), current: false },
            { name: 'Pillars', href: route('generator.pillar.index'), current: false },
            { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
            { name: 'List Page', href: '#', current: false },
        ];

        return {
            breadcrumbs,
        }
    },

}
</script>
