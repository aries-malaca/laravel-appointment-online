<template>
    <div>
        <div class="row" v-if="user.username !== undefined">
            <div class="col-md-12">
                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar">
                    <!-- PORTLET MAIN -->
                    <div class="portlet light profile-sidebar-portlet bordered">
                        <!-- SIDEBAR USERPIC -->
                        <div class="profile-userpic">
                            <img v-bind:src="'/images/users/' + user.user_picture" style="border-radius:10px !important;width:150px" class="img-responsive" alt=""> </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name"> {{ user.username }}</div>
                            <div class="profile-usertitle-job" v-if="user.is_client == 0" style="color:#999999"> {{ user.level_name }} </div>
                            <div class="profile-usertitle-job" v-else v-bind:style="'color:#'+ (user.gender=='male'?'5b9bd1':'ed6b75')"> {{ user.branch.label }} </div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- SIDEBAR BUTTONS -->
                        <div class="profile-userbuttons">
                            <button type="button" @click="showUploadModal" class="btn btn-circle green btn-sm">Update Picture</button>
                            <button type="button" data-toggle="modal" href="#change-password" class="btn btn-circle blue btn-sm">Change Password</button>
                        </div>
                        <!-- END SIDEBAR BUTTONS -->
                        <span><br/></span>
                    </div>
                    <!-- END PORTLET MAIN -->
                    <!-- PORTLET MAIN -->
                    <div class="portlet light bordered">
                        <div>
                            <h4 class="profile-desc-title">Address:</h4>
                            <span class="profile-desc-text"> {{ user.user_address }} </span>
                            <div class="margin-top-10 profile-desc-link">
                                <i class="fa fa-gift"></i>
                                <span>{{ moment(user.birth_date).format("MMMM D, YYYY") }}</span>
                            </div>
                            <div class="margin-top-10 profile-desc-link">
                                <i class="fa fa-phone"></i>
                                <span>{{ user.user_mobile }}</span>
                            </div>
                            <div class="margin-top-10 profile-desc-link">
                                <i class="fa fa-envelope"></i>
                                <span>{{ user.email }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END PORTLET MAIN -->
                </div>
                <!-- END BEGIN PROFILE SIDEBAR -->
                <!-- BEGIN PROFILE CONTENT -->
                <div class="profile-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light bordered">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption caption-md">
                                        <i class="icon-globe theme-font hide"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">Profile Account</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">First Name</label>
                                                <input type="text" v-model="profile.first_name" placeholder="Ex: Diane" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Middle Name</label>
                                                <input type="text" v-model="profile.middle_name" placeholder="" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Last Name</label>
                                                <input type="text" v-model="profile.last_name" placeholder="Ex: Garcia" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Mobile</label>
                                                <input type="text" v-model="profile.user_mobile" placeholder="Ex: 0909090900" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="control-label">Address</label>
                                                <input type="text" v-model="profile.user_address" id="autocomplete" placeholder="Enter your address"
                                                       onFocus="geolocate()" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" v-if="profile.branch!==undefined">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Home Branch</label>
                                                <vue-select v-model="profile.branch" :options="branch_selection"></vue-select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" v-if="profile.user_data !== undefined">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Notifications</label>
                                                <div>
                                                    <input type="checkbox" id="_email" value="email" v-model="profile.user_data.notifications" />
                                                    <label for="_email">Email</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <my-devices-table/>

                                    <div class="margin-top-10" v-if="profile.username !== undefined">
                                        <button type="button" @click="updateProfile($event)" data-loading-text="Updating..." class="btn green"> Update Profile </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PROFILE CONTENT -->
            </div>
        </div>

        <div class="modal fade" id="change-password" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Change Password</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            Note: Your password must be alphanumeric.
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Old Password</label>
                                    <input type="password" class="form-control" v-model="change_password.old_password" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">New Password</label>
                                    <input type="password" class="form-control" v-model="change_password.new_password" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Re-enter New Password</label>
                                    <input type="password" class="form-control" v-model="change_password.verify_password" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" @click="changePassword($event)" data-loading-text="Saving..." class="btn green">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <upload-picture-modal v-if="user.username !== undefined"
            @refresh_host="refreshProfile"
            category="user"
            :param_url="'user_id='+user.id"
            :placeholder_image="'images/users/'+user.user_picture"
            modal_id="upload-picture-modal"
            form_id="upload-user-picture-form"
            input_id="file">
        </upload-picture-modal>
    </div>
</template>

<script>
    import VueSelect from "vue-select"
    import UploadPictureModal from "./uploader/UploadPictureModal.vue"
    import MyDevicesTable from "./tables/MyDevicesTable.vue"

    export default {
        name: 'Profile',
        components:{ VueSelect, UploadPictureModal, MyDevicesTable },
        data: function(){
            return {
                title: 'Profile',
                profile: {},
                user_levels:[],
                change_password:{
                    old_password:'',
                    new_password:'',
                    verify_password:''
                },
            }
        },
        methods:{
            getProfile:function(){
                this.profile = Object.assign({}, this.user);
                if(this.profile.user_data.notifications === undefined)
                    this.profile.user_data.notifications = [];
            },
            refreshProfile:function(){
                this.$store.dispatch('fetchAuthenticatedUser');
            },
            getUserLevels:function(){
                let u = this;
                axios.get('/api/user/getUserLevels')
                .then(function (response) {
                    u.user_levels = response.data;
                })
                .catch(function (error) {
                    XHRCatcher(error);
                });
            },
            updateProfile:function(event){
                let u = this;
                let $btn = $(event.target);

                SweetConfirmation("Are you sure you want to update?", function(){
                    $btn.button('loading');
                    axios.post('/api/user/updateProfile?token=' + u.token, u.profile)
                        .then(function () {
                            u.$store.dispatch('fetchAuthenticatedUser');
                            toastr.success("Profile successfully updated.");
                            $btn.button('reset');
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                            $btn.button('reset');
                        });
                });
            },
            changePassword:function (event) {
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                axios.post('/api/user/changePassword?token=' + this.token, this.change_password)
                .then(function () {
                    u.getProfile();
                    toastr.success("Password successfully changed.");
                    $btn.button('reset');
                    u.change_password = { old_password:'', new_password:'', verify_password:''};
                    $('#change-password').modal('hide');
                })
                .catch(function (error) {
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            showUploadModal:function () {
                $("#upload-picture-modal").modal("show");
                try{
                    $("form")[0].reset();
                }
                catch(error){}
            },
            moment:moment,
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'My Profile');
            this.getUserLevels();
            let u = this;

            this.$store.dispatch('fetchAuthenticatedUser').then(function(){
                u.getProfile();
            });

            $("#autocomplete").change(function(event){
                setTimeout(function(){
                    u.profile.user_address = event.target.value;
                },100);
            });

            //outsourced function from map
            initAutocomplete();
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            branches(){
                return this.$store.getters['branches/activeBranches'];
            }
        }
    }
</script>