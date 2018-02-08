<template>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-puzzle font-grey-gallery"></i>
                <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                <button v-if="user.is_client !== 1" class="btn btn-info" @click="showAddModal"> Add Career </button>
            </div>
        </div>
        <div class="portlet-body">
            <div class="mt-element-list">
                <div class="mt-list-head list-news font-white bg-blue">
                    <div class="list-head-title-container">
                        <h3 class="list-title">Careers</h3>
                    </div>
                </div>
                <div>
                    <div class="panel-group accordion" id="accordion1" style="max-height:360px;overflow-y:scroll">
                        <div class="panel panel-default" v-for="career,key in careers">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle collapsed" data-parent="#accordion1" data-toggle="collapse"
                                       v-bind:href="'#collapse_'+career.id" aria-expanded="false">
                                        <span>{{ career.title }}</span>
                                    </a>
                                </h4>
                            </div>
                            <div v-bind:id="'collapse_'+career.id" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body">
                                    <p>Description: {{ career.description }}
                                    <br/>Date Posted: {{ career.date_posted }}</p>
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li v-for="requirement in career.career_data.requirements"> {{ requirement }} </li>
                                    </ul>
                                    <div class="row" v-if="user.is_client !== 1">
                                        <div class="col-md-12">
                                            <button class="btn btn-warning btn-sm" @click="moveCareer(1, career)">Move Up</button>
                                            <button class="btn btn-warning btn-sm" @click="moveCareer(0, career)">Move Down</button>
                                            <button class="btn btn-info btn-sm" @click="editCareer(career)">Edit</button>
                                            <button class="btn btn-danger btn-sm" @click="deleteCareer(career)">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add-career-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newCareer.id==0">Add Career</h4>
                        <h4 class="modal-title" v-else>Edit Career</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Title</label>
                            <input type="text" v-model="newCareer.title" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <textarea v-model="newCareer.description" rows="3" class="form-control"></textarea>
                        </div>
                        <h3>Requirements: <button class="btn btn-info" @click="addItem"> + </button></h3>
                        <table class="table table-bordered table-stripped table-hover">
                            <tr v-for="requirement,key in newCareer.career_data.requirements">
                                <td>
                                    <button class="btn btn-danger btn-xs" @click="removeItem(key)">X</button>
                                </td>
                                <td>
                                    <textarea v-model="newCareer.career_data.requirements[key]" class="form-control"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newCareer.id==0" @click="addCareer($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateCareer($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
</template>

<script>
    export default {
        name: 'Careers',
        data: function(){
            return {
                title: 'Careers',
                careers:[],
                newCareer:{
                    id:0,
                    description:'',
                    title:'',
                    career_data:{
                        requirements:[]
                    }
                }
            }
        },
        methods:{
            showAddModal:function(){
                this.newCareer = {
                    id:0,
                    description:'',
                    title:'',
                    career_data:{
                        requirements:[]
                    }
                };
                $("#add-career-modal").modal("show");
            },
            addItem:function(){
                this.newCareer.career_data.requirements.push('');
            },
            removeItem:function(key){
                this.newCareer.career_data.requirements.splice(key, 1);
            },
            editCareer:function(career){
                this.newCareer = {
                    id:career.id,
                    title:career.title,
                    description:career.description,
                    career_data:{
                        requirements:[]
                    }
                };

                for(var x=0;x<career.career_data.requirements.length;x++)
                    this.newCareer.career_data.requirements.push(career.career_data.requirements[x]);

                $("#add-career-modal").modal("show");
            },
            addCareer:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.makeRequest('/api/career/addCareer?token='+ this.token, 'post', this.newCareer, function(){
                    u.getCareers();
                    toastr.success("Successfully added Career");
                    $btn.button('reset');
                    $("#add-career-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateCareer:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.makeRequest('/api/career/updateCareer?token='+ this.token, 'post', this.newCareer, function(){
                    u.getCareers();
                    toastr.success("Successfully updated Career");
                    $btn.button('reset');
                    $("#add-career-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            deleteCareer:function(career){
                let u = this;
                let $btn = $(event.target);
                SweetConfirmation("Are you sure you want to delete this FAQ?", function(){
                    $btn.button('loading');
                    u.makeRequest('/api/career/deleteCareer?token='+ u.token, 'post', career, function(){
                        u.getCareers();
                        toastr.success("Successfully deleted Career");
                        $btn.button('reset');
                    },function(error){
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
                });
            },
            moveCareer:function(direction, career){
                u.makeRequest('/api/faq/deleteCareer?token='+ u.token, 'post', {id: career.id, direction: direction } , function(){
                    u.getCareers();
                    toastr.success("Successfully moved Career");
                    $btn.button('reset');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            },
            getCareers:function(){
                let u = this;
                axios.get('/api/career/getCareers')
                    .then(function (response) {
                        u.careers = response.data;
                    });
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Careers');
            this.getCareers();
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
            }
        }
    }
</script>