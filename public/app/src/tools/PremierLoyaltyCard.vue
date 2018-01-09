<template>
    <div id="plc" v-if="user.is_client === 1">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#apply-now" data-toggle="tab">Apply Now</a>
                        </li>
                        <li>
                            <a href="#applications" data-toggle="tab">Recent Applications</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="apply-now">
                            <div class="alert alert-info" style="font-size:11px;" v-html="configs.PLC_APPLICATION_MESSAGE"></div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Select Branch:</label>
                                        <vue-select v-model="apply.branch" :options="branch_selection"></vue-select>
                                    </div>
                                    <div class="form-group">
                                        <label>Application Type:</label><br/>
                                        <label>
                                            <input type="radio" v-model="apply.type" value="New"/>New
                                        </label>
                                        <label>
                                            <input type="radio" v-model="apply.type" value="Replacement"/>Replacement
                                        </label>
                                    </div>
                                    <div class="form-group" v-if="apply.type==='Replacement'">
                                        <label>Reason for Replacement:</label>
                                        <textarea class="form-control" v-model="apply.reason" rows="2"></textarea>
                                    </div>
                                    <button class="btn btn-success btn-block" data-loading-text="Please Wait..." @click="applyPLC($event)">Submit</button>
                                </div>
                                <div class="col-md-7">
                                    <transactions-summary :transactions="transactions" v-if="transactions"></transactions-summary>
                                    <div class="alert alert-info" v-else>
                                        Please wait while we loading your transactions.
                                    </div>

                                    <img style="display:block; margin:auto;" v-bind:src="'../../images/app/plc_'+ user.gender +'.png'">
                                    <div style="display:block; text-align:center;" v-if="user.gender === 'female'">For Her</div>
                                    <div style="display:block; text-align:center;"  v-else>For Him</div>
                                </div>
                            </div>

                        </div>
                        <div id="applications" class="tab-pane">
                            <data-table :columns="plcTable.columns" :rows="premiers" :paginate="true"
                                        :onClick="plcTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>
<style>
    .dropdown-menu{
        max-height:200px !important;
    }
</style>
<script>
    import VueSelect from 'vue-select';
    import DataTable from '../components/DataTable.vue';
    import TransactionsSummary from '../components/TransactionsSummary.vue';
    import UnauthorizedError from '../errors/UnauthorizedError.vue';

    export default {
        name: 'PLC',
        props: ['user','token','configs','transactions'],
        components:{ VueSelect, DataTable, UnauthorizedError, TransactionsSummary },
        data: function(){
            return {
                title: 'Premier Loyalty Card',
                branches:[],
                premiers:[],
                apply:{
                    branch:null,
                    type:'New',
                    platform:'WEB',
                    reason:''
                },
                plcTable:{
                    columns: [
                        { label: 'BOSS ID', field: 'reference_no', filterable: true },
                        { label: 'Application Type', field: 'application_type', filterable: true },
                        { label: 'Branch', field: 'branch_name', filterable: true },
                        { label: 'Remarks', field: 'remarks', filterable: true },
                        { label: 'Date Applied', field: 'date_applied', filterable: true },
                        { label: 'Status', field: 'status', filterable: true },
                    ]
                }
            }
        },
        methods:{
            getBranches:function(){
                let u = this;
                axios.get('/api/branch/getBranches/active')
                    .then(function (response) {
                        u.branches = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            applyPLC:function(event){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                axios.post('/api/premier/applyPremier?token=' + u.token, this.apply)
                    .then(function (response) {

                        axios.post('/api/premier/sendPremierVerification?token=' + u.token, {data: u.apply, result: (response.data.result==='success')})
                        .then(function () {});

                        u.getPLC();
                        toastr.success("Request successfully sent. Please check your email for verification.");
                        $btn.button('reset');

                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
            },
            getPLC:function(){
                let u = this;
                axios.get('/api/premier/getPremiers/'+ this.user.id +'/all')
                    .then(function (response) {
                        u.premiers = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getBranches();
        },
        watch:{
          'user':function(){
              this.getPLC();
              this.$emit('get_transactions');
          }
        },
        computed:{
            net_amount:function(){
                var total=0;
                for(var x=0;x<this.transactions.length;x++){
                    total += Number(this.transactions[x].net_amount);
                }
                return total;
            },
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item, i){
                    a.push({
                        label:item.branch_name,
                        value:item.id,
                    });
                });
                return a;
            },
        }
    }
</script>