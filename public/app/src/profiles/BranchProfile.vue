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
                    <a href="#appointments" data-toggle="tab">Appointments</a>
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
        <div class="portlet-body profile">
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
                                        <td> Classification: </td>
                                        <td> {{ branch.branch_classification }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact No.: </td>
                                        <td> {{ branch.branch_contact }} </td>
                                    </tr>
                                    <tr>
                                        <td> Contact Person: </td>
                                        <td> {{ branch.branch_contact_person}} </td>
                                    </tr>
                                    <tr>
                                        <td> Email: </td>
                                        <td> {{ branch.branch_email}} </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--end row-->
                            <button class="btn btn-info" v-if="with_back" @click="editBranch(branch)">Edit Branch</button>
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
                                </div>
                            </div>
                        </div>
                        <!--end col-md-4-->
                    </div>
                    <div class="row">
                        <div class="col-md-2" v-for="(pic,key) in pictures">
                            <ul class="list-unstyled profile-nav" style="margin-top:5px">
                                <li>
                                    <img v-bind:src="'images/branches/'+ pic" class="img-responsive pic-bordered" alt="" />
                                    <a @click="showUploadModal(key)" class="profile-edit"> <i class="fa fa-pencil"></i> </a>
                                    <a @click="removePicture(key,pic)" style="margin-top:30px" class="profile-edit"> <i class="fa fa-close"></i> </a>
                                </li>
                            </ul>
                        </div>
                        <button class="btn btn-success" @click="addPicture">Add</button>
                    </div>
                </div>
                <!--tab_1_2-->
                <div class="tab-pane" id="account">

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
    import UploadPictureModal from "../modals/UploadPictureModal.vue";
    export default {
        name: 'BranchProfile',
        props: ['token','configs','id','with_back','id','show'],
        components:{ UploadPictureModal },
        data: function(){
           return {
               branch:{},
               pictures:[]
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
                        u.branch.branch_pictures = JSON.parse(response.data.branch_pictures);
                        u.branch.branch_data = JSON.parse(response.data.branch_data);
                        u.pictures = u.branch.branch_pictures;
                        u.branch.map_coordinates = JSON.parse(u.branch.map_coordinates);
                        u.branch.social_media_accounts = JSON.parse(u.branch.social_media_accounts);
                    }
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
            }
        },
        watch:{
            id:function(){
                this.getBranch();
            }
        }
    }
</script>