<template>
    <div class="client-dashboard">
        <div class="row widget-row">
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading" v-if="branch_visited.length>1">Last {{ branch_visited.length }} Branch Visited</h4>
                    <h4 class="widget-thumb-heading" v-else>Last Branch Visited</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-green icon-home"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="branch in branch_visited">{{ branch }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading">Most Service Availed</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-red icon-badge"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="service in services_availed">{{ service }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading">Purchased Products</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-purple icon-basket-loaded"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="product in purchased_products">{{ product }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ClientDashboard',
        props:['user','token','configs'],
        components:{},
        data: function(){
            return {
                branch_visited:['Aguirre','Bicutan','Orlando'],
                services_availed:['Brazilian','Biniki','Package 3'],
                purchased_products:['Minty Breeze Feminine Wash','Ethyl Alcohol','Sweet Sakura'],
            }
        },
        methods:{

        }
    }
</script>