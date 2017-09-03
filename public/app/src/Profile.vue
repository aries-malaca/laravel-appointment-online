<template>
    <div class="row" v-if="user.username !== undefined">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet bordered">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img v-bind:src="'/images/users/' + user.picture" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ user.username }}</div>
                        <div class="profile-usertitle-job" v-if="user.is_client == 0" style="color:#999999"> {{ user.level_name }} </div>
                        <div class="profile-usertitle-job" v-else v-bind:style="'color:#'+ (user.gender=='male'?'5b9bd1':'ed6b75')"> {{ user.branch.branch_name }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR BUTTONS -->
                    <div class="profile-userbuttons">
                        <button type="button" class="btn btn-circle green btn-sm">Update Picture</button>
                        <button type="button" class="btn btn-circle blue btn-sm">Change Password</button>
                    </div>
                    <!-- END SIDEBAR BUTTONS -->
                    <span><br/></span>     
                </div>
                <!-- END PORTLET MAIN -->
                <!-- PORTLET MAIN -->
                <div class="portlet light bordered">
                    <div>
                        <h4 class="profile-desc-title">Address:</h4>
                        <span class="profile-desc-text"> {{ user.address }} </span>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-globe"></i>
                            <a href="http://www.keenthemes.com">www.keenthemes.com</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-twitter"></i>
                            <a href="http://www.twitter.com/keenthemes/">@keenthemes</a>
                        </div>
                        <div class="margin-top-20 profile-desc-link">
                            <i class="fa fa-facebook"></i>
                            <a href="http://www.facebook.com/keenthemes/">keenthemes</a>
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

                                    </div>
                                    <div class="col-md-4">
                                        
                                    </div>
                                    <div class="col-md-4">
                                        
                                    </div>
                                </div>
                                <div class="margiv-top-10" v-if="profile.username !== undefined">
                                    <button @click="updateProfile($event)" data-loading-text="Updating..." class="btn green"> Update Profile </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Profile',
        props: ['user'],
        data: function(){
            return {
                title: 'Profile',
                profile: {},
                token:''
            }
        },
        methods:{
            getProfile:function(){
                var u = this;
                axios.get('/api/user/getUser?token=' + this.token)
                .then(function (response) {
                    u.profile = response.data.user;
                })
                .catch(function (error) {
                    XHRCatcher(error);
                });
            },
            updateProfile:function(event){
                var u = this;
                var $btn = $(event.target);
                $btn.button('loading');

                axios.patch('/api/user/updateProfile?token=' + this.token, this.profile)
                .then(function (response) {
                    u.getProfile();
                    u.$emit('update_user');
                    toastr.success("Profile successfully updated.");
                    $btn.button('reset');
                })
                .catch(function (error) {
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.token = $.cookie("login_cookie");
            this.getProfile();
            this.$emit('update_title', this.title);
        }
    }
</script>