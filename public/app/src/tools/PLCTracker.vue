<template>
    <div id="plc-tracker">
        <div class="portlet light" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tracker" data-toggle="tab">Tracker</a>
                    </li>
                    <li>
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
                                        <option value="approved">Approved</option>
                                        <option value="denied">Denied</option>
                                        <option value="processing">Processing</option>
                                        <option value="delivery">Delivery</option>
                                        <option value="ready">Ready</option>
                                        <option value="picked_up">Picked Up</option>
                                        <option value="deleted">Deleted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <label><br><br></label>
                                <button class="btn btn-lg btn-warning" @click="selectAll(true)">Select</button>
                                <button class="btn btn-lg purple" v-if="selected.length>0" @click="selectAll(false)">Deselect</button>

                                <span v-if="selected.length>0">
                                    <button class="btn btn-lg btn-info" v-if="filter==='approved'">Process Selected</button>
                                    <button class="btn btn-lg btn-info" v-else-if="filter==='processing'">Deliver Selected</button>
                                    <button class="btn btn-lg btn-info" v-else-if="filter==='delivery'">Mark As Ready</button>
                                    <button class="btn btn-lg btn-info" v-else-if="filter==='ready'">Mark as Picked Up</button>
                                    <button class="btn btn-lg btn-info" v-else-if="filter==='denied'">Delete Selected</button>
                                </span>

                                <button class="btn btn-lg btn-success" @click="exportExcel" v-if="selected.length>0">Export Excel</button>
                            </div>
                        </div>
                        <data-table
                            :columns="plcTable.columns"
                            :rows="plc[filter]"
                            :paginate="true"
                            :onClick="plcTable.rowClicked"
                            styleClass="table table-bordered table-hover table-striped"
                            :rowStyleClass="decorateRow">
                        </data-table>
                    </div>
                    <div class="tab-pane" id="requests">
                        <plc-review :token="token" :user="user" :configs="configs"></plc-review>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <div class="modal fade" id="launch-excel-modal" tabindex="1">
            <div class="modal-dialog modal-xs">
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
    </div>
</template>

<script>
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    import DataTable from '../components/DataTable.vue';
    import PlcReview from '../tools/PLCReview.vue';
    export default {
        name: 'PLCTracker',
        props: ['token','user','configs'],
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
                plcTable:{
                    columns: [
                        { label: '', field: 'checked_html', html:true },
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
                        u.plc[status] = response.data;
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
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
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
        }
    }
</script>