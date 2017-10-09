<template>
    <div class="portlet mt-element-ribbon light portlet-fit bordered" v-if="show">
        <a v-if="with_back" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-danger uppercase">
            Back
        </a>
        <div class="portlet-title tabbable-line" v-if="branch.branch_name !== undefined">
            <div class="caption">
                &nbsp;
                <span class="caption-subject bold font-grey-gallery uppercase"> {{ branch.branch_name }} </span>
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
        <div class="portlet-body profile" v-if="branch.branch_name !== undefined">
            <div class="tab-content">
                <div class="tab-pane active" id="info">
                    <div class="row">
                        <div class="col-md-8 profile-info">
                            <table class="table table-hover table-light">
                                <tbody>
                                    <tr>
                                        <td> Branch Code: </td>
                                        <td> {{ branch.branch_code }} </td>
                                    </tr>
                                    <tr>
                                        <td> Branch Address: </td>
                                        <td> {{ branch.branch_address }} </td>
                                    </tr>
                                    <tr>
                                        <td> City/ Region: </td>
                                        <td> {{ branch.city_name }} / {{ branch.region_name }} </td>
                                    </tr>
                                    <tr>
                                        <td> Classification: </td>
                                        <td> {{ branch.branch_classification }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact No.: </td>
                                        <td> {{ branch.branch_contact }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact Person: </td>
                                        <td> {{ branch.branch_contact_person }} </td>
                                    </tr>
                                    <tr>
                                        <td> Email: </td>
                                        <td> {{ branch.branch_email }} </td>
                                    </tr>
                                    <tr>
                                        <td> Facebook: </td>
                                        <td><a target="_blank" v-bind:href="'http://facebook.com/'+ branch.social_media_accounts[0]"> {{ branch.social_media_accounts[0] }}</a></td>
                                    </tr>
                                    <tr>
                                        <td> Twitter: </td>
                                        <td><a target="_blank" v-bind:href="'http://twitter.com/'+ branch.social_media_accounts[1]"> {{ branch.social_media_accounts[1] }}</a></td>
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
                            <button class="btn btn-info" v-if="with_back" @click="editBranch(branch)">Edit Branch</button>
                            <button class="btn btn-success" @click="addPicture">Add Photo</button>

                            <div class="row">
                                <div class="col-md-4" v-for="(pic,key) in pictures">
                                    <ul class="list-unstyled profile-nav" style="margin-top:5px">
                                        <li>
                                            <img v-bind:src="'images/branches/'+ pic" class="img-responsive pic-bordered" alt="" />
                                            <a @click="showUploadModal(key)" class="profile-edit"> <i class="fa fa-pencil"></i> </a>
                                            <a @click="removePicture(key,pic)" style="margin-top:30px" class="profile-edit"> <i class="fa fa-close"></i> </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="portlet sale-summary">
                                <div class="portlet-title">
                                    <div class="caption font-red sbold"> Transaction Summary </div>
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="map-single"></div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">Directions: {{ branch.directions}} </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-md-4-->
                    </div>
                </div>
                <!--tab_1_2-->
                <div class="tab-pane" id="appointments">
                    <div class="row">
                        <div class="col-md-12">
                            <appointments-table title="Active Appointments" :hide_branch="true" :configs="configs" @get_appointments="getAppointments"
                                                :appointments="active_appointments" :token="token" :user="user"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <appointments-table title="Appointment History" :hide_branch="true" :user="user" @get_appointments="getAppointmentHistory"
                                                :appointments="appointment_history" :token="token" :configs="configs" />
                        </div>
                    </div>
                </div>
                <!--end tab-pane-->
                <div class="tab-pane" id="transactions">

                </div>
                <!--end tab-pane-->

                <schedules :token="token" @refresh_branch="getBranch" :configs="configs" :branch="branch" v-if="branch.id !== undefined"></schedules>

                <div class="tab-pane" id="reviews">

                </div>
                <!--end tab-pane-->
            </div>
        </div>

        <div v-for="(pic, key) in pictures">
            <upload-picture-modal
                    @refresh_host="getBranch"
                    :token="token"
                    category="branch"
                    :param_url="'branch_id='+branch.id+'&key='+key"
                    :placeholder_image="'images/branches/'+branch.branch_pictures[key]"
                    :modal_id="'upload-picture-modal-'+key"
                    :form_id="'upload-user-picture-form-'+key"
                    :input_id="'file-'+key">
            </upload-picture-modal>
        </div>
    </div>
</template>

<script>
    import UploadPictureModal from "../modals/UploadPictureModalSmall.vue";
    import AppointmentsTable from "../tables/AppointmentsTable.vue";
    import Schedules from "./branch/Schedules.vue";

    export default {
        name: 'BranchProfile',
        props: ['token','configs','id','with_back','show','user'],
        components:{ UploadPictureModal, AppointmentsTable, Schedules },
        data: function(){
           return {
               branch:{},
               pictures:[],
               appointment_history:[],
               active_appointments:[],
           }
        },
        methods:{
            back:function(){
                this.$emit('back');
            },
            getBranch:function(){
                let u = this;
                axios.get('/api/branch/getBranch/' + this.id)
                .then(function (response) {
                    if(response.data.id !== undefined){
                        u.branch = response.data;
                        u.branch.branch_pictures = response.data.branch_pictures;
                        u.branch.branch_data = response.data.branch_data;
                        u.pictures = u.branch.branch_pictures;
                        u.branch.map_coordinates = u.branch.map_coordinates;
                        u.branch.social_media_accounts = u.branch.social_media_accounts;

                        setTimeout(function(){
                            let latlng = new google.maps.LatLng(u.branch.map_coordinates.lat,u.branch.map_coordinates.long);
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

                    }
                }).catch(function(error){
                    if(this.with_back)
                        this.back();
                });
            },
            addPicture:function(){
                this.pictures.push('no photo.jpg');
            },
            removePicture(key, name){
                if(name == 'no photo.jpg'){
                    this.pictures.splice(key, 1);
                    return false;
                }
                if(confirm("Are you sure you want to delete this?")){
                    let u = this;
                    axios.patch('/api/branch/removePicture?token=' + this.token, {branch_id:this.id, key: key})
                    .then(function (response) {
                        u.pictures.splice(key, 1);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
                }
            },
            showUploadModal:function (key) {
                console.log(key);
                $("#upload-picture-modal-"+key).modal("show");
                try{
                    $("form")[key].reset();
                }
                catch(error){}
            },
            editBranch:function(){
                this.$emit('edit_branch', this.branch);
            },
            getAppointments:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/branch/'+ this.id +'/active';

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
                var url = '/api/appointment/getAppointments/branch/'+ this.id +'/inactive';

                axios.get(url)
                    .then(function (response) {
                        u.appointment_history = [];
                        response.data.forEach(function(item){
                            u.appointment_history.push(item);
                        });
                    });
            },
            moment:moment
        },
        watch:{
            id:function(){
                if(this.id !== 0){
                    this.getAppointments();
                    this.getAppointmentHistory();
                    this.getBranch();
                }
            }
        },
        mounted:function(){
            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.branch_id === u.id){
                    u.getAppointments();
                    u.getAppointmentHistory();
                }
            };
        }
    }
</script>

<style>
    #map-single{
        margin:5px;
        height:250px;
        width:100%;
    }
</style>