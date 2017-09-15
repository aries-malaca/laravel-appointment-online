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
    export default {
        name: 'ClientProfile',
        props: ['token','configs','id','with_back','id','show'],
        components:{ UploadPictureModal },
        data: function(){
            return {
                client:{},
            }
        },
        methods:{
            back:function(){
                this.$emit('back');
            },
            getClient:function(){
                let u = this;
                axios.get('/api/client/getClient/' + this.id)
                    .then(function (response) {
                        u.client = response.data;
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
            }
        },
        watch:{
            id:function(){
                this.getClient();
            }
        }
    }
</script>