<template>
    <Listbox v-model="selectedItem">
        <div class="relative mt-1">
            <ListboxButton class="w-full text-left rounded border border-gray-300 bg-white py-2 pl-3 pr-10 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 sm:text-sm">
              <span class="block truncate">{{ selectedItem?.name ?? 'Select' }} </span>
              <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
              </span>
            </ListboxButton>

            <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption v-slot="{ active, selected }" v-for="item in items" :key="item.name" :value="item" as="template">
                        <li :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-900', hideIcon ? 'pl-4 pr-4' : '', rightIcon ? 'pl-4 pr-10' : 'pl-10 pr-4', 'relative cursor-default select-none py-2']">
                            <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate',]">{{ item.name }}</span>
                            <span v-if="selected && !hideIcon" :class="[rightIcon ? 'right-0 pr-3' : ' left-0 pl-3', 'absolute inset-y-0 flex items-center text-gray-600']">
                              <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script>
import { computed, ref, watch } from 'vue'
import {
    Listbox,
    ListboxLabel,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import {
    CheckIcon,
    ChevronUpDownIcon
} from '@heroicons/vue/24/solid'

export default {
    components: {
        CheckIcon,
        Listbox,
        ListboxLabel,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        ChevronUpDownIcon,
    },

    emits: ['update:modelValue'],
    props: {
        items: Array,
        modelValue: Object,
        rightIcon: {
            type: Boolean,
            default: true,
        },
        hideIcon: {
            type: Boolean,
            default: false,
        }
    },

    setup(props, { emit }) {
        const selectedItem = ref(props.items.find(item => (props.modelValue == item.id)) ?? '')

		watch(selectedItem, function() {
            emit('update:modelValue', selectedItem.value.id)
        })

        return {
            selectedItem,
        }
    },
}
</script>
