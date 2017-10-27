<template>
    <div class="tab-pane" id="users">
        <button type="button" @click="showAddUserModal" class="btn green-meadow">New User</button>
        <br/><br/>
        <data-table v-if="branches.length>0"
                    :columns="userTable.columns"
                    :rows="users"
                    :paginate="true"
                    :onClick="userTable.rowClicked"/>

        <div class="modal fade" id="add-user-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newUser.id == 0">Add User</h4>
                        <h4 class="modal-title" v-else>Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">First Name</label>
                                    <input type="text" v-model="newUser.first_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Middle Name</label>
                                    <input type="text" v-model="newUser.middle_name" placeholder="(Optional)" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Last Name</label>
                                    <input type="text" v-model="newUser.last_name" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <input type="text" v-model="newUser.user_address" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Gender</label>
                                    <select class="form-control" v-model="newUser.gender">
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Mobile</label>
                                    <input type="text" v-model="newUser.user_mobile" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input type="text" v-model="newUser.email" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">User Level</label>
                                    <select class="form-control" v-model="newUser.level">
                                        <option v-for="level in levels" v-bind:value="level.id">{{ level.level_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="newUser.user_data!==undefined">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Branch Assignment</label>
                                    <vue-select multiple v-model="newUser.user_data.branches" :options="branch_selection"></vue-select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newUser.id==0" @click="addUser($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateUser($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import VueSelect from "vue-select"
    import DataTable from '../components/DataTable.vue';
    export default {
        name: 'Users',
        components:{ VueSelect, DataTable },
        props: ['token'],
        data: function(){
            return {
                branches:[],
                users:[],
                levels:[],
                newUser:{},
                userTable:{
                    columns: [
                        { label: 'Name', field: 'name_html', filterable: true, html:true},
                        { label: 'User Level',  field: 'level_name', filterable: true },
                        { label: 'Mobile', field: 'user_mobile', filterable: true },
                        { label: 'Email', field: 'email', filterable: true }
                    ],
                    rowClicked: this.viewUser
                },
            }
        },
        methods:{
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
                    });
            },
            getLevels:function(){
                this.getData('/api/user/getUserLevels', 'levels');
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
            getBranches:function(){
                this.getData('/api/branch/getBranches/active', 'branches');
            },
            getBranchName:function(id){
                for(var x=0;x<this.branches.length;x++){
                    if(id == this.branches[x].id)
                        return this.branches[x].branch_name;
                }
            },
            showAddUserModal:function(){
                this.newUser = {
                    id:0,
                    first_name:'',
                    middle_name:'',
                    last_name:'',
                    user_address:'',
                    birth_date: moment().format("YYYY-MM-DD"),
                    user_mobile:'',
                    gender:'female',
                    level:0,
                    user_data:{
                        branches:[]
                    },
                    email:''
                };
                $("#add-user-modal").modal("show");
            },

            getUsers:function(){
                let u = this;
                axios.get('/api/user/getUsers')
                    .then(function (response) {
                        u.users = [];
                        response.data.forEach(function(item){
                            item.name_html = '<table><tr><td><img class="img-circle" style="height:35px" src="images/users/'+ item.user_picture +'" /></td><td> &nbsp;' + item.first_name +' ' + item.last_name +'</td></tr></table>';
                            item.user_data = JSON.parse(item.user_data);
                            u.users.push(item);
                        });
                    });
            },
            viewUser:function(user){
                this.newUser = {
                    id:user.id,
                    first_name:user.first_name,
                    middle_name:user.middle_name,
                    last_name:user.last_name,
                    user_address:user.user_address,
                    email:user.email,
                    user_mobile:user.user_mobile,
                    gender:user.gender,
                    level:user.level,
                    user_data:{
                        branches:[]
                    }
                };

                for(var x=0;x<user.user_data.branches.length;x++){
                    this.newUser.user_data.branches.push({
                        label:this.getBranchName(user.user_data.branches[x]),
                        value:user.user_data.branches[x]
                    });
                }
                $("#add-user-modal").modal("show");
            },

            addUser:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/user/addUser?token=' + this.token, 'post', this.newUser, function(){
                    u.getUsers();
                    toastr.success("User added successfully.");
                    $btn.button('reset');
                    $("#add-user-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateUser:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/user/updateUser?token=' + this.token, 'patch', this.newUser, function(){
                    u.getUsers();
                    toastr.success("User updated successfully.");
                    $btn.button('reset');
                    $("#add-user-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.getUsers();
            this.getBranches();
            this.getLevels();
        },
        computed:{
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item, i){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            }
        }
    }
</script>