<template>
    <div>
        <div class="portlet mt-element-ribbon light portlet-fit bordered">
            <a v-if="with_back && technician.first_name !== undefined" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                <i class="fa fa-arrow-circle-left"></i> Back
            </a>
            <div class="portlet-title tabbable-line" v-if="technician.first_name !== undefined">
                <div class="caption">
                    &nbsp;
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ technician.first_name }} {{ technician.last_name }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#info" data-toggle="tab">Technician Info</a>
                    </li>
                    <li>
                        <a href="#schedules" data-toggle="tab">Schedules</a>
                    </li>
                    <li>
                        <a href="#reviews" data-toggle="tab">Reviews</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body profile" v-if="technician.first_name !== undefined">
                <div class="tab-content">
                    <div class="tab-pane active" id="info">
                        <div class="row">
                            <div class="col-md-8 profile-info">

                            </div>
                            <div class="col-md-4">
                                <div class="portlet sale-summary">
                                    <div class="portlet-title">
                                        <div class="caption font-red sbold"> Today's Transaction Summary </div>
                                        <div class="tools">
                                            <a class="reload" href="javascript:;"> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <ul class="list-unstyled">
                                            <li>
                                                <span class="sale-info"> PRODUCTS AVAILED </span>
                                                <span class="sale-num"> 2377 </span>
                                            </li>
                                            <li>
                                                <span class="sale-info"> SERVICES AVAILED </span>
                                                <span class="sale-num"> 2377 </span>
                                            </li>
                                            <li>
                                                <span class="sale-info"> TOTAL SALES </span>
                                                <span class="sale-num"> 2377 </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--end col-md-4-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: 'TechnicianProfile',
        props: ['with_back'],
        data: function(){
            return {
            }
        },
        methods:{
            back:function(){
                this.$store.commit('technicians/updateViewingTechnician', false);
            }
        },
        mounted:function(){

        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            technician(){
                return this.$store.state.technicians.viewing_technician;
            }
        }
    }
</script>