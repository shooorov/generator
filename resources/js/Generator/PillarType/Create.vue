<template>
    <Head title="Create Pillar type"></Head>

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
                    <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> Pillar Type Create</p>

                    <div class="flex-shrink-0 flex space-x-3">
                        <div>
                            'number' => [{{ validation }}]<br>$table->decimal({{ migration }});
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <dl class="space-y-8 py-6">
                        <div class="max-w-xl mx-auto">
                            <div class="grid grid-cols-6 gap-4">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="date" class="block text-sm font-medium text-gray-700"> Name <span class="text-red-500">*</span> </label>
                                    <input v-model="form.name" id="name" placeholder="Name" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="validate" class="block text-sm font-medium text-gray-700"> Request Validate <span class="text-red-500">*</span> </label>
                                    <input v-model="form.validate" @keyup="demoBody()" id="validate" placeholder="Request Validate" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="max_n_min_length" class="block text-sm font-medium text-gray-700"> Max n Min Length Validation </label>
                                    <input v-model="form.max_n_min_length" @keyup="demoBody()" id="max_n_min_length" placeholder="max: 255, min: 191" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="full_n_float_length" class="block text-sm font-medium text-gray-700"> Full n Float Length </label>
                                    <input v-model="form.full_n_float_length" @keyup="demoBody()" id="full_n_float_length" placeholder="5, 3 => 32145.154" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6">
                                    <label for="mimes" class="block text-sm font-medium text-gray-700"> Mimes </label>
                                    <input v-model="form.mimes" @keyup="demoBody()" id="mimes" placeholder="image => mimes:jpeg,png,jpg,gif,svg" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6">
                                    <label for="guide" class="block text-sm font-medium text-gray-700"> Guide </label>
                                    <textarea v-model="form.guide" id="guide" placeholder="Guide Here..." rows="3" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-primary-400 focus:border-primary-400 border-gray-300 rounded" />
                                </div>
                            </div>
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

<script>
import { reactive } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Combobox from '@/Components/Combobox.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid'

import {
    PencilSquareIcon,
} from '@heroicons/vue/24/outline'

export default {
    components: {
        Breadcrumb,
        Head,
        Combobox,
        Link,

        PlusIcon,
        PencilSquareIcon,
    },

    props:{
        errors: Object,
        alertMessage: Object,
    },

    data() {
        return {
            migration: '',
            validation: '',
        }
    },

    methods: {
        demoBody(){
            let migration = [ "'amount'" ];
            let migration_length = this.form.full_n_float_length;
            if(migration_length){
                migration_length = migration_length.split(",");
                migration = migration.concat(migration_length);
            }
            migration = migration.filter(function (str){
                str = str.replace(/ +(?= )/g,'').trim();
                return str != null && str != '';
            });
            this.migration = migration.join(', ');

            let validation = [ 'required' ];
            validation = validation.concat(this.form.validate);

            let validation_length = this.form.max_n_min_length;
            if(validation_length){
                validation_length = validation_length.split(",");
                validation = validation.concat(validation_length);
            }
            validation = validation.filter(function (str){
                str = str.replace(/ +(?= )/g,'').trim();
                return str != null && str != '';
            });
            validation = validation.map(function (str){
                return '\'' + str + '\'';
            });
            this.validation = validation.join(', ');
        }
    },

    mounted() {
        this.demoBody();
    },

    setup () {
        const breadcrumbs = [
            { name: 'Desks', href: route('generator.desk.index'), current: false },
            { name: 'Pillars', href: route('generator.pillar.index'), current: false },
            { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
            { name: 'Create Page', href: '#', current: false },
        ]

        const form = reactive({
            name: null,
            mimes: null,
            guide: null,
            validate: '',
            max_n_min_length: null,
            full_n_float_length: null,
        })

        function submit() {
            router.post(route('generator.pillar_type.store'), form);
        }

        return {
            breadcrumbs,
            form,
            submit
        }
    }
}
</script>
