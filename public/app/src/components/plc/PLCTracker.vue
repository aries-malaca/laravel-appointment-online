<template>
    <div id="plc-tracker">
        <div class="portlet light" v-if="user.is_client !== 1 && gate(user, 'plc_tracker', 'view')">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tracker" data-toggle="tab">Tracker</a>
                    </li>
                    <li v-if="gate(user, 'plc_request', 'view')">
                        <a href="#requests" data-toggle="tab">Requests</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tracker">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>PLC Status</label>
                                    <select v-model="filter" class="form-control">
                                        <option value="approved" v-bind:value="state.value" v-for="state in states">{{ state.label }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9" v-if="gate(user, 'plc_tracker', 'process')">
                                <label><br><br></label>
                                <button v-if="plc[filter].length > 0" class="btn btn-lg btn-warning" @click="selectAll(true)">Select</button>
                                <button class="btn btn-lg purple" v-if="selected.length>0" @click="selectAll(false)">Deselect</button>
                                <button v-if="selected.length>0" class="btn btn-lg btn-info" @click="showOptionModal">Options</button>
                                <button class="btn btn-lg btn-success" @click="exportExcel" v-if="selected.length>0">Export Excel</button>
                            </div>
                        </div>
                        <data-table
                            :columns="plcTable.columns"
                            :rows="plc[filter]"
                            :paginate="true"
                            :onClick="plcTable.rowClicked"
                            styleClass="table table-bordered table-hover table-striped">
                        </data-table>
                    </div>
                    <div class="tab-pane" id="requests">
                        <plc-review :token="token" :user="user" :configs="configs"></plc-review>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <div data-backdrop="static" class="modal fade" id="launch-excel-modal" tabindex="1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Generated Excel</h4>
                    </div>
                    <div class="modal-body">
                        <a class="btn btn-block btn-success btn-lg" target="_blank" v-bind:href="'../../temp/'+ link">Click To View</a>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="options-modal" tabindex="1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">PLC Options</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            You selected <b>{{ selected.length }}</b> {{ selected.length>1?"Entries":"Entry" }}
                        </div>

                        <div>
                            <div class="form-group" v-if="filter!=='deleted' && filter!=='denied'">
                                <label>Move to: </label>
                                <select class="form-control" v-model="move_to">
                                    <option value="approved" v-bind:value="state.value"
                                            v-bind:disabled="filter === state.value || state.value === 'denied'" v-for="state in states">
                                        {{ state.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" v-else>
                                <label>Move to: </label>
                                <select class="form-control" v-model="move_to">
                                    <option :value="'denied'" v-bind:disabled="filter === 'denied'">Denied</option>
                                    <option :value="'deleted'" v-bind:disabled="filter === 'deleted'">Deleted</option>
                                </select>
                            </div>
                            <button class="btn btn-block btn-success" @click="movePremier">Update</button>
                            <div class="form-group" v-if="filter === 'denied'">
                                <br/>
                                <label>Or: </label>
                            </div>
                            <button class="btn btn-block btn-warning" v-if="filter === 'denied'" @click="recheckPremier">Re-Evaluate</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</template>

<script>
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    import DataTable from '../tables/DataTable.vue';
    import PlcReview from './PLCReview.vue';
    export default {
        name: 'PLCTracker',
        components:{ UnauthorizedError, PlcReview, DataTable },
        data: function(){
            return {
                title: 'PLC Tracker',
                filter:'approved',
                toggles:false,
                selected:[],
                link:'',
                plc:{
                    approved:[],
                    denied:[],
                    ready:[],
                    delivery:[],
                    processing:[],
                    deleted:[],
                    picked_up:[]
                },
                move_to:'approved',
                states:[
                    { label:'Approved', value:'approved' },
                    { label:'Denied', value:'denied' },
                    { label:'Processing', value:'processing' },
                    { label:'Delivery', value:'delivery' },
                    { label:'Ready', value:'ready' },
                    { label:'Picked-up', value:'picked_up' },
                    { label:'Deleted', value:'deleted' },
                ],
                plcTable:{
                    columns: [
                        { label: '', field: 'checked_html', html:true},
                        { label: 'BOSS ID', field: 'reference_no', filterable:true},
                        { label: 'Client', field: 'client.username', filterable:true },
                        { label: 'Address', field: 'client.user_address', filterable:true },
                        { label: 'Mobile', field: 'client.user_mobile', filterable:true },
                        { label: 'Gender', field: 'client.gender', filterable:true },
                        { label: 'Email', field: 'client.email', filterable:true },
                        { label: 'Branch', field: 'branch_name', filterable:true },
                        { label: 'Type', field: 'application_type', filterable:true },
                    ],
                    rowClicked: this.togglePLC,
                },
            }
        },
        methods:{
            recheckPremier:function(event){
                let u = this;
                let $btn = $(event.target);
                axios({url:'/api/premier/movePremier?token='+this.token, method:'post', data:{selected:this.selected, move_to:this.move_to}})
                    .then(function (response) {
                        $btn.button('reset');
                        u.getPremiers(u.move_to);
                        u.getPremiers(u.filter);
                        u.selected=[];
                        $("#options-modal").modal("hide");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            movePremier:function(event){
                let u = this;
                let $btn = $(event.target);
                axios({url:'/api/premier/movePremier?token='+this.token, method:'post', data:{selected:this.selected, move_to:this.move_to}})
                    .then(function (response) {
                        $btn.button('reset');
                        u.getPremiers(u.move_to);
                        u.getPremiers(u.filter);
                        u.selected=[];
                        $("#options-modal").modal("hide");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            togglePLC:function(plc){
                plc.checked = !plc.checked;

                if(plc.checked )
                    plc.checked_html  = '<input type="checkbox" class="checkbox" checked/>';
                else
                    plc.checked_html  = '<input type="checkbox" class="checkbox"/>';

                for(var x=0;x<this.plc[this.filter].length;x++){
                    if(plc.id === this.plc[this.filter][x].id){
                        this.plc[this.filter][x].checked = !this.plc[this.filter][x].checked;
                        this.toggles = !this.toggles;

                        if(this.plc[this.filter][x].checked)
                            this.plc[this.filter][x].checked_html = '<input type="checkbox" class="checkbox" checked/>';
                        else
                            this.plc[this.filter][x].checked_html = '<input type="checkbox" class="checkbox"/>';
                    }
                }
            },
            getPremiers:function(status){
                let u = this;
                axios.get('/api/premier/getPremiers/all/'+status)
                    .then(function (response) {
                        u.plc[status] = response.data.filter((item)=>{
                            return (u.user.user_data.branches.indexOf(item.branch_id)  !== -1 || u.user.user_data.branches.indexOf(0) !== -1)
                        });
                        for(var x=0;x<u.plc[status].length;x++){
                            u.plc[status][x].checked = false;
                            u.plc[status][x].checked_html = '<input type="checkbox" class="checkbox"/>';
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            selectAll:function(flag){
                let u = this;
                axios.get('/api/premier/getPremiers/all/'+ this.filter)
                    .then(function (response) {
                        u.plc[u.filter] = response.data;
                        for(var x=0;x<u.plc[u.filter].length;x++){
                            u.plc[u.filter][x].checked = flag;
                            u.plc[u.filter][x].checked_html = flag?'<input type="checkbox" class="checkbox" checked/>':'<input type="checkbox" class="checkbox"/>';
                        }
                        u.refreshTable();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            exportExcel:function(){
                let u = this;
                axios({url:'/api/premier/exportExcel?token='+this.token, method:'post', data:{selected:this.selected}})
                    .then(function (response) {
                        console.log(response);
                        if(response.data.result === 'success'){
                            u.link = response.data.filename;
                            $("#launch-excel-modal").modal("show");
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            refreshTable:function(){
                this.selected = [];
                for(var x=0;x<this.plc[this.filter].length;x++){
                    if(this.plc[this.filter][x].checked)
                        this.selected.push(this.plc[this.filter][x].id);
                }
            },
            showOptionModal:function(){
                if(this.filter === 'approved')
                    this.move_to='processing';
                else if(this.filter === 'processing')
                    this.move_to='delivery';
                else if(this.filter === 'delivery')
                    this.move_to='ready';
                else if(this.filter === 'ready')
                    this.move_to='picked_up';
                else if(this.filter === 'denied')
                    this.move_to='deleted';
                else if(this.filter === 'deleted')
                    this.move_to='denied';

                $("#options-modal").modal("show");
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'PLC Tracker');
            this.getPremiers('approved');
            this.getPremiers('denied');
            this.getPremiers('ready');
            this.getPremiers('delivery');
            this.getPremiers('processing');
            this.getPremiers('deleted');
            this.getPremiers('picked_up');
        },
        watch:{
            toggles:function(){
                this.refreshTable();
            },
            filter:function(){
                this.refreshTable();
            }
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