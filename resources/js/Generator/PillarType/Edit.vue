<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import Breadcrumb from '../Components/Breadcrumb.vue';

import {
    PlusIcon,
} from '@heroicons/vue/24/solid'

import {
    PencilSquareIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
    pillar_type: Object
})

const migration = ref('')
const validation = ref('')

onMounted(() => {
    demoBody()
})

const form = useForm({
    name: props.pillar_type.name,
    validate: props.pillar_type.validate,
    max_n_min_length: props.pillar_type.max_n_min_length,
    full_n_float_length: props.pillar_type.full_n_float_length,
    mimes: props.pillar_type.mimes,
    guide: props.pillar_type.guide,
})

const breadcrumbs = [
    { name: 'Desks', href: route('generator.desk.index'), current: false },
    { name: 'Pillars', href: route('generator.pillar.index'), current: false },
    { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
    { name: 'Create Page', href: '#', current: false },
]

const demoBody = () => {
    migration.value = [ "'amount'" ];
    let migration_length = form.full_n_float_length;
    if(migration_length){
        migration_length = migration_length.split(",");
        migration.value = migration.value.concat(migration_length);
    }
    migration.value = migration.value.filter(function (str){
        str = str.replace(/ +(?= )/g,'').trim();
        return str != null && str != '';
    });
    migration.value = migration.value.join(', ');

    validation.value = [ 'required', form.validate ];
    let validation_length = form.max_n_min_length;
    if(validation_length){
        validation_length = validation_length.split(",");
        validation.value = validation.value.concat(validation_length);
    }
    validation.value = validation.value.filter(function (str){
        str = str.replace(/ +(?= )/g,'').trim();
        return str != null && str != '';
    });
    validation.value = validation.value.map(function (str){
        return '\'' + str + '\'';
    });
    validation.value = validation.value.join(', ');
}

const submit = () => {
    form.post(route('generator.pillar_type.update', props.pillar_type.id), {
        onFinish: () => {
        }
    });
}
</script>
<template>
    <Head title="Edit Pillar Type"></Head>

    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <Breadcrumb :breadcrumbs="breadcrumbs" />
                </div>
                <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
                    <Link :href="route('generator.pillar_type.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-400">
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
                    <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> Pillar Type Edit</p>

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
                                    <input v-model="form.name" id="name" placeholder="Name" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="validate" class="block text-sm font-medium text-gray-700"> Request Validate <span class="text-red-500">*</span> </label>
                                    <input v-model="form.validate" @keyup="demoBody()" id="validate" placeholder="Request Validate" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="max_n_min_length" class="block text-sm font-medium text-gray-700"> Max n Min Length Validation </label>
                                    <input v-model="form.max_n_min_length" @keyup="demoBody()" id="max_n_min_length" placeholder="max: 255, min: 191" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="full_n_float_length" class="block text-sm font-medium text-gray-700"> Full n Float Length </label>
                                    <input v-model="form.full_n_float_length" @keyup="demoBody()" id="full_n_float_length" placeholder="5, 3 => 32145.154" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6">
                                    <label for="mimes" class="block text-sm font-medium text-gray-700"> Mimes </label>
                                    <input v-model="form.mimes" @keyup="demoBody()" id="mimes" placeholder="image => mimes:jpeg,png,jpg,gif,svg" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
                                </div>

                                <div class="col-span-6">
                                    <label for="guide" class="block text-sm font-medium text-gray-700"> Guide </label>
                                    <textarea v-model="form.guide" id="guide" placeholder="Guide Here..." rows="3" type="text" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-gray-400 focus:border-gray-400 border-gray-300 rounded" />
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
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <PencilSquareIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
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
