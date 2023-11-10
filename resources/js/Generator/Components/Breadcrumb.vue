<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
        <li>
            <div>
                <Link href="/generator" class="text-gray-400 hover:text-gray-500">
                    <HomeIcon class="flex-shrink-0 h-5 w-5" aria-hidden="true" />
                    <span class="sr-only">Home</span>
                </Link>
            </div>
        </li>
        <li v-for="breadcrumb in breadcrumbs" :key="breadcrumb.name">
            <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                </svg>
                <Link :href="breadcrumb.href" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" :aria-current="breadcrumb.current ? 'page' : undefined"> {{ breadcrumb.name }} </Link>
            </div>
        </li>
        </ol>
    </nav>
</template>

<script>
import {
    HomeIcon
} from '@heroicons/vue/24/solid'
import { Link } from '@inertiajs/vue3'

export default {
    props: {
        breadcrumbs: Object,
    },

    components: {
        Link,
        HomeIcon,
    },
}
</script>
