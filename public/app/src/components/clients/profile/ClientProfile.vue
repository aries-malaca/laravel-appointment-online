<template>
    <div>
        <div class="portlet mt-element-ribbon light portlet-fit bordered" v-if="show" v-show="newClient.first_name !== undefined">
            <a v-if="with_back" @click="back" class="ribbon ribbon-left ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                <i class="fa fa-arrow-circle-left"></i> Back
            </a>
            <div class="portlet-title tabbable-line" v-if="client.first_name !== undefined">
                <div class="caption">
                    &nbsp;
                    <span class="caption-subject bold font-grey-gallery uppercase">{{ client.first_name }} {{ client.last_name }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#overview" data-toggle="tab"> Overview </a>
                    </li>
                    <li>
                        <a href="#account" data-toggle="tab"> Account </a>
                    </li>
                    <li>
                        <a href="#appointments" data-toggle="tab"> Appointments &nbsp;
                            <span class="badge badge-success" v-if="active_appointments.length>0"> {{ active_appointments.length }} </span>
                        </a>
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
                                            <img v-bind:src="'images/users/'+client.user_picture" style="border-radius:10px !important;width:180px" class="img-responsive pic-bordered" alt="" />
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
                                                    <i class="fa fa-gift"></i> {{ moment(client.birth_date).format("MMMM D, YYYY") }}
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
                                            </ul>

                                            <table class="table table-hover table-light">
                                                <tbody>
                                                <tr v-if="client.last_login !== null">
                                                    <td> Last Login: </td>
                                                    <td> {{ moment(client.last_login).fromNow() }} </td>
                                                </tr>
                                                <tr v-if="client.last_activity !== null">
                                                    <td> Last Activity: </td>
                                                    <td> {{ moment(client.last_activity).fromNow() }} </td>
                                                </tr>
                                                <tr>
                                                    <td> Home Branch: </td>
                                                    <td> {{ client.home_branch_name }} </td>
                                                </tr>
                                                <tr>
                                                    <td> Premier Client: </td>
                                                    <td v-if="client.user_data !== undefined ">
                                                        <span class="badge badge-success" v-if="client.user_data.premier_status == 1">Yes</span>
                                                        <span class="badge badge-warning" v-else>No</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <!--end col-md-8-->
                                        <div class="col-md-4" v-if="transactions !== false">
                                            <div class="portlet sale-summary">
                                                <div class="portlet-title">
                                                    <div class="caption font-red sbold"> Transaction Summary </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <ul class="list-unstyled">
                                                        <li>
                                                            <span class="sale-info"> PRODUCTS AVAILED </span>
                                                            <span class="sale-num"> {{ products_availed_total }} </span>
                                                        </li>
                                                        <li>
                                                            <span class="sale-info"> SERVICES AVAILED </span>
                                                            <span class="sale-num"> {{ services_availed_total }} </span>
                                                        </li>
                                                        <li>
                                                            <span class="sale-info"> TOTAL SALES </span>
                                                            <span class="sale-num"> {{ products_availed_total+services_availed_total }} </span>
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
                        </div>
                        <!--tab_1_2-->
                        <div class="tab-pane" id="account">
                            <div class="row profile-account">
                                <div class="col-md-3">
                                    <ul class="list-unstyled profile-nav">
                                        <li>
                                            <img v-bind:src="'images/users/'+client.user_picture" style="border-radius:10px !important;width:180px" class="img-responsive pic-bordered" alt="" />
                                            <a data-toggle="modal" @click="showUploadModal" class="profile-edit"> Change </a>
                                        </li>
                                    </ul>
                                    <ul class="ver-inline-menu tabbable margin-bottom-10">
                                        <li class="active">
                                            <a data-toggle="tab" href="#personal-info">
                                                <i class="fa fa-cog"></i> Personal Info </a>
                                            <span class="after"> </span>
                                        </li>
                                        <li>
                                            <a data-toggle="tab" href="#account-settings">
                                                <i class="fa fa-eye"></i> Settings </a>
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
                                                        <input type="text" v-model="newClient.user_address" id="autocomplete2" placeholder="Enter your address"
                                                               @focus="locate" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Mobile</label>
                                                        <input type="text" class="form-control" v-model="newClient.user_mobile">
                                                    </div>
                                                </div>
                                                <div class="col-md-8" v-if="newClient.home_branch !== undefined ">
                                                    <div class="form-group">
                                                        <label class="control-label">Home Branch</label>
                                                        <vue-select v-model="newClient.home_branch" :options="branch_selection"></vue-select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" v-if="newClient.user_data !== undefined">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Notifications</label>
                                                        <div>
                                                            <input type="checkbox" id="_email" value="email" v-model="newClient.user_data.notifications" />
                                                            <label for="_email">Email</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" @click="updateInfo($event)" data-loading-text="Updating..." class="btn green">Save changes</button>
                                        </div>
                                        <div id="account-settings" class="tab-pane">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Change Password</h4>
                                                    <div class="input-group">
                                                        <div class="input-icon">
                                                            <i class="fa fa-lock fa-fw"></i>
                                                            <input class="form-control" type="password" v-model="newClient.password" placeholder="password" />
                                                        </div>
                                                        <span class="input-group-btn">
                                                        <button class="btn btn-success" @click="changePassword($event)" data-loading-text="Updating..." type="button">Save Changes</button>
                                                    </span>
                                                    </div>

                                                </div>
                                            </div>
                                            <my-devices-table @emit_host="getClient" :default_user="newClient"/>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-md-9-->
                            </div>
                        </div>
                        <!--end tab-pane-->
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
                                                <appointments-table :paginate="true" :hide_client="true"
                                                                    @get_appointments="getAppointments" :appointments="active_appointments"/>
                                            </div>
                                            <div id="inactive" class="tab-pane">
                                                <appointments-table :paginate="true" :hide_client="true"
                                                                    @get_appointments="getAppointmentHistory" :appointments="appointment_history"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="transactions">
                            <transactions-view :client="client" :transactions="transactions"></transactions-view>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
            <div v-if="client.id !== undefined">
                <upload-picture-modal
                        @refresh_host="refreshClient"
                        category="user"
                        :param_url="'user_id='+client.id"
                        :placeholder_image="'images/users/'+client.user_picture"
                        modal_id="upload-picture-modal"
                        form_id="upload-user-picture-form"
                        input_id="file"
                        croppie_id="client_croppie">
                </upload-picture-modal>
            </div>
        </div>
        <appointment-modal @refresh_list="refreshList"></appointment-modal>

        <loading v-if="newClient.first_name===undefined && show"></loading>
    </div>
</template>

<script>
    import UploadPictureModal from "../../uploader/UploadPictureModal.vue";
    import AppointmentsTable from "../../appointment/AppointmentsTable.vue";
    import AppointmentModal from "../../appointment/AppointmentModal.vue";
    import MyDevicesTable from "../../tables/MyDevicesTable.vue";
    import TransactionsView from "../../transactions/TransactionsView.vue";
    import VueSelect from "vue-select"
    import Loading from '../../etc/Loading.vue';
    export default {
        name: 'ClientProfile',
        props: ['with_back','id','show'],
        components:{ UploadPictureModal, AppointmentsTable, VueSelect, MyDevicesTable, TransactionsView, Loading, AppointmentModal },
        data: function(){
            return {
                client:{},
                newClient:{},
                active_appointments:[],
                appointment_history:[],
                transactions:[]
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
            getClient:function(){
                let u = this;
                axios.get('/api/client/getClient/' + this.id)
                    .then(function (response) {
                        if(response.data.id !== undefined){
                            u.client = response.data;
                            u.newClient = {
                                id:response.data.id,
                                first_name:response.data.first_name,
                                middle_name:response.data.middle_name,
                                last_name:response.data.last_name,
                                birth_date:moment(response.data.birth_date).format("YYYY-MM-DD"),
                                user_address: response.data.user_address,
                                user_mobile: response.data.user_mobile,
                                home_branch:response.data.home_branch,
                                password:'',
                                device_data:response.data.device_data,
                                user_data:response.data.user_data,
                            };

                            if(u.newClient.user_data.notifications === undefined)
                                u.newClient.user_data.notifications = [];

                            u.getBossTransactions();
                        }
                    });
            },
            refreshClient:function(){
                this.$emit('refresh_client');
                this.getClient();
            },
            refreshList(){
                this.getAppointments();
                this.getAppointmentHistory();
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
                    axios.post('/api/client/updateInfo?token=' + u.token, u.newClient)
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
                let u = this;
                let $btn = $(event.target);

                SweetConfirmation("Are you sure you want to change client's password?", function(){
                    $btn.button('loading');
                    axios.post('/api/client/changePassword?token=' + u.token, u.newClient)
                        .then(function () {
                            u.getClient();
                            toastr.success("Client's password successfully updated.");
                            $btn.button('reset');
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                            $btn.button('reset');
                        });
                });
            },
            locate:function(){
                let u = this;
                geolocate();
                initAutocomplete();
                $("#autocomplete2").change(function(event){
                    setTimeout(function(){
                        console.log(event);
                        u.newClient.user_address = event.target.value;
                    },100);
                })
            },
            getAppointments:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/client/'+ this.id +'/active';

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
                var url = '/api/appointment/getAppointments/client/'+ this.id +'/inactive';

                axios.get(url)
                    .then(function (response) {
                        u.appointment_history = [];
                        response.data.forEach(function(item){
                            u.appointment_history.push(item);
                        });
                    });
            },
            getBossTransactions:function(){
                let u = this;
                if(this.configs.FETCH_BOSS_TRANSACTIONS === undefined && this.client.is_client === 1)
                    return false;

                this.transactions = false;

                axios.get(this.configs.FETCH_BOSS_TRANSACTIONS + "" + this.client.email)
                    .then(function (response) {
                        u.transactions = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        watch:{
            id:function(){
                if(this.id !== 0){
                    this.newClient = {};
                    this.getClient();
                    this.getAppointments();
                    this.getAppointmentHistory();
                }
            }
        },
        mounted:function(){
            if(this.id !== 0){
                this.newClient = {};
                this.getClient();
                this.getAppointments();
                this.getAppointmentHistory();
            }

            let u = this;
            this.$options.sockets.refreshAppointments = function(data){
                if(data.client_id === u.id){
                    u.getAppointments();
                    u.getAppointmentHistory();
                }
            };
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            },
            services_availed_total:function(){
                return 500;
            },
            products_availed_total:function(){
                return 500;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            branches(){
                return this.$store.getters['branches/activeBranches'];
            }
        }
    }
</script>