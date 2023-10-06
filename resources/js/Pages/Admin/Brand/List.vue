<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import NavLink from '@/Components/NavLink.vue';
import Alert from '@/components/Alert.vue';
import Pagination from '@/components/Pagination.vue';
import {watch, reactive} from 'vue';
import _ from 'lodash';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2'
import axios from 'axios';

const props = defineProps({
    brands: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object
    }
});

const params = reactive({
    search: props.filters.search,
    order_by: props.filters.order_by,
    direction: props.filters.direction,
    brands: []
});

const sort = (field) => {
    params.order_by = field;
    params.direction = params.direction == 'asc' ? 'desc' : 'asc';
}

watch(
    params,
    _.throttle((new_params, old_params) => {
        let p = _.pickBy(new_params)
        router.get(route('admin.brand.index'), p, { replace: true, preserveState: true });
    }, 500)
);

const deleteBrand = (id) => {
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'bg-blue-500 mb-2 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded',
        cancelButton: 'bg-red-500 mb-2 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
    },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel!',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {
        axios.post(`/admin/brands/destroy`, {'id': id}).then((res) => {
            if (res.status) {
                params.brands = props.brands.data.filter(e => e.id != id);
                swalWithBootstrapButtons.fire(
                    'Deleted!',
                    'Brand has been deleted.',
                    'success'
                )
            }
            }).catch((err) => {
                console.log(err);
            });        
    } else if (
        result.dismiss === Swal.DismissReason.cancel
    ) {
        swalWithBootstrapButtons.fire(
        'Cancelled',
        'You cancel the request :)',
        'error'
        )
    }
    })
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Brands
            </h2>
        </template>
        <div class="py-12">            
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <NavLink :href="route('admin.brand.create')" class="bg-blue-500 mb-2 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Brand
                </NavLink>
                <Alert v-if="$page.props.flash.message" :message="$page.props.flash.message" type="success"></Alert>
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div
                        class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center"
                    >
                        <p>Brands</p>
                        <div>
                            <input type="search" v-model="params.search" placeholder="Search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg  mx-2 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ">
                        </div>
                    </div>

                    <div class="relative overflow-x-auto brand-list">
                        <table
                            class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                        >
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                            >
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        <span @click="sort('id')" class="cursor-pointer">ID &duarr;</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <span @click="sort('name')" class="cursor-pointer">Brand Name &duarr;</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="brands.data.length > 0" v-for="brand in brands.data"
                                    key="brand.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                >
                                    <th
                                        scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    >
                                        {{ brand?.id }}
                                    </th>
                                    <td class="px-6 py-4">{{ brand?.name }}</td>
                                    <td class="px-6 py-4">{{ brand.is_active ? "Yes" : "No" }}</td>
                                    <td class="px-6 py-4">
                                        <NavLink
                                            :href="
                                                route(
                                                    'admin.brand.show',
                                                    brand.id
                                                )
                                            "
                                            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800" >Edit</NavLink
                                        >
                                        <button
                                            @click.prevent="deleteBrand(brand.id)"
                                            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800" >Delete</button
                                        >
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="4" class="p-2">
                                        <Alert message="Brand not found" type="success"></Alert>
                                    </td>
                                </tr>
                            </tbody>                            
                        </table>
                        <Pagination :data="brands.meta"></Pagination>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
