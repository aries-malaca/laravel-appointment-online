<template>
    <div class="tab-pane" id="user-levels">
        <button type="button" @click="showAddUserLevelModal" class="btn green-meadow">New User Level</button>
        <br/><br/>
        <data-table
                :columns="userLevelTable.columns"
                :rows="userLevels"
                :paginate="true"
                :onClick="userLevelTable.rowClicked"
                styleClass="table table-bordered table-hover table-striped"  />

        <div data-backdrop="static" class="modal fade" id="add-user-level-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newUserLevel.id==0">Add User Level</h4>
                        <h4 class="modal-title" v-else>Edit User Level</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Level Name</label>
                                    <input type="text" v-model="newUserLevel.level_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <input type="text" v-model="newUserLevel.description" placeholder="(Optional)" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3" v-if="newUserLevel.level_data !== undefined">
                                <div class="form-group">
                                    <label class="control-label">Dashboard</label>
                                    <select v-model="newUserLevel.level_data.dashboard" class="form-control">
                                        <option v-bind:value="dashboard" v-for="dashboard in dashboards">{{ dashboard }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <h4>User Access</h4>
                        <table class="table table-condensed table-bordered table-hover table-striped"
                               v-if="newUserLevel.level_data !== undefined">
                            <tbody>
                            <tr v-for="p,key in permissions">
                                <th>{{ p.name }}</th>
                                <td>
                                    <span v-for="a,k in p.actions">
                                        <label>
                                             <input type="checkbox" v-model="newUserLevel.level_data.permissions[p.name]" :value="a"/>
                                            {{ a }} &nbsp;
                                        </label>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newUserLevel.id==0" @click="addUserLevel($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateUserLevel($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import DataTable from '../tables/DataTable.vue';
    export default {
        name: 'Levels',
        components:{ VueSelect, DataTable },
        data: function(){
            return {
                newUserLevel:{},
                userLevels:[],
                permissions:[],
                userLevelTable:{
                    columns: [
                        { label: 'Level', field: 'level_name', filterable: true },
                        { label: 'Description', field: 'description', filterable: true },
                        { label: 'Dashboard', field: 'level_data.dashboard', filterable: true }
                    ],
                    rowClicked: this.viewUserLevel
                },
                dashboards:["AdminDashboard",
                            "BranchSupervisorDashboard",
                            "CustomerServiceDashboard",
                            "FranchisingDashboard",
                            "PurchasingDashboard",
                            "TrainingDashboard"
                            ]
            }
        },
        methods:{
            getPermissions(){
                this.getData('/api/user/getPermissions', 'permissions');
            },
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
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
            getUserLevels:function(){
                this.getData('/api/user/getUserLevels', 'userLevels');
            },
            showAddUserLevelModal:function(){
                this.newUserLevel = {
                    id:0,
                    level_name:'',
                    description:'',
                    level_data:{
                        dashboard:'AdminDashboard',
                        permissions:{}
                    }
                };

                let u = this;
                this.permissions.forEach((item)=>{
                    u.newUserLevel.level_data.permissions[item.name] = [];
                });

                $("#add-user-level-modal").modal("show");
            },
            viewUserLevel:function(level){
                this.newUserLevel = {
                    id:level.id,
                    level_name:level.level_name,
                    description:level.description,
                    level_data:{
                        dashboard: level.level_data.dashboard,
                        permissions:{}
                    }
                };

                let u = this;
                this.permissions.forEach((item)=>{
                    u.newUserLevel.level_data.permissions[item.name] = function(){
                        if(level.level_data.permissions !== undefined){
                            if(level.level_data.permissions[item.name] !== undefined){
                                return level.level_data.permissions[item.name];
                            }
                        }
                        return [];
                    }();
                });

                $("#add-user-level-modal").modal("show");
            },
            addUserLevel:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/user/addUserLevel?token=' + this.token, 'post', this.newUserLevel, function(){
                    u.getUserLevels();
                    toastr.success("User level added successfully.");
                    $btn.button('reset');
                    $("#add-user-level-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateUserLevel:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/user/updateUserLevel?token=' + this.token, 'post', this.newUserLevel, function(){
                    u.getUserLevels();
                    toastr.success("User level updated successfully.");
                    $btn.button('reset');
                    $("#add-user-level-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
        },
        mounted:function(){
            this.getUserLevels();
            this.getPermissions();
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>