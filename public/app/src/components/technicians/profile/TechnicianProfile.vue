<template>
    <div>
        <div class="portlet mt-element-ribbon light portlet-fit bordered">
            <a v-if="with_back && technician.first_name !== undefined" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                <i class="fa fa-arrow-circle-left"></i> Back
            </a>
            <div class="portlet-title tabbable-line" v-if="newTechnician.first_name !== undefined">
                <div class="caption">
                    &nbsp;
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ newTechnician.first_name }} {{ newTechnician.last_name }} </span>
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
            <div class="portlet-body profile" v-if="newTechnician.first_name !== undefined">
                <div class="tab-content">
                    <div class="tab-pane active" id="info">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="list-unstyled profile-nav">
                                    <li>
                                        <img v-bind:src="'images/technicians/'+newTechnician.technician_picture" style="border-radius:10px !important;width:180px" class="img-responsive pic-bordered" alt="" />
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12 profile-info" v-if="newTechnician.technician_data !== undefined">
                                        <h1 class="font-green sbold uppercase">{{ newTechnician.first_name }} {{ newTechnician.last_name }}</h1>
                                        <ul class="list-inline">
                                            <li>
                                                <i class="fa fa-map-marker"></i> {{ newTechnician.technician_data.address }}
                                            </li>
                                            <li>
                                                <i class="fa fa-gift"></i> {{ moment(newTechnician.technician_data.birth_date).format("MMMM D, YYYY") }}
                                            </li>
                                            <li>
                                                <i class="fa fa-phone"></i> {{ newTechnician.technician_data.mobile }}
                                            </li>
                                            <li v-if="newTechnician.technician_data.gender == 'female'">
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
                                                    <td> {{ newTechnician.employee_id }} </td>
                                                </tr>
                                                <tr>
                                                    <td> Position: </td>
                                                    <td> {{ newTechnician.technician_data.position_name }} </td>
                                                </tr>

                                                <tr>
                                                    <td> Cluster:</td>
                                                    <td> {{ newTechnician.cluster_name }} </td>
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
            <loading v-else></loading>
        </div>
        <technician-modal v-if="technician" operation="add"></technician-modal>
    </div>
</template>

<script>
    import Reviews from './Reviews.vue';
    import Schedules from './Schedules.vue';
    import TechnicianModal from '../TechnicianModal.vue';
    import Loading from '../../etc/Loading.vue';

    export default {
        name: 'TechnicianProfile',

        props: ['with_back'],
        components:{ Reviews, Schedules, TechnicianModal, Loading },
        data: function(){
            return {
                newTechnician:{}
            }
        },
        methods:{
            getTechnician:function(){
                let u = this;

                axios.get('/api/technician/getTechnician/' + this.technician.id)
                    .then(function (response) {
                        if(response.data.id !== undefined)
                            u.newTechnician = response.data;
                    });
            },
            back:function(){
                this.$store.commit('technicians/updateViewingTechnician', false);
            },
            showEditModal(){
                $("#add-technician-modal").modal("show");
            },
            moment:moment
        },
        watch:{
            'technician.id'(){
                this.getTechnician();
            }
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
        },
        mounted(){
            this.getTechnician();
        }
    }
</script>