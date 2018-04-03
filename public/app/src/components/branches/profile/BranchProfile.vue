<template>
    <div>
        <div class="portlet mt-element-ribbon light portlet-fit bordered">
            <a v-if="with_back && newBranch.branch_name !== undefined" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                <i class="fa fa-arrow-circle-left"></i> Back
            </a>
            <div class="portlet-title tabbable-line" v-if="newBranch.branch_name !== undefined">
                <div class="caption">
                    &nbsp;
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ newBranch.branch_name }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#info" data-toggle="tab">Branch Info</a>
                    </li>
                    <li>
                        <a href="#appointments" data-toggle="tab">Appointments &nbsp;
                            <span class="badge badge-success" v-if="active_appointments.length>0"> {{ active_appointments.length }} </span>
                        </a>
                    </li>
                    <li>
                        <a href="#transactions" data-toggle="tab">Transactions</a>
                    </li>
                    <li>
                        <a href="#schedules" data-toggle="tab">Schedules</a>
                    </li>
                    <li>
                        <a href="#reviews" data-toggle="tab">Reviews</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body profile" v-if="newBranch.branch_name !== undefined">
                <div class="tab-content">
                    <div class="tab-pane active" id="info">
                        <div class="row">
                            <div class="col-md-8 profile-info">
                                <table class="table table-hover table-light">
                                    <tbody>
                                    <tr>
                                        <td> Branch Code: </td>
                                        <td> {{ newBranch.branch_code }} </td>
                                    </tr>
                                    <tr>
                                        <td> Branch Address: </td>
                                        <td> {{ newBranch.branch_address }} </td>
                                    </tr>
                                    <tr>
                                        <td> City/ Region: </td>
                                        <td> {{ newBranch.city_name }} / {{ newBranch.region_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Classification: </td>
                                        <td> {{ newBranch.branch_classification }} </td>
                                    </tr>
                                    <tr>
                                        <td> Cluster: </td>
                                        <td> {{ newBranch.cluster_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact No.: </td>
                                        <td> {{ newBranch.branch_contact }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact Person: </td>
                                        <td> {{ newBranch.branch_contact_person }} </td>
                                    </tr>
                                    <tr>
                                        <td> Email: </td>
                                        <td> {{ newBranch.branch_email }} </td>
                                    </tr>
                                    <tr>
                                        <td> Facebook: </td>
                                        <td><a target="_blank" v-bind:href="'http://facebook.com/'+ newBranch.social_media_accounts[0]"> {{ newBranch.social_media_accounts[0] }}</a></td>
                                    </tr>
                                    <tr>
                                        <td> Twitter: </td>
                                        <td><a target="_blank" v-bind:href="'http://twitter.com/'+ newBranch.social_media_accounts[1]"> {{ newBranch.social_media_accounts[1] }}</a></td>
                                    </tr>
                                    <tr>
                                        <td> Rooms: </td>
                                        <td> {{ branch.rooms_count }}</td>
                                    </tr>
                                    <tr>
                                        <td> Welcome Message: </td>
                                        <td> {{ branch.welcome_message }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!--end row-->

                                <div v-if="with_back">
                                    <button class="btn btn-info" @click="editBranch(branch)">Edit Branch</button>
                                    <button class="btn btn-success" @click="addPicture">Add Photo</button>
                                </div>

                                <div class="row">
                                    <div class="col-md-4" v-for="(pic,key) in pictures">
                                        <ul class="list-unstyled profile-nav" style="margin-top:5px">
                                            <li>
                                                <img v-bind:src="'images/branches/'+ pic" class="img-responsive pic-bordered" alt="" />
                                                <div v-if="with_back">
                                                    <a @click="showUploadModal(key)" class="profile-edit"> <i class="fa fa-pencil"></i> </a>
                                                    <a @click="removePicture(key,pic)" style="margin-top:30px" class="profile-edit"> <i class="fa fa-close"></i> </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div v-if="!isNaN(averageRating)">
                                    <h4>Branch Rating</h4>
                                    <star-rating :item-size="25"
                                                 inactive-color="#e4eadb"
                                                 active-color="#92c740"
                                                 :read-only="true"
                                                 :increment="0.1"
                                                 text-class="starer"
                                                 v-model="averageRating"/>
                                </div>
                                <h4>Map</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="map-single"></div>
                                    </div>
                                </div>
                                <div class="alert alert-info">Directions: {{ newBranch.directions}} </div>
                                <div v-if="technicians.length>0">
                                    <b>Technicians: </b>
                                    <ul>
                                        <li v-for="technician in technicians">{{ technician.name }} </li>
                                    </ul>
                                </div>
                                <h4></h4>
                            </div>
                            <!--end col-md-4-->
                        </div>
                    </div>
                    <!--tab_1_2-->
                    <div class="tab-pane" id="appointments">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#active" data-toggle="tab">Active Appointments</a>
                                        </li>
                                        <li>
                                            <a href="#inactive" data-toggle="tab">Appointment History</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="active">
                                            <appointments-table :hide_branch="true" @get_appointments="getAppointments"
                                                                :paginate="true" :appointments="active_appointments"/>
                                        </div>
                                        <div id="inactive" class="tab-pane">
                                            <appointments-table :hide_branch="true" @get_appointments="getAppointmentHistory"
                                                                :paginate="true" :appointments="appointment_history"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="transactions"></div>
                    <!--end tab-pane-->
                    <schedules @refresh_branch="refreshBranch" :branch="branch" v-if="branch.id !== undefined"></schedules>
                    <reviews></reviews>
                    <!--end tab-pane-->
                </div>
            </div>
            <loading v-else></loading>

            <div v-for="(pic, key) in pictures">
                <upload-picture-modal
                        @refresh_host="refreshBranch"
                        category="branch"
                        :param_url="'branch_id='+newBranch.id+'&key='+key"
                        :placeholder_image="'images/branches/'+newBranch.branch_pictures[key]"
                        :modal_id="'upload-picture-modal-'+key"
                        :form_id="'upload-user-picture-form-'+key"
                        :input_id="'file-'+key">
                </upload-picture-modal>
            </div>
        </div>
        <branch-modal operation="edit" @refreshBranch="getBranch"></branch-modal>
        <appointment-modal @refresh_list="refreshList"></appointment-modal>
    </div>

</template>

<script>
    import UploadPictureModal from "../../uploader/UploadPictureModalSmall.vue";
    import AppointmentModal from "../../appointment/AppointmentModal.vue";
    import AppointmentsTable from "../../appointment/AppointmentsTable.vue";
    import Schedules from "./Schedules.vue";
    import Reviews from "./Reviews.vue";
    import BranchModal from "../BranchModal.vue";
    import Loading from '../../etc/Loading.vue';
    import { StarRating } from 'vue-rate-it';

    export default {
        name: 'BranchProfile',
        props:['with_back'],
        components:{ UploadPictureModal, Loading, AppointmentsTable, Schedules, Reviews, BranchModal, AppointmentModal, StarRating },
        data: function(){
           return {
               pictures:[],
               appointment_history:[],
               active_appointments:[],
               newBranch:{},
               technicians:[]
           }
        },
        methods:{
            back:function(){
                this.$store.commit('branches/updateViewingBranch', false);
            },
            refreshBranch:function(){
                this.$socket.emit('refreshModel', 'branches');
                this.getBranch();
            },
            refreshList(){
                this.getAppointments();
                this.getAppointmentHistory();
            },
            getBranch:function(){
                let u = this;

                axios.get('/api/branch/getBranch/' + this.branch.id)
                    .then(function (response) {
                        if(response.data.id !== undefined){
                            u.newBranch = response.data;
                            u.newBranch.branch_pictures = response.data.branch_pictures;
                            u.newBranch.branch_data = response.data.branch_data;
                            u.pictures = u.newBranch.branch_pictures;

                            setTimeout(function(){
                                let latlng = new google.maps.LatLng(u.newBranch.map_coordinates.lat,u.newBranch.map_coordinates.long);
                                let map = new google.maps.Map(document.getElementById("map-single"), {
                                    center: latlng,
                                    zoom: 16,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                });
                                let marker = new google.maps.Marker({
                                    position: latlng,
                                    draggable: false,
                                });
                                marker.setMap(map);
                            },1000);
                            u.$store.commit('branches/updateViewingBranch', u.newBranch);
                        }
                    });
            },
            getTechnicians:function(){
                let u = this;
                axios.get('/api/technician/getBranchTechnicians/'+this.branch.id + '/' + moment().format("YYYY-MM-DD"))
                    .then(function (response) {
                        u.technicians = response.data;
                    });
            },
            addPicture:function(){
                this.pictures.push('no photo.jpg');
            },
            removePicture(key, name){
                if(name === 'no photo.jpg'){
                    this.pictures.splice(key, 1);
                    return false;
                }
                if(confirm("Are you sure you want to delete this?")){
                    let u = this;
                    axios.post('/api/branch/removePicture?token=' + this.token, {branch_id:this.branch.id, key: key})
                    .then(function () {
                        u.pictures.splice(key, 1);
                        u.refreshBranch();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
                }
            },
            showUploadModal:function (key) {
                $("#upload-picture-modal-"+key).modal("show");
                try{
                    $("form")[key].reset();
                }
                catch(error){}
            },
            editBranch:function(){
                this.$store.commit('branches/updateEditingBranch', this.branch);

                $("#add-branch-modal").modal("show");
                let u = this;

                setTimeout(function(){
                    initMap(u.branch.map_coordinates.lat, u.branch.map_coordinates.long);
                },1000);

            },
            getAppointments:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/branch/'+ this.branch.id +'/active';

                axios.get(url)
                    .then(function (response) {
                        u.active_appointments = [];
                        response.data.forEach(function(item){
                            u.active_appointments.push(item);
                        });
                    });
            },
            getAppointmentHistory:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/branch/'+ this.branch.id +'/inactive';

                axios.get(url)
                    .then(function (response) {
                        u.appointment_history = [];
                        response.data.forEach(function(item){
                            u.appointment_history.push(item);
                        });
                    });
            }
        },
        watch:{
            'branch.id'(){
                if(this.branch)
                    if(this.branch.id !== 0){
                        this.getAppointments();
                        this.getAppointmentHistory();
                        this.getBranch();
                    }
            }
        },
        mounted:function(){
            let u = this;

            if(this.branch.id !== 0){
                this.getAppointments();
                this.getAppointmentHistory();
                this.getBranch();
                this.getTechnicians();
            }

            this.$options.sockets.refreshAppointments = function(data){
                if(data.branch_id === u.id){
                    u.getAppointments();
                    u.getAppointmentHistory();
                }
            };
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            branch(){
                return this.$store.state.branches.viewing_branch;
            },
            averageRating(){
                return this.$store.getters['branches/averageRating'];
            }
        }
    }
</script>

<style>
    #map-single{
        margin:5px;
        height:200px;
        width:100%;
    }
    .starer{
        color: #92c740;
        font-size: 32px;
    }
</style>