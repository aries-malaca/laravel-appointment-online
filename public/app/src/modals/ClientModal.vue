<template>
    <div>
        <div class="modal fade" id="client-modal" tabindex="-1">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">{{ client.first_name }} {{ client.last_name }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="profile">
                            <div class="tabbable-line tabbable-full-width">
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
                                <div class="tab-content">
                                    <div class="tab-pane active" id="overview">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <ul class="list-unstyled profile-nav">
                                                    <li>
                                                        <img v-bind:src="'images/users/'+client.user_picture" class="img-responsive pic-bordered" alt="" />
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
                                                        <a data-toggle="tab" href="#change-password">
                                                            <i class="fa fa-lock"></i> Change Password </a>
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


                                                    </div>
                                                    <div id="change-password" class="tab-pane">

                                                    </div>
                                                    <div id="account-settings" class="tab-pane">

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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <upload-picture-modal :user="client" @update_user="refreshClient" :token="token"></upload-picture-modal>
    </div>
</template>

<script>
    import UploadPictureModal from "./UploadPictureModal.vue";

    export default {
        name: 'ClientModal',
        props: ['client','token'],
        components:{ UploadPictureModal },
        data: function(){
            return {
            }
        },
        methods:{
            refreshClient:function(){
                this.$emit('refresh_client', this.client);
            },
            moment:function (string, format) {
                return moment(string).format(format);
            },
            showUploadModal:function () {
                $("#upload-picture-modal").modal("show");
                $("form")[0].reset();
            }
        }
    }
</script>