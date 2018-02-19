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
                            <div class="col-md-3">
                                <ul class="list-unstyled profile-nav">
                                    <li>
                                        <img v-bind:src="'images/technicians/'+technician.technician_picture" style="border-radius:10px !important;width:180px" class="img-responsive pic-bordered" alt="" />
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12 profile-info" v-if="technician.technician_data !== undefined">
                                        <h1 class="font-green sbold uppercase">{{ technician.first_name }} {{ technician.last_name }}</h1>
                                        <ul class="list-inline">
                                            <li>
                                                <i class="fa fa-map-marker"></i> {{ technician.technician_data.address }}
                                            </li>
                                            <li>
                                                <i class="fa fa-gift"></i> {{ moment(technician.technician_data.birth_date).format("MMMM D, YYYY") }}
                                            </li>
                                            <li>
                                                <i class="fa fa-phone"></i> {{ technician.technician_data.mobile }}
                                            </li>
                                            <li v-if="technician.technician_data.gender == 'female'">
                                                <i class="fa fa-female"></i> Female
                                            </li>
                                            <li v-else>
                                                <i class="fa fa-male"></i> Male
                                            </li>
                                        </ul>

                                        <table class="table table-hover table-light">
                                            <tbody>
                                                <tr>
                                                    <td> Employee ID: </td>
                                                    <td> {{ technician.employee_id }} </td>
                                                </tr>
                                                <tr>
                                                    <td> Position: </td>
                                                    <td> {{ technician.technician_data.position_name }} </td>
                                                </tr>

                                                <tr>
                                                    <td> Cluster:</td>
                                                    <td> {{ technician.cluster_name }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" @click="showEditModal" class="btn green-meadow">Edit Info</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <schedules></schedules>
                    <reviews></reviews>
                </div>
            </div>
        </div>
        <technician-modal v-if="technician" operation="add"></technician-modal>
    </div>
</template>

<script>
    import Reviews from './Reviews.vue';
    import Schedules from './Schedules.vue';
    import TechnicianModal from '../TechnicianModal.vue';

    export default {
        name: 'TechnicianProfile',
        props: ['with_back'],
        components:{ Reviews, Schedules, TechnicianModal },
        data: function(){
            return {
            }
        },
        methods:{
            back:function(){
                this.$store.commit('technicians/updateViewingTechnician', false);
            },
            showEditModal(){
                $("#add-technician-modal").modal("show");
            },
            moment:moment
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