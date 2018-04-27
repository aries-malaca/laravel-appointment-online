<template>
    <div class="services">
        <div class="portlet light" v-if="user.is_client !== 1 && (gate(user, 'products', 'view') || gate(user, 'services', 'view'))">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Services & Products </span>
                </div>
                <ul class="nav nav-tabs">
                    <li>
                        <a href="#services" data-toggle="tab">Services</a>
                    </li>
                    <li v-if="gate(user, 'services', 'view')">
                        <a href="#service-types" data-toggle="tab" >Service Types</a>
                    </li>
                    <li v-if="gate(user, 'services', 'view')">
                        <a href="#service-packages" data-toggle="tab">Service Packages</a>
                    </li>
                    <li v-if="gate(user, 'products', 'view')">
                        <a href="#products" data-toggle="tab">Products</a>
                    </li>
                    <li v-if="gate(user, 'products', 'view')">
                        <a href="#product-groups" data-toggle="tab">Product Groups</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <services-list></services-list>
                    <service-types></service-types>
                    <service-packages></service-packages>
                    <products></products>
                    <product-groups></product-groups>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import ServicesList from './services/ServicesList.vue';
    import ServiceTypes from './services/ServiceTypes.vue';
    import ServicePackages from './services/ServicePackages.vue';
    import Products from './services/Products.vue';
    import ProductGroups from './services/ProductGroups.vue';

    export default {
        name: 'Services',
        components:{ ServicesList, ServiceTypes, ServicePackages, Products, ProductGroups, UnauthorizedError },
        mounted:function(){
            this.$store.commit('updateTitle', 'Services');
            this.$store.dispatch('services/fetchServices');
            this.$store.dispatch('services/fetchPackages');
            this.$store.dispatch('services/fetchTypes');
            this.$store.dispatch('products/fetchProducts');
            this.$store.dispatch('products/fetchGroups');
        },
        computed:{
            user(){
                return this.$store.state.user;
            }
        }
    }
</script>