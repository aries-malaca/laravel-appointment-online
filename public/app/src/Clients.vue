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
                                <input type="text" class="form-control" v-model="search.keyword" />
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn-success btn btn-md" @click="searchClients($event)">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet light" v-if="clients.length>0">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Search Results</span>
                </div>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">
                <vue-good-table
                    :columns="columns"
                    :rows="clients"
                    :paginate="true"
                    :lineNumbers="true"/>
            </div>
        </div>
    </div>
</template>

<script>
    import VueGoodTable from './components/Table.vue';
    export default {
        name: 'Clients',
        components:{ VueGoodTable},
        data: function(){
            return {
                title: 'Clients',
                search:{
                    keyword:''
                },
                clients:[],
                columns: [
                    {
                        label: 'First Name',
                        field: 'first_name',
                        filterable: true,
                    },
                    {
                        label: 'Middle Name',
                        field: 'middle_name',
                        filterable: true,
                    },
                ],
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
            },
            searchClients:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios.get('/api/clients/searchClients', {params:this.search})
                .then(function (response) {
                    u.clients = response.data;
                    $btn.button('reset');
                })
                .catch(function (error) {
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.emit();
        }
    }
</script>