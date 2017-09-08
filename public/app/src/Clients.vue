<template>
    <div class="clients">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Search {{ title }} </span>
                </div>
            </div>
            <div class="portlet-body">
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

                    <client-modal :token="token" @refresh_client="refreshClient" :client="display_client"></client-modal>
                </div>

                <div class="alert alert-info" v-if="show_not_found">
                    <strong>No Client found with your search parameters.</strong>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from './components/DataTable.vue';
    import ClientModal from './modals/ClientModal.vue';
    export default {
        name: 'Clients',
        components:{ DataTable, ClientModal},
        props:['token'],
        data: function(){
            return {
                title: 'Clients',
                search:{
                    keyword:''
                },
                clients:[],
                clientTable:{
                    columns: [
                        {
                            label: 'Name',
                            field: 'name_html',
                            filterable: true,
                            html:true
                        },
                        {
                            label: 'Address',
                            field: 'user_address',
                            filterable: true,
                        },
                        {
                            label: 'Mobile',
                            field: 'user_mobile',
                            filterable: true,
                        },
                        {
                            label: 'Email',
                            field: 'email',
                            filterable: true,
                        },
                        {
                            label: 'Gender',
                            field: 'gender_html',
                            filterable: true,
                            html:true
                        },
                    ],
                    rowClicked: this.viewClient,
                },
                display_client:{},
                show_not_found:false
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
            },
            searchClients:function(event){
                let u = this;

                let $btn = $(event.target);
                $btn.button('loading');

                u.show_not_found = false;
                u.clients = [];

                axios.get('/api/client/searchClients', {params:this.search})
                .then(function (response) {
                    response.data.forEach(function(item){
                        item.name_html = '<table><tr><td><img class="img-circle" style="height:35px" src="images/users/'+ item.user_picture +'" /></td><td> &nbsp;' + item.first_name +' ' + item.last_name +'</td></tr></table>';
                        item.gender_html = '<span class="badge badge-'+ (item.gender=='male'?'success':'warning')+'">'+item.gender.toUpperCase()+'</span>';
                        item.user_data = JSON.parse(item.user_data);
                        u.clients.push(item);
                    });
                    if(response.data.length==0){
                        u.show_not_found = true;
                    }
                    $btn.button('reset');
                })
                .catch(function (error) {
                    toastr.error(error.message);
                    $btn.button('reset');
                });
            },
            viewClient:function(data){
                this.display_client = data;
                $("#client-modal").modal("show");
            },
            listenKey:function(event){
                if(event.keyCode == 13){
                    this.searchClients($("#btn-search"));
                }
            },
            refreshClient:function(client){
                let u = this;
                axios.get('/api/client/getClient/'+client.id)
                .then(function (response) {
                    u.clients.forEach(function (item, i) {
                        if(item.id == response.data.id){
                            u.clients[i] = response.data;
                        }
                    });

                    u.display_client = response.data;
                    u.display_client.name_html = '<table><tr><td><img class="img-circle" style="width:50px" src="images/users/'+ response.data.user_picture +'" /></td><td> &nbsp;' + response.data.first_name +' ' + response.data.last_name +'</td></tr></table>';
                    u.display_client.gender_html  = '<span class="badge badge-'+ (response.data.gender=='male'?'success':'warning')+'">'+response.data.gender.toUpperCase()+'</span>';
                    u.display_client.user_data = JSON.parse(response.data.user_data);
                });
            }
        },
        mounted:function(){
            this.emit();
        }
    }
</script>