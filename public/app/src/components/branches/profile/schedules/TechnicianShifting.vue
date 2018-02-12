<template>
    <div id="shifting" class="tab-pane" v-if="branch.cluster_data !==undefined">
        <div v-if="branch.cluster_data.ems_supported">
            <div class="alert alert-info">
                Selected Branch is EMS Supported. Click here:
                <a href="https://ems.lay-bare.com" target="_blank">http://ems.lay-bare.com</a> to login.
            </div>
        </div>
        <div v-else>
            <button @click="showAddModal" class="btn btn-info btn-sm">Add</button>
            <br/>
            <br/>
            <div style="overflow-x:scroll">
                <table class="table table-responsive table-hover table-bordered table-stripped">
                    <thead>
                        <tr>
                            <th>Schedule</th>
                            <th>Color</th>
                            <th>Sun.</th>
                            <th>Mon.</th>
                            <th>Tue.</th>
                            <th>Wed.</th>
                            <th>Thu.</th>
                            <th>Fri.</th>
                            <th>Sat.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="shift in shifts">
                            <td>{{ shift.shift_name }}</td>
                            <td :style="'color:'+ shift.shift_color ">{{ shift.shift_color.toUpperCase() }}</td>
                            <td v-for="t in shift.shift_data">{{ t }}</td>
                            <td>
                                <button class="btn btn-info btn-block" @click="viewShift(shift)">Edit</button>
                                <button class="btn btn-danger btn-block" @click="deleteTechnicianShift(shift)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="add-shift-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Shifting Schedule</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Shift Name</label>
                                    <input type="text" class="form-control" v-model="newShift.shift_name"/>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Color</label>
                                    <select v-model="newShift.shift_color" class="form-control">
                                        <option value="red">Red</option>
                                        <option value="green">Green</option>
                                        <option value="blue">Blue</option>
                                        <option value="yellow">Yellow</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" >
                            <div style="overflow-x:scroll">
                                <div class="col-md-12">
                                    <table style="width:100%">
                                        <tr>
                                            <th>Sun.</th>
                                            <th>Mon.</th>
                                            <th>Tue.</th>
                                            <th>Wed.</th>
                                            <th>Thu.</th>
                                            <th>Fri.</th>
                                            <th>Sat.</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <td v-for="time,key in newShift.shift_data">
                                                <select v-model="newShift.shift_data[key]">
                                                    <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newShift.id===0" @click="addTechnicianShift($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateTechnicianShift($event)" data-loading-text="Saving..." class="btn green">Save</button>
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
        name: 'TechnicianShifting',
        data(){
            return{
                shifts:[],
                newShift:{},
                times:[]
            }
        },
        methods:{
            getShifts:function(){
                let u = this;
                axios.get('/api/schedule/getTechnicianShifts/' + this.branch.id)
                    .then(function (response) {
                        u.shifts = response.data;
                    });
            },
            showAddModal:function(){
                this.newShift = {
                    id:0,
                    branch_id:this.branch.id,
                    shift_name:'',
                    shift_color:'',
                    shift_data:[
                        '09:00',
                        '09:00',
                        '09:00',
                        '09:00',
                        '09:00',
                        '09:00',
                        '09:00'
                    ]
                };
                $("#add-shift-modal").modal("show");
            },
            viewShift:function(shift){
                this.newShift = {
                    id:shift.id,
                    branch_id:shift.branch_id,
                    shift_name:shift.shift_name,
                    shift_color:shift.shift_color,
                    shift_data:[
                        shift.shift_data[0],
                        shift.shift_data[1],
                        shift.shift_data[2],
                        shift.shift_data[3],
                        shift.shift_data[4],
                        shift.shift_data[5],
                        shift.shift_data[6]
                    ]
                };
                $("#add-shift-modal").modal("show");
            },
            addTechnicianShift:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/schedule/addTechnicianShift?token=' + this.token, this.newShift)
                    .then(function () {
                        u.$emit('refresh_branch');
                        u.getShifts();
                        $btn.button('reset');
                        $("#add-shift-modal").modal("hide");
                        toastr.success("Shift successfully added.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            updateTechnicianShift:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/schedule/updateTechnicianShift?token=' + this.token, this.newShift)
                    .then(function () {
                        u.$emit('refresh_branch');
                        u.getShifts();
                        $btn.button('reset');
                        $("#add-shift-modal").modal("hide");
                        toastr.success("Shift successfully updated.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            deleteTechnicianShift:function(schedule){
                let u = this;

                SweetConfirmation("Are you sure you want to delete this shift?", function(){
                    axios.post('/api/schedule/deleteTechnicianShift?token=' + u.token, schedule)
                        .then(function () {
                            u.getShifts();
                            u.$emit('refresh_branch');
                            toastr.success("Shift successfully deleted.");
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                });
            },
            moment:moment,
        },
        mounted:function(){
            let start = moment("2020-01-01 06:00:00");
            for(var x=0;x<24;x++){
                this.times.push({
                        label: moment(start).add((x*30),"minutes").format("hh:mm A"),
                        value: moment(start).add((x*30),"minutes").format("HH:mm")
                    }
                );
            }

            this.getShifts();
        },
        computed:{
            branch(){
                return this.$store.state.branches.viewing_branch;
            },
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>