<template>
    <div class="tab-pane" id="schedules">
        <div class="row">
            <div class="col-md-3" v-if="technician.cluster_data !== null && technician.cluster_data !== undefined">
                <div class="note note-success" v-if="technician.cluster_data.ems_supported===true">
                    <h4 class="block">EMS Employee</h4>
                    <p>
                        Setup {{ technician.first_name }} {{ technician.last_name }}'s schedule at EMS <a target="_blank" :href="technician.cluster_data.ems_server">{{ technician.cluster_data.ems_server }}</a>.
                    </p>
                </div>
                <div v-else>
                    <div class="row" v-if="gate(user, 'technician_schedules', 'add')">
                        <div class="col-md-12">
                            <h4>Set Single Schedule</h4>
                            <div>
                                <label>Branch: </label>
                                <select class="form-control" v-model="branch_id" @change="getTechnicianShifts(branch_id)">
                                    <option v-for="branch in branches" :value="branch.id">{{ branch.branch_name }}</option>
                                </select>
                            </div>
                            <div id='external-events' v-if="shifts.length>0">
                                <br/>
                                <p><strong>Drag and Drop Shift</strong></p>
                                <div style="width:100px">
                                    <div class='fc-event' :data-shift_id="shift.id" :data-branch_id="shift.branch_id" :style="'background-color:' + shift.shift_color "
                                         v-for="(shift,key) in shifts">{{ shift.shift_name }}
                                    </div>
                                    <div class='fc-event' :data-shift_id="0" :data-branch_id="branch_id" style="'background-color:black">
                                        Rest Day
                                    </div>
                                </div>
                            </div>

                            <br/>
                            <hr/>

                            <h4>Set Regular Schedule</h4>
                            <button class="btn btn-info" @click="showRegularScheduleModal">Schedule Form</button>

                            <br/>
                            <hr/>

                            <div v-if="regularSchedules.length>0">
                                <h4>Regular Schedules List</h4>
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Coverage</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="schedule in regularSchedules">
                                        <td>{{ moment(schedule.date_start).format("MM/DD/YYYY") + ' - ' + moment(schedule.date_end).format("MM/DD/YYYY") }}</td>
                                        <td>
                                            <button class="btn btn-success btn-xs" @click="editRegularSchedule(schedule)">View</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div data-backdrop="static" class="modal fade" id="add-schedule-modal" tabindex="-1" role="basic" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title">
                                                <span v-if="setRegularSchedule.id===0">Add</span>
                                                <span v-else>Edit</span>
                                                Regular Schedule
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Branch:</label>
                                                        <select v-model="setRegularSchedule.branch_id" class="form-control">
                                                            <option :value="branch.id" v-for="branch in branches">{{ branch.branch_name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Start:</label>
                                                        <input type="date" class="form-control" v-model="setRegularSchedule.date_start"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>End:</label>
                                                        <input type="date" class="form-control" v-model="setRegularSchedule.date_end"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
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
                                                            <td v-for="time,key in setRegularSchedule.schedule_data">
                                                                <select v-model="setRegularSchedule.schedule_data[key]">
                                                                    <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                                                                    <option value="00:00">Rest-day</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div v-if="gate(user, 'technician_schedules', 'update')">
                                                <button type="button" v-if="setRegularSchedule.id!==0" @click="deleteSchedule(setRegularSchedule)" data-loading-text="Saving..." class="btn pull-left red">Delete</button>
                                                <button type="button" v-if="setRegularSchedule.id===0" @click="addRegularSchedule($event)" data-loading-text="Saving..." class="btn green pull-right">Save</button>
                                                <button type="button" v-else @click="updateRegularSchedule($event)" data-loading-text="Saving..." class="btn green pull-right">Save</button>
                                            </div>
                                            <button type="button" class="btn dark btn-outline pull-right" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->

                            <div data-backdrop="static" class="modal fade" id="single-schedule-modal" tabindex="-1" role="basic" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title">
                                                Add/Edit Single Schedule
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Branch:</label>
                                                        <select v-model="setSingleSchedule.branch_id" class="form-control">
                                                            <option :value="branch.id" v-for="branch in branches">{{ branch.branch_name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Date:</label>
                                                        <input type="date" disabled class="form-control" v-model="setSingleSchedule.date"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Shift:</label>
                                                        <select v-model="setSingleSchedule.time" class="form-control">
                                                            <option v-for="t in times" v-bind:value="t.value">{{ t.label }}</option>
                                                            <option value="00:00">Rest-day</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" v-if="setSingleSchedule.id!==0" @click="deleteSchedule(setSingleSchedule)" data-loading-text="Saving..." class="btn pull-left red">Delete</button>
                                            <button type="button" @click="saveSingleSchedule()" data-loading-text="Saving..." class="btn pull-right green">Save</button>
                                            <button type="button" class="btn dark btn-outline pull-right" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" v-else>
                <div class="alert alert-warning">
                    <b>Notice:</b>
                    <p>Please select cluster for technician.</p>
                </div>
            </div>
            <div class="col-md-9">
                <div class="portlet light calendar">
                    <div class="portlet-body">
                        <div id="schedule-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Schedules',
        props:['tab'],
        data: function(){
            return {
                events:[],
                setRegularSchedule:{ },
                setSingleSchedule:{ },
                shifts:[],
                times:[],
                branch_id:0
            }
        },
        methods:{
            showRegularScheduleModal(){
                this.setRegularSchedule = {
                    id:0,
                    date_start: moment().format("YYYY-MM-01"),
                    date_end: moment().format("YYYY-MM-DD"),
                    branch_id:0,
                    technician_id:this.technician.id,
                    schedule_data:['09:00', '09:00', '09:00', '09:00', '09:00', '09:00', '09:00'],
                    schedule_type:'RANGE',
                };
                $("#add-schedule-modal").modal("show");
            },
            editRegularSchedule(schedule){
                var sd = schedule.schedule_data;
                this.setRegularSchedule = {
                    id:schedule.id,
                    date_start: moment(schedule.date_start).format("YYYY-MM-DD"),
                    date_end: moment(schedule.date_end).format("YYYY-MM-DD"),
                    branch_id:schedule.branch_id,
                    technician_id:schedule.technician_id,
                    schedule_data:[sd[0], sd[1], sd[2], sd[3], sd[4], sd[5], sd[6]],
                    schedule_type:'RANGE',
                };
                $("#add-schedule-modal").modal("show");
            },
            editSingleSchedule(schedule){
                this.setSingleSchedule = {
                    id:schedule.event.id,
                    technician_id:this.technician.id,
                    branch_id:schedule.event.branch_id,
                    date:schedule.dd,
                    time:schedule.event.schedule_data,
                };
                $("#single-schedule-modal").modal("show");
            },
            saveSingleSchedule(date, shift_id, branch_id){
                let u = this;
                var data = date !== undefined?{technician_id: u.technician.id, date:date, shift_id:shift_id, branch_id:branch_id}:this.setSingleSchedule;

                axios.post('/api/technician/addSingleSchedule?token=' + u.token, data)
                    .then(function () {
                        u.getSchedule();
                        toastr.success("Schedule successfully added.");
                    })
                    .catch(function (error) {
                        u.getSchedule();
                        XHRCatcher(error);
                    });
            },
            getSchedule(){
                let u = this;
                axios.get('/api/technician/getSchedules/' + this.technician.id)
                    .then(function (response) {
                        u.events = response.data;

                        setTimeout(()=>{
                            $('#schedule-calendar').fullCalendar('destroy'); // destroy the calendar
                            $("#schedule-calendar").fullCalendar({
                                events: u.schedules,
                                timeFormat: 'hh:mm A',
                                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                                droppable: true, // this allows things
                                eventClick: u.gate(u.user, 'technician_schedules', 'update')?function(event) {
                                    if(event.event.schedule_type === 'RANGE')
                                        u.editRegularSchedule(event.event);
                                    else{
                                        u.editSingleSchedule(event);
                                    }
                                }:null,
                                drop:function(date,event){
                                    let shift_id = Number(event.target.dataset.shift_id);
                                    let branch_id = Number(event.target.dataset.branch_id);
                                    u.saveSingleSchedule(moment(date).format("YYYY-MM-DD"), shift_id, branch_id);
                                }
                            });
                        },1000);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            getTechnicianShifts(branch_id){
                let u = this;
                axios.get('/api/schedule/getTechnicianShifts/' + branch_id)
                    .then(function (response) {
                        u.shifts = response.data;

                        setTimeout(()=>{
                            $('#external-events .fc-event').each(function(e,f) {
                                // store data so the calendar knows to render an event upon drop
                                $(f).data('event', {
                                    title: $.trim($(f).text()), // use the element's text as the event title
                                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                                });

                                // make the event draggable using jQuery UI
                                $(f).draggable({
                                    zIndex: 999,
                                    revert: true,      // will cause the event to go back to its
                                    revertDuration: 0  //  original position after the drag
                                });
                            });
                        }, 400);
                    });
            },
            addRegularSchedule(event){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/technician/addRegularSchedule?token=' + this.token, this.setRegularSchedule)
                    .then(function () {
                        u.getSchedule();
                        $btn.button('reset');
                        $("#add-schedule-modal").modal("hide");
                        toastr.success("Schedule successfully added.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            updateRegularSchedule(event){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/technician/updateRegularSchedule?token=' + this.token, this.setRegularSchedule)
                    .then(function () {
                        u.getSchedule();
                        $btn.button('reset');
                        $("#add-schedule-modal").modal("hide");
                        toastr.success("Schedule successfully updated.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            deleteSchedule(schedule){
                if(!confirm("Are you sure you want to delete this schedule?"))
                    return false;

                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.post('/api/technician/deleteSchedule?token=' + this.token, schedule)
                    .then(function () {
                        u.getSchedule();
                        $btn.button('reset');
                        $("#add-schedule-modal").modal("hide");
                        $("#single-schedule-modal").modal("hide");
                        toastr.success("Schedule successfully deleted.");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            getShiftColor(shifts, time){
                for(var x=0;x<shifts.length;x++){
                    var d = JSON.parse(shifts[x].shift_data);
                    for(var y=0;y<d.length;y++){
                        if(time === d[y])
                            return shifts[x].shift_color;
                    }
                }
                return undefined;
            }
        },
        mounted(){
            this.getSchedule();
            let start = moment("2020-01-01 06:00:00");
            for(var x=0;x<24;x++){
                this.times.push({
                        label: moment(start).add((x*30),"minutes").format("hh:mm A"),
                        value: moment(start).add((x*30),"minutes").format("HH:mm")
                    }
                );
            }

        },
        watch:{
          tab(){
              if(this.tab===2)
                this.getSchedule();
          }
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            technician(){
                return this.$store.state.technicians.viewing_technician;
            },
            user(){
                return this.$store.state.user;
            },
            branches(){
                let u = this;
                return this.$store.state.branches.branches.filter((item)=>{
                    return item.cluster_id === u.technician.cluster_id;
                });
            },
            schedules(){
                var events = [];

                for(var x=0;x<this.events.length;x++){
                    let shifts = this.events[x].shifts;

                    if(this.events[x].schedule_type === 'RANGE'){
                        var start = Number(moment(this.events[x].date_start).format("X"));
                        var days = 0;
                        while( (start + (days*86400)) <= Number(moment(this.events[x].date_end).format("X"))){
                            var d = moment(this.events[x].date_start).add(days, 'days');

                            if((Number(moment(d).format("X") ) + 86400) >= moment().format("X"))
                                events.push({
                                    title  : this.events[x].schedule_data[Number(d.format("e"))] ==='00:00'? 'Rest Day':this.events[x].branch_name,
                                    start  : moment(d).format("YYYY-MM-DD") + " " +  this.events[x].schedule_data[Number(d.format("e"))],
                                    allDay : this.events[x].schedule_data[Number(d.format("e"))] ==='00:00',
                                    backgroundColor : this.events[x].schedule_data[Number(d.format("e"))] ==='00:00'?'black':this.getShiftColor(shifts, this.events[x].schedule_data[Number(d.format("e"))]),
                                    id : 0,
                                    dd : moment(d).format("YYYY-MM-DD"),
                                    event : this.events[x]
                                });

                            days++;
                        }
                    }
                    else{
                        var find_index = false;
                        events.find((event, index)=>{
                            if(event.dd === moment(this.events[x].date_start).format("YYYY-MM-DD"))
                                find_index = index;
                        });

                        if(find_index !== false){
                            events[find_index].start = moment(events[find_index].start).format("YYYY-MM-DD") + " " +  this.events[x].schedule_data ;
                            events[find_index].title = this.events[x].schedule_data ==='00:00'? '* Rest Day': "* "+ this.events[x].branch_name;
                            events[find_index].allDay = this.events[x].schedule_data ==='00:00';
                            events[find_index].backgroundColor = this.events[x].schedule_data === '00:00'?'black':this.getShiftColor(shifts, this.events[x].schedule_data);
                            events[find_index].id = this.events[x].id;
                            events[find_index].event = this.events[x];
                        }
                        else{
                            events.push({
                                title  : this.events[x].schedule_data ==='00:00'? '* Rest Day':  "* "+  this.events[x].branch_name,
                                start  : moment(this.events[x].date_start).format("YYYY-MM-DD") + " " +  this.events[x].schedule_data,
                                allDay : this.events[x].schedule_data ==='00:00',
                                backgroundColor : this.events[x].schedule_data ==='00:00'?'black':this.getShiftColor(shifts, this.events[x].schedule_data),
                                id : this.events[x].id,
                                dd : moment(this.events[x].date_start).format("YYYY-MM-DD"),
                                event : this.events[x]
                            });
                        }
                    }
                }

                return events;
            },
            regularSchedules(){
                return this.events.filter((item)=>{
                    return item.schedule_type === 'RANGE';
                });
            }
        }
    }
</script>
<style>
    .portlet.calendar .fc-event .fc-content{
        padding:2px !important;
    }
    .portlet.calendar.light .fc-button{
        top:0px;
    }
    .fc-state-hover{
        border-bottom: 0px !important;
    }
    .fc-time, .fc-title{
        font-size:12px !important;
    }
    #external-events .fc-event{
        cursor:pointer;
        margin:5px;
    }
</style>