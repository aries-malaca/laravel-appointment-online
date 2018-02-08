<template>
    <div class="tab-pane active" id="settings">
        <div class="tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#apis" data-toggle="tab">External API</a>
                </li>
                <li>
                    <a href="#others" data-toggle="tab">Others</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="apis">
                    <data-table :columns="configTable.columns"
                                :rows="apis"
                                :paginate="true"
                                :onClick="configTable.rowClicked"/>
                </div>
                <div id="others" class="tab-pane">
                    <data-table :columns="configTable.columns"
                                :rows="others"
                                :paginate="true"
                                :onClick="configTable.rowClicked"/>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueSelect from "vue-select"
    import DataTable from '../tables/DataTable.vue';
    export default {
        name: 'Settings',
        components:{ VueSelect, DataTable },
        data: function(){
            return {
                apis:[],
                others:[],
                configTable:{
                    columns: [
                        { label: 'Name', field: 'config_name', filterable: true},
                        { label: 'Description', field: 'config_description', filterable: true },
                        { label: 'Value', field: 'config_value', filterable: true }
                    ],
                    rowClicked: this.viewConfig
                },
            }
        },
        methods:{
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u.apis = [];
                        u.others = [];
                        response.data.forEach(function(item){
                            if(item.config_category === 'API'){
                                u.apis.push(item);
                            }
                            else{
                                item.config_value = item.config_type==='html'? 'HTML':item.config_value;
                                u.others.push(item);
                            }
                        });
                    });
            },
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            },
            getConfigs:function(){
                this.getData('/api/config/getConfigs', 'configs');
            },
            viewConfig:function(config){

            }
        },
        mounted:function(){
            this.getConfigs();
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>