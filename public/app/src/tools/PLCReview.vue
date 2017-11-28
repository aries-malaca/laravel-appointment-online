<template>
    <div id="plc-tracker">
        <div class="portlet light" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                    <a class="btn btn-info" href="../../#/plctracker">PLC Tracker</a>
                </div>
            </div>
            <div class="portlet-body">
                <data-table
                    :columns="requestTable.columns"
                    :rows="requests"
                    :paginate="true"
                    :onClick="requestTable.rowClicked"
                    styleClass="table table-bordered table-hover table-striped"
                />
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>
<script>

    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    import DataTable from '../components/DataTable.vue';
    export default {
        name: 'PLCReview',
        props: ['token','user','configs'],
        components:{ UnauthorizedError, DataTable },
        data: function(){
            return {
                title: 'PLC Review Requests',
                requests:[],
                requestTable:{
                    columns: [
                        { label: 'Client', field: 'name', filterable:true },
                        { label: 'Message', field: 'message',filterable:true},
                        { label: 'Status', field: 'status',filterable:true, html:true},
                    ],
                    rowClicked: this.viewRequest,
                },
                newRequest:{}
            }
        },
        methods:{
            getRequests:function(){
                let u = this;
                axios.get('/api/premier/getRequests')
                    .then(function (response) {
                        u.requests = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            viewRequest:function(request){
                this.newRequest = {
                    id:request.id,
                    message:request.message,
                    remarks:request.remarks,
                    client_id:request.client_id,
                    valid_id_image:request.valid_id_image,
                    status:request.status,
                }
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getRequests();
        }
    }
</script>