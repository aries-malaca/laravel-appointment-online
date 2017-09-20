<template>
    <div class="portlet mt-element-ribbon light portlet-fit bordered" v-if="show">
        <a v-if="with_back" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-danger uppercase">
            Back
        </a>
        <div class="portlet-title tabbable-line" v-if="client.first_name !== undefined">
            <div class="caption">
                &nbsp;
                <span class="caption-subject bold font-grey-gallery uppercase"> {{ client.first_name }} {{ client.last_name }} </span>
            </div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#overview" data-toggle="tab"> Overview </a>
                </li>
                <li>
                    <a href="#account" data-toggle="tab"> Account </a>
                </li>
                <li>
                    <a href="#appointments" data-toggle="tab"> Appointments </a>
                </li>
                <li>
                    <a href="#transactions" data-toggle="tab"> Transactions </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body" v-if="client.first_name !== undefined">
            <div class="profile">
                <div class="tab-content">
                    <div class="tab-pane active" id="overview">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="list-unstyled profile-nav">
                                    <li>
                                        <img v-bind:src="'images/users/'+client.user_picture" style="border-radius:10px !important;width:150px" class="img-responsive pic-bordered" alt="" />
                                        <a data-toggle="modal" @click="showUploadModal" class="profile-edit"> Change </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8 profile-info">
                                        <h1 class="font-green sbold uppercase">{{ client.first_name }} {{ client.last_name }}</h1>
                                        <ul class="list-inline">
                                            <li>
                                                <i class="fa fa-map-marker"></i> {{ client.user_address }}
                                            </li>
                                            <li>
                                                <i class="fa fa-gift"></i> {{ moment(client.birth_date,"MMMM D, YYYY") }}
                                            </li>
                                            <li>
                                                <i class="fa fa-phone"></i> {{ client.user_mobile }}
                                            </li>
                                            <li>
                                                <i class="fa fa-envelope"></i> {{ client.email }}
                                            </li>
                                            <li v-if="client.gender == 'female'">
                                                <i class="fa fa-female"></i> Female
                                            </li>
                                            <li v-else>
                                                <i class="fa fa-male"></i> Male
                                            </li>
                                            <li v-if="client.is_premiere == 1">
                                                <i class="fa fa-credit-card"></i> Premiere Client
                                            </li>
                                        </ul>

                                        <table class="table table-hover table-light">
                                            <tbody>
                                                <tr>
                                                    <td> Home Branch: </td>
                                                    <td> {{ client.home_branch_name }} </td>
                                                </tr>
                                                <tr>
                                                    <td> Is Premier: </td>
                                                    <td v-if="client.user_data !== undefined ">
                                                        <span class="badge badge-success" v-if="client.user_data.premier_status == 1">Yes</span>
                                                        <span class="badge badge-warning" v-else>No</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <!--end col-md-8-->
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
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-md-4-->
                                </div>
                                <!--end row-->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <active-appointments-table :active_appointments="[]" :token="token" :user_id="id"
                                                           :configs="configs" />
                            </div>
                        </div>
                    </div>
                    <!--tab_1_2-->
                    <div class="tab-pane" id="account">
                        <div class="row profile-account">
                            <div class="col-md-3">
                                <ul class="ver-inline-menu tabbable margin-bottom-10">
                                    <li class="active">
                                        <a data-toggle="tab" href="#personal-info">
                                            <i class="fa fa-cog"></i> Personal Info </a>
                                        <span class="after"> </span>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#account-settings">
                                            <i class="fa fa-eye"></i> Account Settings </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content">
                                    <div id="personal-info" class="tab-pane active">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">First Name</label>
                                                    <input type="text" placeholder="Diane" v-model="newClient.first_name" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Middle Name</label>
                                                    <input type="text" v-model="newClient.middle_name" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name</label>
                                                    <input type="text" placeholder="Garcia" v-model="newClient.last_name" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Birth Date</label>
                                                    <input type="date" class="form-control" v-model="newClient.birth_date"/>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label class="control-label">Address</label>
                                                    <textarea class="form-control" v-model="newClient.user_address"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile</label>
                                                    <input type="text" class="form-control" v-model="newClient.user_mobile">
                                                </div>
                                            </div>
                                            <div class="col-md-6" v-if="newClient.home_branch !== undefined ">
                                                <div class="form-group">
                                                    <label class="control-label">Home Branch</label>
                                                    <vue-select v-model="newClient.home_branch" :options="branch_selection"></vue-select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="updateInfo($event)" data-loading-text="Updating..." class="btn green">Save changes</button>
                                    </div>
                                    <div id="account-settings" class="tab-pane">
                                        <h3>Change Password</h3>
                                        <div class="form-group">
                                            <label class="control-label">New Password</label>
                                            <input type="password" class="form-control" v-model="newClient.password" />
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!--end col-md-9-->
                        </div>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="appointments">

                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="transactions">

                    </div>
                    <!--end tab-pane-->
                </div>
            </div>
        </div>

        <div v-if="client.id !== undefined">
            <upload-picture-modal
                    @refresh_host="refreshClient"
                    :token="token"
                    category="user"
                    :param_url="'user_id='+client.id"
                    :placeholder_image="'images/users/'+client.user_picture"
                    modal_id="upload-picture-modal"
                    form_id="upload-user-picture-form"
                    input_id="file">
            </upload-picture-modal>
        </div>
    </div>
</template>

<script>
    import UploadPictureModal from "../modals/UploadPictureModal.vue";
    import ActiveAppointmentsTable from "../tables/ActiveAppointmentsTable.vue";
    import VueSelect from "vue-select"

    export default {
        name: 'ClientProfile',
        props: ['token','configs','with_back','id','show'],
        components:{ UploadPictureModal, ActiveAppointmentsTable, VueSelect },
        data: function(){
            return {
                client:{},
                newClient:{},
                branches:[]
            }
        },
        methods:{
            back:function(){
                this.$emit('back');
            },
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
                    });
            },
            getBranches:function(){
                this.getData('/api/branch/getBranches/active', 'branches');
            },
            getClient:function(){
                let u = this;
                axios.get('/api/client/getClient/' + this.id)
                    .then(function (response) {
                        u.client = response.data;
                        u.newClient = {
                            id:response.data.id,
                            first_name:response.data.first_name,
                            middle_name:response.data.middle_name,
                            last_name:response.data.last_name,
                            birth_date:u.moment(response.data.birth_date, "YYYY-MM-DD"),
                            user_address: response.data.user_address,
                            user_mobile: response.data.user_mobile,
                            home_branch:response.data.home_branch,
                            password:''
                        };
                    });
            },
            refreshClient:function(){
                this.$emit('refresh_client');
                this.getClient();
            },
            moment:function (string, format) {
                return moment(string).format(format);
            },
            showUploadModal:function () {
                $("#upload-picture-modal").modal("show");
                try{
                    $("form")[0].reset();
                }
                catch(error){}
            },
            updateInfo:function(event){
                let u = this;
                let $btn = $(event.target);

                SweetConfirmation("Are you sure you want to update?", function(){
                    $btn.button('loading');
                    axios.patch('/api/client/updateInfo?token=' + u.token, u.newClient)
                        .then(function () {
                            u.getClient();
                            toastr.success("Client Info successfully updated.");
                            $btn.button('reset');
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                            $btn.button('reset');
                        });
                });
            },
            changePassword:function(){

            }
        },
        watch:{
            id:function(){
                this.getClient();
            }
        },
        mounted:function(){
            this.getBranches();
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            }
        }
    }
</script>