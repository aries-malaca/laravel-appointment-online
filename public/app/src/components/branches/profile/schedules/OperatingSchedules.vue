<template>
    <div class="tab-pane active" id="operating">
        <h4>Regular Schedule</h4>
        <div style="overflow-x:scroll">
            <table class="table table-responsive table-hover table-bordered table-stripped" v-if="regular_schedule">
                <thead>
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
                </thead>
                <tbody>
                <tr v-if="currently_editing === regular_schedule.id">
                    <td v-for="time,key in newSchedule.schedule_data">
                        <select v-model="newSchedule.schedule_data[key].start">
                            <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                        </select>
                        <select v-model="newSchedule.schedule_data[key].end">
                            <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-block" @click="currently_editing=undefined">Cancel</button>
                        <button class="btn btn-success btn-sm btn-block" @click="updateBranchSchedule">Save</button>
                    </td>
                </tr>
                <tr v-else>
                    <td v-for="time in regular_schedule.schedule_data">
                        {{ moment("2000-01-01 " + time.start).format("hh:mm A") }} - {{ moment("2000-01-01 " + time.end).format("hh:mm A")}}
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" v-if="gate(user, 'branch_schedules','update')" @click="editSchedule(regular_schedule)">Edit</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <h4>Custom Schedules <button @click="showAddScheduleModal" class="btn btn-info btn-sm"
                     v-if="gate(user, 'branch_schedules','add')">Add</button></h4>
        <div style="overflow-x:scroll">
            <table class="table table-responsive table-hover table-bordered table-stripped" v-if="custom_schedules">
                <thead>
                    <tr>
                        <th>Start</th>
                        <th>End</th>
                        <th>Type</th>
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
                    <tr v-for="schedule,key in custom_schedules" v-show="currently_editing !== schedule.id">
                        <td>
                            {{ moment(schedule.date_start).format("MM/DD/YYYY") }}
                            <span v-if="schedule.schedule_type==='closed'"> ({{ moment(schedule.date_start).format("hh:mm A") }})</span>
                        </td>
                        <td>
                            {{ moment(schedule.date_end).format("MM/DD/YYYY") }}
                            <span v-if="schedule.schedule_type==='closed'"> ({{ moment(schedule.date_end).format("hh:mm A") }})</span>
                        </td>
                        <td>{{ schedule.schedule_type }}</td>
                        <td v-for="time in custom_schedules[key].schedule_data" v-if="schedule.schedule_type==='custom' ">
                            {{ moment("2000-01-01 " + time.start).format("hh:mm A") }} - {{ moment("2000-01-01 " + time.end).format("hh:mm A")}}
                        </td>
                        <td v-if="schedule.schedule_type==='closed'" colspan="7">
                            <div class="alert alert-danger">
                                <h4>Branch were closed within this period.</h4>
                            </div>
                        </td>
                        <td>
                            <button v-if="gate(user, 'branch_schedules','update')" class="btn btn-info btn-sm btn-block" @click="editSchedule(schedule)">Edit</button>
                            <button v-if="gate(user, 'branch_schedules','update')" class="btn btn-danger btn-sm btn-block" @click="deleteBranchSchedule(schedule)">Delete</button>
                        </td>
                    </tr>
                    <tr v-show="newSchedule.schedule_type==='custom' && currently_editing===newSchedule.id">
                        <td>
                            <input type="date" class="form-control" v-model="newSchedule.date_start"/>
                        </td>
                        <td>
                            <input type="date" class="form-control" v-model="newSchedule.date_end"/>
                        </td>
                        <td>
                            <select v-model="newSchedule.schedule_type" class="form-control" style="width: 100px;">
                                <option value="custom">Custom</option>
                                <option value="closed">Closed</option>
                            </select>
                        </td>
                        <td v-for="time,key in newSchedule.schedule_data">
                            <select v-model="newSchedule.schedule_data[key].start">
                                <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                            </select>
                            <select v-model="newSchedule.schedule_data[key].end">
                                <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-block" @click="currently_editing=undefined">Cancel</button>
                            <button class="btn btn-success btn-sm btn-block" @click="updateBranchSchedule">Save</button>
                        </td>
                    </tr>
                    <tr v-show="newSchedule.schedule_type==='closed' && currently_editing===newSchedule.id">
                        <td>
                            <input type="date" class="form-control" v-model="newSchedule.date_start"/>
                            <input type="time" class="form-control" v-model="newSchedule.time_start"/>
                        </td>
                        <td>
                            <input type="date" class="form-control" v-model="newSchedule.date_end"/>
                            <input type="time" class="form-control" v-model="newSchedule.time_end"/>
                        </td>
                        <td>
                            <select style="width:100px;" v-model="newSchedule.schedule_type" class="form-control">
                                <option value="custom">Custom</option>
                                <option value="closed">Closed</option>
                            </select>
                        </td>
                        <td colspan="7">
                            <div class="alert alert-danger">
                                <h3>Branch Closed</h3>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-block" @click="currently_editing=undefined">Cancel</button>
                            <button class="btn btn-success btn-sm btn-block" @click="updateBranchSchedule">Save</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info">
            Custom schedules and closed schedules always overrides the regular schedule.
        </div>

        <div data-backdrop="static" class="modal fade" id="add-schedule-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Add Operating Schedule</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Start</label>
                                    <input type="date" class="form-control" v-model="newSchedule.date_start"/>
                                    <input type="time" class="form-control" v-if="newSchedule.schedule_type === 'closed'" v-model="newSchedule.time_start"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">End</label>
                                    <input type="date" class="form-control" v-model="newSchedule.date_end"/>
                                    <input type="time" v-if="newSchedule.schedule_type === 'closed'" class="form-control" v-model="newSchedule.time_end"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Schedule Type</label>
                                    <select v-model="newSchedule.schedule_type" class="form-control">
                                        <option value="custom">Custom</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="newSchedule.schedule_type === 'custom'">
                            <div style="overflow-x:scroll">
                                <div class="col-md-12">
                                    <table>
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
                                            <td v-for="time,key in newSchedule.schedule_data">
                                                <select v-model="newSchedule.schedule_data[key].start">
                                                    <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                                                </select>
                                                <select v-model="newSchedule.schedule_data[key].end">
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
                        <button type="button" @click="addBranchSchedule($event)" data-loading-text="Saving..." class="btn green">Save</button>
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
        name: 'OperatingSchedules',
        data: function(){
            return {
                newSchedule:{},
                currently_editing:undefined,
                times:[]
            }
        },
        methods:{
            showAddScheduleModal:function(){
                this.newSchedule = {
                    id:0,
                    date_start:moment().format("YYYY-MM-DD"),
                    date_end:moment().format("YYYY-MM-DD"),
                    time_start: moment().format("HH:mm"),
                    time_end: moment().format("HH:mm"),
                    branch_id:this.branch.id,
                    schedule_data:[
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                        {start:"09:00" , end: "20:00"},
                    ],
                    schedule_type:'custom'
                };
                this.currently_editing = undefined;
                $("#add-schedule-modal").modal("show");
            },
            editSchedule:function(schedule){
                this.newSchedule = {
                    id:schedule.id,
                    date_start:moment(schedule.date_start).format("YYYY-MM-DD"),
                    date_end:moment(schedule.date_end).format("YYYY-MM-DD"),
                    time_start: moment(schedule.date_start).format("HH:mm"),
                    time_end: moment(schedule.date_end).format("HH:mm"),
                    schedule_data:[],
                    branch_id:this.branch.id,
                    schedule_type:schedule.schedule_type
                };

                for(var x=0;x<schedule.schedule_data.length;x++){
                    this.newSchedule.schedule_data.push({
                        start:schedule.schedule_data[x].start,
                        end:schedule.schedule_data[x].end,
                    });
                }

                this.currently_editing = schedule.id;
            },
            updateBranchSchedule:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/schedule/updateBranchSchedule?token=' + this.token, this.newSchedule)
                    .then(function () {
                        u.$emit('refresh_branch');
                        $btn.button('reset');
                        u.currently_editing = undefined;
                        toastr.success("Schedule successfully updated.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            addBranchSchedule:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/schedule/addBranchSchedule?token=' + this.token, this.newSchedule)
                    .then(function () {
                        u.$emit('refresh_branch');
                        $btn.button('reset');
                        toastr.success("Schedule successfully added.");
                        $("#add-schedule-modal").modal("hide");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            deleteBranchSchedule:function(schedule){
                let u = this;
                SweetConfirmation("Are you sure you want to delete this schedule?", function(){
                    axios.post('/api/schedule/deleteBranchSchedule?token=' + u.token, schedule)
                        .then(function () {
                            u.$emit('refresh_branch');
                            toastr.success("Schedule successfully deleted.");
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                });
            }
        },
        mounted:function(){
            let start = moment("2020-01-01 05:00:00");
            for(var x=0;x<38;x++){
                this.times.push({
                        label: moment(start).add((x*30),"minutes").format("hh:mm A"),
                        value: moment(start).add((x*30),"minutes").format("HH:mm")
                    }
                );
            }
        },
        computed:{
            regular_schedule:function(){
                for(var x=0;x<this.branch.schedules.length;x++){
                    if(this.branch.schedules[x].schedule_type === 'regular')
                        return this.branch.schedules[x];
                }
                return false;
            },
            custom_schedules:function(){
                var array = [];
                for(var x=0;x<this.branch.schedules.length;x++){
                    if(this.branch.schedules[x].schedule_type === 'custom' || this.branch.schedules[x].schedule_type === 'closed')
                        array.push(this.branch.schedules[x]);
                }
                return array;
            },
            token(){
                return this.$store.state.token;
            },
            user(){
                return this.$store.state.user;
            },
            branch(){
                return this.$store.state.branches.viewing_branch;
            }
        }
    }
</script>