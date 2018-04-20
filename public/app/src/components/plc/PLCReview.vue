<template>
    <div id="plc-review">
        <data-table
            :columns="requestTable.columns"
            :rows="requests"
            :paginate="true"
            :onClick="requestTable.rowClicked"
            styleClass="table table-bordered table-hover table-striped"
        />

        <div class="modal fade" id="review-modal" tabindex="1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Premier Review Request</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Message:</label>
                                    <textarea rows="2" class="form-control" v-model="newRequest.message" disabled></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <textarea class="form-control" v-model="newRequest.remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Attachment:</label>
                                    <img class="img img-responsive" v-bind:src="'../../images/ids/'+ newRequest.valid_id_url" alt="">
                                </div>
                            </div>
                            <div class="col-md-6" v-if="newRequest.plc_review_request_data !== undefined">
                                <div class="form-group">
                                    <label>BOSS ID:</label>
                                    <input class="form-control" v-model="newRequest.plc_review_request_data.boss_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="processRequest($event, 'approved')">Approve</button>
                        <button class="btn btn-danger" @click="processRequest($event, 'denied')">Deny</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</template>
<script>

    import DataTable from '../tables/DataTable.vue';
    export default {
        name: 'PLCReview',
        components:{ DataTable },
        data: function(){
            return {
                requests:[],
                requestTable:{
                    columns: [
                        { label: 'Client', field: 'name', filterable:true },
                        { label: 'Message', field: 'message',filterable:true},
                        { label: 'Remarks', field: 'remarks',filterable:true},
                        { label: 'Date Processed', field: 'processed_date_formatted',filterable:true},
                        { label: 'Processed By', field: 'updated_by',filterable:true},
                        { label: 'Status', field: 'status_html',filterable:true, html:true},
                    ],
                    rowClicked: this.viewRequest,
                },
                newRequest:{}
            }
        },
        methods:{
            getRequests:function(){
                let u = this;
                axios.get('/api/premier/getAllRequests')
                    .then(function (response) {
                        u.requests = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            processRequest:function(event, action){
                this.newRequest.status = action;

                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                axios({url:'/api/premier/processRequest?token='+u.token, method:'post', data:this.newRequest})
                    .then(function () {
                        u.getRequests();
                        toastr.success("Successfully updated.");
                        $btn.button('reset');

                        $("#review-modal").modal("hide");
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            viewRequest:function(request){
                this.newRequest = {
                    id:request.id,
                    message:request.message,
                    remarks:request.remarks,
                    client_id:request.client_id,
                    valid_id_url:request.valid_id_url,
                    status:request.status,
                    status_html:request.status_html,
                    plc_review_request_data:{
                        boss_id: request.plc_review_request_data.boss_id
                    },
                }
                $("#review-modal").modal("show");
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getRequests();
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