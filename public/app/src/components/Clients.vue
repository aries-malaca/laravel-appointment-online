<template>
    <div class="clients">
        <div class="portlet light" v-show="view=='list'" v-if="user.is_client !== 1 && gate(user, 'clients', 'view')">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Search {{ title }} </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="row">
                                    <label class="control-label col-md-3">Search</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" @keypress="listenKey($event)" v-model="search.keyword" />
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" id="btn-search" class="btn-success btn btn-md" @click="searchClients($event)">Search</button>
                                        <button type="button" class="btn-info btn btn-md" data-toggle="modal" href="#advanced-modal">Advanced Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="clients.length>0">
                            <data-table
                                    :columns="clientTable.columns"
                                    :rows="clients"
                                    :paginate="true"
                                    :onClick="clientTable.rowClicked"
                                    styleClass="table table-bordered table-hover table-striped"
                            />
                        </div>
                        <div class="alert alert-info" v-if="show_not_found">
                            <strong>No Client found with your search parameters.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <client-profile @back="view='list',view_id=0" :show="view=='single'" :with_back="true" :id="view_id" />

        <div class="modal fade" id="advanced-modal" data-backdrop="static" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Advanced Client Search</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <b>Client Migration: </b>
                            <p>Use this tool to migrate client profile from Old Database. Kindly search for
                                Name, email and birth date then select client to migrate.
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>First Name</label>
                                <input type="text" v-model="advanced.first_name" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label>Last Name</label>
                                <input type="text" v-model="advanced.last_name" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label>Email</label>
                                <input type="text" v-model="advanced.email" class="form-control"/>
                            </div>
                            <div class="col-md-3">
                                <label>Birth Date</label>
                                <input type="date" v-model="advanced.birth_date" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <br/>
                                <button class="btn btn-success" @click="searchAdvanced($event)">Search</button>
                                <br/>
                                <br/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <data-table v-if="accounts.length>0"
                                    :columns="advancedTable.columns"
                                    :rows="accounts"
                                    :paginate="true"
                                    :onClick="advancedTable.rowClicked"
                                    styleClass="table table-bordered table-hover table-striped"
                                />
                                <div class="alert alert-warning" v-else>
                                    No client profile found.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
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
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import DataTable from './tables/DataTable.vue';
    import ClientProfile from './clients/profile/ClientProfile.vue';

    export default {
        name: 'Clients',
        components:{ DataTable, ClientProfile, UnauthorizedError},
        data: function(){
            return {
                title: 'Clients',
                view:'list',
                view_id:0,
                search:{
                    keyword:''
                },
                advanced:{
                    first_name:'',
                    last_name:'',
                    email:'',
                    birth_date:'2000-01-01',
                },
                clients:[],
                clientTable:{
                    columns: [
                        { label: 'Name', field: 'name', filterable: true, html:true },
                        { label: 'Address', field: 'user_address', filterable: true },
                        { label: 'Mobile', field: 'user_mobile', filterable: true },
                        { label: 'Email', field: 'email', filterable: true },
                        { label: 'Gender', field: 'gender_html', filterable: true, html:true },
                    ],
                    rowClicked: this.viewClient,
                },
                advancedTable:{
                    columns: [
                        { label: 'First Name', field: 'cusfname'},
                        { label: 'Last Name', field: 'cuslname'},
                        { label: 'Address', field: 'cusaddress'},
                        { label: 'Mobile', field: 'cusmob'},
                        { label: 'Email', field: 'cusemail' },
                        { label: 'Gender', field: 'cusgender'},
                    ],
                    rowClicked: this.migrateClient,
                },
                show_not_found:false,
                accounts:[],
            }
        },
        methods:{
            migrateClient(client){
                if(!confirm("Proceed migration for this client ("+ client.cusemail +")?"))
                    return false;
                axios.post('/api/client/migrateClient', client)
                    .then(function() {
                        toastr.success("Migration success, close this dialog box and search for registered client.");
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            searchAdvanced(){
                let $btn = $(event.target);
                let u = this;
                axios.post('/api/client/searchAdvancedClients', this.advanced)
                    .then(function (response) {
                        $btn.button('reset');
                        u.accounts = response.data;
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            searchClients:function(event){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                u.show_not_found = false;

                axios.get('/api/client/searchClients', {params:this.search})
                .then(function (response) {
                    u.clients = response.data;
                    if(response.data.length===0)
                        u.show_not_found = true;
                    $btn.button('reset');
                })
                .catch(function (error) {
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            listenKey:function(event){
                if(event.keyCode === 13)
                    this.searchClients($("#btn-search"));
            },
            viewClient:function(client){
                this.view_id = client.id;
                this.view ='single';
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Clients');
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
            },
        }
    }
</script>