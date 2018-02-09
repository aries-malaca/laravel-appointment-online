<template>
    <div class="clients">
        <div class="portlet light" v-show="view=='list'" v-if="user.is_client !== 1">
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
                clients:[],
                clientTable:{
                    columns: [
                        { label: 'Photo', field: 'picture_html', html:true },
                        { label: 'Name', field: 'name', filterable: true, html:true },
                        { label: 'Address', field: 'user_address', filterable: true },
                        { label: 'Mobile', field: 'user_mobile', filterable: true },
                        { label: 'Email', field: 'email', filterable: true },
                        { label: 'Gender', field: 'gender_html', filterable: true, html:true },
                    ],
                    rowClicked: this.viewClient,
                },
                show_not_found:false
            }
        },
        methods:{
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