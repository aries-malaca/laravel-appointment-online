<template>
    <div data-backdrop="static" class="modal fade" id="add-technician-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" v-if="operation==='add'">Add Technician</h4>
                    <h4 class="modal-title" v-else>Add Technician</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" v-model="newTechnician.first_name"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" class="form-control" v-model="newTechnician.middle_name"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" v-model="newTechnician.last_name"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Cluster</label>
                                <select v-model="newTechnician.cluster_id" class="form-control">
                                    <option v-for="cluster in clusters" :value="cluster.id">{{ cluster.cluster_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <input type="text" class="form-control" v-model="newTechnician.employee_id"/>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="newTechnician.technician_data !== undefined">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" class="form-control" v-model="newTechnician.technician_data.mobile"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender</label>
                                <select v-model="newTechnician.technician_data.gender" class="form-control">
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Birth Date</label>
                                <input type="date" class="form-control" v-model="newTechnician.technician_data.birth_date"/>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="newTechnician.technician_data !== undefined">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" v-model="newTechnician.technician_data.email"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea v-model="newTechnician.technician_data.address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="newTechnician.technician_data !== undefined">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Civil Status</label>
                                <select v-model="newTechnician.technician_data.civil_status" class="form-control">
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Position</label>
                                <input type="text" class="form-control" v-model="newTechnician.technician_data.position_name"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Hired</label>
                                <input type="date" class="form-control" v-model="newTechnician.technician_data.hired_date"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" v-if="operation==='add'" @click="addTechnician($event)" data-loading-text="Saving..." class="btn green">Save</button>
                    <button type="button" v-else @click="updateTechnician($event)" data-loading-text="Saving..." class="btn green">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</template>
<script>
    export default {
        name: 'TechnicianModal',
        props:['operation'],
        data: function(){
            return {
                newTechnician:{
                    first_name:'',
                    middle_name:'',
                    last_name:'',
                    technician_status:'active',
                    cluster_id:0,
                    is_active: 1,
                    employee_id: '',
                    technician_data: {
                        mobile:'',
                        gender:'female',
                        address:'',
                        email:'',
                        civil_status:'single',
                        position_name:'Wax Technician',
                        birth_date:'2000-01-01',
                        hired_date:moment().format("YYYY-MM-DD"),
                    },
                }
            }
        },
        methods:{
            getTechnician:function(){
                let u = this;
                axios.get('/api/technician/getTechnician/' + this.technician.id)
                    .then(function (response) {
                        if(response.data.id !== undefined){
                            u.newTechnician = response.data;
                            u.$store.commit('technicians/updateViewingTechnician', response.data);
                        }
                    });
            },
            addTechnician(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/technician/addTechnician?token=' + this.token, 'post', this.newTechnician, function(){
                    toastr.success("Technician added successfully.");
                    $btn.button('reset');
                    $("#add-technician-modal").modal('hide');
                    u.$socket.emit('refreshModel', 'technicians');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateTechnician(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/technician/updateTechnician?token=' + this.token, 'post', this.newTechnician, function(){
                    toastr.success("Technician updated successfully.");
                    $btn.button('reset');
                    u.$socket.emit('refreshModel', 'technicians');
                    u.$emit('refreshTechnician');
                    $("#add-technician-modal").modal('hide');
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
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            clusters(){
                return this.$store.state.branches.clusters.filter((item)=>{
                    return (item.cluster_data.ems_supported !== true);
                });
            },
            technician(){
                return this.$store.state.technicians.editing_technician;
            },
        },
        watch:{
            technician(){
                if(this.operation === 'edit'){
                    this.getTechnician();
                }
            }
        }
    }
</script>