<template>
    <div class="control_panel">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#settings" data-toggle="tab">System Settings</a>
                    </li>
                    <li>
                        <a href="#users" data-toggle="tab">Users</a>
                    </li>
                    <li>
                        <a href="#user-levels" data-toggle="tab">User Levels</a>
                    </li>
                    <li>
                        <a href="#permissions" data-toggle="tab">Permissions</a>
                    </li>
                    <li>
                        <a href="#places" data-toggle="tab">Places</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="settings">

                    </div>
                    <div class="tab-pane" id="users">
                        <button type="button" @click="showAddUserModal" class="btn green-meadow">New User</button>
                        <br/><br/>
                        <data-table v-if="branches.length>0"
                            :columns="userTable.columns"
                            :rows="users"
                            :paginate="true"
                            :onClick="userTable.rowClicked"
                            styleClass="table table-bordered table-hover table-striped"
                        />
                    </div>
                    <div class="tab-pane" id="user-levels">
                        <button type="button" @click="showAddUserLevelModal" class="btn green-meadow">New User Level</button>
                        <br/><br/>
                        <data-table
                            :columns="userLevelTable.columns"
                            :rows="userLevels"
                            :paginate="true"
                            :onClick="userLevelTable.rowClicked"
                            styleClass="table table-bordered table-hover table-striped"
                        />
                    </div>

                    <div class="tab-pane" id="permissions">

                    </div>
                    <div class="tab-pane" id="places">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" @click="showAddRegionModal" class="btn green-meadow">New Region</button>
                                <br/><br/>
                                <data-table
                                    title="Regions"
                                    :columns="regionTable.columns"
                                    :rows="regions"
                                    :paginate="true"
                                    :onClick="regionTable.rowClicked"
                                    styleClass="table table-bordered table-hover table-striped"
                                />
                            </div>
                            <div class="col-md-6">
                                <button type="button" @click="showAddCityModal" class="btn green-meadow">New City</button>
                                <br/><br/>
                                <data-table
                                    title="Cities"
                                    :columns="cityTable.columns"
                                    :rows="cities"
                                    :paginate="true"
                                    :onClick="cityTable.rowClicked"
                                    styleClass="table table-bordered table-hover table-striped"
                            />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                        <option v-for="level in userLevels" v-bind:value="level.id">{{ level.level_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
        <div class="modal fade" id="add-user-level-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newUserLevel.id==0">Add User Level</h4>
                        <h4 class="modal-title" v-else>Edit User Level</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Level Name</label>
                                    <input type="text" v-model="newUserLevel.level_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <input type="text" v-model="newUserLevel.description" placeholder="(Optional)" class="form-control" />
                                </div>
                            </div>
                        </div>
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

        <div class="modal fade" id="add-region-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newRegion.id==0">Add Region</h4>
                        <h4 class="modal-title" v-else>Edit Region</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Region Name</label>
                                    <input type="text" v-model="newRegion.region_name" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newRegion.id==0" @click="addRegion($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateRegion($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="add-city-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newCity.id==0">Add City</h4>
                        <h4 class="modal-title" v-else>Edit City</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">City Name</label>
                                    <input type="text" v-model="newCity.city_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Region</label>
                                    <select v-model="newCity.region_id" class="form-control">
                                        <option v-for="region in regions" v-bind:value="region.id">{{ region.region_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newCity.id==0" @click="addCity($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateCity($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import DataTable from './components/DataTable.vue';
    export default {
        name: 'ControlPanel',
        components:{ VueSelect, DataTable },
        props: ['token'],
        data: function(){
            return {
                title: 'Control Panel',
                branches:[],
                users:[],
                userLevels:[],
                cities:[],
                regions:[],
                newUser:{
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
                    email:'',
                },
                newUserLevel:{
                    id:0, level_name:'', description:''
                },
                newRegion:{
                    id:0, region_name:''
                },
                newCity:{
                    id:0, region_id:0, city_name:''
                },
                userTable:{
                    columns: [
                        {
                            label: 'Name', field: 'name_html', filterable: true, html:true
                        },
                        {
                            label: 'User Level',  field: 'level_name', filterable: true,
                        },
                        {
                            label: 'Mobile', field: 'user_mobile', filterable: true,
                        },
                        {
                            label: 'Email', field: 'email', filterable: true,
                        },
                    ],
                    rowClicked: this.viewUser,
                },
                userLevelTable:{
                    columns: [
                        {
                            label: 'Level', field: 'level_name', filterable: true,
                        },
                        {
                            label: 'Description', field: 'description', filterable: true,
                        },
                    ],
                    rowClicked: this.viewUserLevel,
                },
                cityTable:{
                    columns: [
                        {
                            label: 'City', field: 'city_name', filterable: true,
                        },
                        {
                            label: 'Region', field: 'region_name', filterable: true,
                        },
                    ],
                    rowClicked: this.viewCity,
                },
                regionTable:{
                    columns: [
                        {
                            label: 'Region', field: 'region_name', filterable: true,
                        },
                    ],
                    rowClicked: this.viewRegion,
                },
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
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
            getCities:function(){
                this.getData('/api/city/getCities', 'cities');
            },
            getRegions:function(){
                this.getData('/api/region/getRegions', 'regions');
            },
            getUserLevels:function(){
                this.getData('/api/user/getUserLevels', 'userLevels');
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
                    email:'',
                };
                $("#add-user-modal").modal("show");
            },
            showAddUserLevelModal:function(){
                this.newUserLevel = {
                    id:0,
                    level_name:'',
                    description:''
                };
                $("#add-user-level-modal").modal("show");
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
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
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
            },

            viewUserLevel:function(level){
                this.newUserLevel = {
                    id:level.id,
                    level_name:level.level_name,
                    description:level.description
                };
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

                this.makeRequest('/api/user/updateUserLevel?token=' + this.token, 'patch', this.newUserLevel, function(){
                    u.getUserLevels();
                    toastr.success("User level updated successfully.");
                    $btn.button('reset');
                    $("#add-user-level-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            showAddCityModal:function(){
                this.newCity = {
                    id:0,
                    city_name:'',
                    region_id:0
                };
                $("#add-city-modal").modal("show");
            },
            showAddRegionModal:function(){
                this.newRegion = {
                    id:0,
                    region_name:''
                };
                $("#add-region-modal").modal("show");
            },
            addRegion:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/region/addRegion?token=' + this.token, 'post', this.newRegion, function(){
                    u.getRegions();
                    toastr.success("Region added successfully.");
                    $btn.button('reset');
                    $("#add-region-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            addCity:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/city/addCity?token=' + this.token, 'post', this.newCity, function(){
                    u.getCities();
                    toastr.success("City added successfully.");
                    $btn.button('reset');
                    $("#add-city-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewCity:function(city){
                this.newCity = {
                    id:city.id,
                    city_name:city.city_name,
                    region_id:city.region_id
                };
                $("#add-city-modal").modal("show");
            },
            viewRegion:function(region){
                this.newRegion = {
                    id:region.id,
                    region_name:region.region_name,
                };
                $("#add-region-modal").modal("show");
            },
            updateRegion:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/region/updateRegion?token=' + this.token, 'patch', this.newRegion, function(){
                    u.getRegions();
                    toastr.success("Region updated successfully.");
                    $btn.button('reset');
                    $("#add-region-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateCity:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/city/updateCity?token=' + this.token, 'patch', this.newCity, function(){
                    u.getCities();
                    toastr.success("City updated successfully.");
                    $btn.button('reset');
                    $("#add-city-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.emit();
            this.getUsers();
            this.getUserLevels();
            this.getBranches();
            this.getRegions();
            this.getCities();
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