<template>
    <div class="tab-pane" id="service-types">
        <button type="button" v-if="gate(user, 'services', 'add')" @click="showAddServiceTypeModal" class="btn green-meadow">New Service Type</button>
        <br/><br/>
        <data-table :columns="serviceTypeTable.columns" :rows="types" :paginate="true"
                    :onClick="serviceTypeTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />

        <div data-backdrop="static" class="modal fade" id="add-service-type-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newServiceType.id==0">Add Service Type</h4>
                        <h4 class="modal-title" v-else>Edit Service Type</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Service Name</label>
                                    <input type="text" v-model="newServiceType.service_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Service Description</label>
                                    <textarea v-model="newServiceType.service_description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" v-if="newServiceType.service_type_data !== undefined">
                                    <label class="control-label">Restricted</label>
                                    <vue-select multiple v-model="newServiceType.service_type_data.restricted" :options="service_selection">
                                    </vue-select>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="newServiceType.id != 0">
                            <div class="col-md-12">
                                <upload-form input_id="service_file" form_id="service_form" category="service" :param_url="'service_id='+newServiceType.id"
                                             :placeholder_image="'images/services/'+newServiceType.service_picture" @emit_host="getServiceTypes" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newServiceType.id==0" @click="addServiceType($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateServiceType($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import DataTable from '../tables/DataTable.vue';
    import UploadForm from '../uploader/UploadForm.vue';
    import VueSelect from "vue-select"

    export default {
        name: 'ServiceTypes',
        components:{ DataTable, UploadForm, VueSelect },
        data: function(){
            return {
                serviceTypeTable:{
                    columns: [
                        { label: 'Photo', field: 'service_picture_html', html:true },
                        { label: 'Service Name', field: 'service_name', filterable: true },
                        { label: 'Service Description',  field: 'service_description', filterable: true }
                    ],
                    rowClicked: this.viewServiceType
                },
                newServiceType:{}
            }
        },
        methods:{
            getServiceTypes(){
                this.$store.dispatch('services/fetchTypes');
                this.$store.dispatch('services/fetchServices');
                $("#add-service-type-modal").modal("hide");
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
            showAddServiceTypeModal:function(){
                this.newServiceType = {
                    id:0,
                    service_name:'',
                    service_description:'',
                    service_picture:'',
                    service_type_data:{
                        restricted:[]
                    }
                };
                $("#add-service-type-modal").modal("show");
                try{
                    $("form")[0].reset();
                }
                catch(error){}
            },
            addServiceType:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/service/addServiceType?token=' + this.token, 'post', this.newServiceType, function(){
                    u.$store.dispatch('services/fetchTypes');
                    u.$store.dispatch('services/fetchServices');
                    toastr.success("Service Type added successfully.");
                    $btn.button('reset');
                    $("#add-service-type-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateServiceType:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/service/updateServiceType?token=' + this.token, 'post', this.newServiceType, function(){
                    u.$store.dispatch('services/fetchTypes');
                    u.$store.dispatch('services/fetchServices');
                    toastr.success("Service Type updated successfully.");
                    $btn.button('reset');
                    $("#add-service-type-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewServiceType:function(service_type){
                if(!this.gate(this.user, 'services', 'update'))
                    return false;

                let u = this;

                this.newServiceType = {
                    id:service_type.id,
                    service_name:service_type.service_name,
                    service_description:service_type.service_description,
                    service_picture:service_type.service_picture,
                    service_type_data:{
                        restricted: service_type.service_type_data.restricted.map(function(item){
                            return {
                                value:item,
                                label:u.getServiceName(item)
                            }
                        })
                    }
                };

                $("#add-service-type-modal").modal("show");
                try{
                    $("form")[0].reset();
                }
                catch(error){}
            },
            getServiceName:function(id){
                if(id===0)
                    return 'ALL';

                for(var x=0;x<this.types.length;x++){
                    if(id === this.types[x].id)
                        return this.types[x].service_name;
                }
                return 'Unknown';
            }
        },
        computed:{
            service_selection(){
                let u = this;
                let aa = [{
                    label:'ALL',
                    value:0
                }];

                return aa.concat(this.types.map(function(item){
                    item.label = item.service_name;
                    item.value = item.id;
                    return item;
                }).filter(function(item){
                    return u.newServiceType.id===0 || u.newServiceType.id !== item.id
                }));
            },
            types(){
                return this.$store.state.services.types.map(function(item){
                    item.service_picture_html = '<img src="images/services/'+item.service_picture+'" style="height:40px"/>';
                    return item;
                });
            },
            token(){
                return this.$store.state.token;
            },
            user(){
                return this.$store.state.user;
            }
        }
    }
</script>