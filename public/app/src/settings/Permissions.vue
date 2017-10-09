<template>
    <div class="tab-pane" id="permissions">

    </div>
</template>
<script>
    import VueSelect from "vue-select"
    import DataTable from '../components/DataTable.vue';
    export default {
        name: 'Permissions',
        components:{ VueSelect, DataTable },
        props: ['token'],
        data: function(){
            return {

            }
        },
        methods:{
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
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
        },
        mounted:function(){

        }
    }
</script>