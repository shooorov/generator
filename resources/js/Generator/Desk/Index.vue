<script setup>
import { onMounted } from 'vue';
import { router, Head, Link, usePage, useForm } from '@inertiajs/vue3'
import Breadcrumb from '../Components/Breadcrumb.vue';
import { PlusIcon } from '@heroicons/vue/24/solid'
import {
	DocumentDuplicateIcon,
	ArrowTopRightOnSquareIcon,
	PencilSquareIcon,
	CogIcon,
	TrashIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const props = defineProps({
    desks: Object,
})

const deleteRecord = (record_id) => {
    if (confirm("Do you really want to delete this desk?")) {
        router.get(route('generator.desk.destroy', record_id));
    }
}

const breadcrumbs = [
    { name: 'Pillars', href: route('generator.pillar.index'), current: false },
    { name: 'Pillar Types', href: route('generator.pillar_type.index'), current: false },
    { name: 'Desks', href: route('generator.desk.index'), current: false },
    { name: 'List Page', href: '#', current: false },
];
</script>
<template>
	<Head title="Desks"></Head>

	<div class="bg-white shadow">
		<div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
			<div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
				<div class="flex-1 min-w-0">
					<Breadcrumb :breadcrumbs="breadcrumbs" />
				</div>

				<div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4">
					<Link :href="route('generator.desk.create')" class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-400">
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
					<p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium"> Desks</p>

					<div class="flex-shrink-0 flex space-x-3">
						<Link :href="route('generator.all_tables')"
							class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-400">
						<DocumentDuplicateIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
						Generate All Files
						</Link>
					</div>
				</div>

				<table class="table-auto sm:table-fixed min-w-full w-full" id="table">
					<thead>
						<tr>
							<th
								class="w-20 p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">
								S.No </th>
							<th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Name
							</th>
							<th class="p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">
								Directory</th>
							<th class="w-32 p-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">
								Action</th>
						</tr>
					</thead>
					<tbody class="bg-white">
						<tr v-for="(desk, index) in desks.data" :key="desk.id"
							:class="[index % 2 == 0 ? 'bg-white' : 'bg-gray-50', 'border-b']">
							<td class="p-2 text-center"> {{ (index + 1) }} </td>

							<td class="p-2 whitespace-no-wrap">
								<Link :href="route('generator.desk.decorate', desk.id)" class="hover:underline"
									title="decorate">
								{{ desk.name }}
								</Link>
							</td>
							<td class="p-2 whitespace-no-wrap">{{ desk.directory }}</td>

							<td class="p-2 whitespace-no-wrap">
								<div class="flex justify-end">
									<Link :href="route('generator.desk.decorate', desk.id)"
										class="text-gray-600 hover:text-gray-800 ml-3" title="decorate">
									<CogIcon class="w-6 h-6" aria-hidden="true" />
									</Link>
									<Link v-show="desk.route" :href="desk.route?.index"
										class="text-gray-600 hover:text-gray-800 ml-3" :title="'go to ' + desk.name + ' table index'">
									<ArrowTopRightOnSquareIcon class="w-6 h-6" aria-hidden="true" />
									</Link>
									<Link :href="route('generator.desk.edit', desk.id)"
										class="text-indigo-600 hover:text-indigo-800 ml-3" title="edit">
									<PencilSquareIcon class="w-6 h-6" aria-hidden="true" />
									</Link>
									<Link @click="deleteRecord(desk.id)"
										class="text-red-600 hover:text-red-800 ml-3" title="destroy">
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
