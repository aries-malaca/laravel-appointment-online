<template>
    <div class="tab-pane active" id="services">
        <button type="button" @click="showAddServiceModal" class="btn green-meadow">New Service</button>
        <br/><br/>
        <data-table :columns="serviceTable.columns" :rows="services" :onClick="serviceTable.rowClicked"
                    :paginate="true" styleClass="table table-bordered table-hover table-striped" />

        <div class="modal fade" id="add-service-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newService.id==0">Add Service</h4>
                        <h4 class="modal-title" v-else>Edit Service</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">ID</label>
                                    <input type="number" @change="searchItem(newService.search_id,'service')" v-model="newService.search_id" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Code</label>
                                    <input type="text" v-model="newService.service_code" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Service Gender</label>
                                    <select v-model="newService.service_gender" class="form-control">
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                        <option value="both">Unisex</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Service Price</label>
                                    <input type="number" class="form-control" v-model="newService.service_price"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Service Type</label>
                                    <select v-model="newService.service_type_id" class="form-control">
                                        <option value="0">N/A</option>
                                        <option v-for="type in types" v-bind:value="type.id">{{ type.service_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Package</label>
                                    <select v-model="newService.service_package_id" class="form-control">
                                        <option value="0">N/A</option>
                                        <option v-for="package in packages" v-bind:value="package.id">{{ package.package_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Service Minutes</label>
                                    <input type="number" class="form-control" v-model="newService.service_minutes" />
                                </div>
                            </div>
                            <div class="col-md-3">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newService.id==0" @click="addService($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateService($event)" data-loading-text="Updating..." class="btn green">Save</button>
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

    export default {
        name: 'ServicesList',
        components:{ DataTable, UploadForm },
        data: function(){
            return {
                serviceTable:{
                    columns: [
                        { label: 'ID', field: 'id', filterable: true, type:'number' },
                        { label: 'Code', field: 'service_code', filterable: true },
                        { label: 'Service Name', field: 's_name', filterable: true },
                        { label: 'Gender',  field: 'service_gender_html', filterable: true, html: true },
                        { label: 'Price',  field: 'service_price', filterable: true, type:'decimal'},
                        { label: 'Service Time',  field: 'service_minutes', filterable: true}
                    ],
                    rowClicked: this.viewService
                },
                newService:{ }
            }
        },
        methods:{
            searchItem:function(id, type){
                let u = this;
                axios.get( this.configs.SEARCH_ITEM_API +id+'&type='+type)
                    .then(function (response) {
                        if(response.data.item_id !== undefined) {
                            if (type == 'service' && response.data.gender != null) {
                                if(!confirm("Service has found in BOSS Server ("+ response.data.item_name +"). Do you want to auto-ill fields?"))
                                    return false;

                                u.newService.service_code = response.data.item_code;
                                u.newService.service_gender = response.data.gender;
                                u.newService.service_price = response.data.price;
                            }
                        }
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
            showAddServiceModal:function(){
                this.newService = {
                    id:0,
                    search_id:0,
                    service_code:'',
                    service_gender:'female',
                    service_minutes:20,
                    service_price:100,
                    service_type_id:0,
                    service_package_id:0
                };
                $("#add-service-modal").modal("show");
            },
            viewService:function(service){
                this.newService = {
                    id:service.id,
                    search_id:service.id,
                    service_code:service.service_code,
                    service_gender:service.service_gender,
                    service_minutes:service.service_minutes,
                    service_price:service.service_price,
                    service_type_id:service.service_type_id,
                    service_package_id:service.service_package_id
                };
                $("#add-service-modal").modal("show");
            },
            addService:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.makeRequest('/api/service/addService?token=' + this.token, 'post', this.newService, function(){
                    u.$store.dispatch('services/fetchServices');
                    u.$socket.emit('refreshModel', 'services');
                    toastr.success("Service added successfully.");
                    $btn.button('reset');
                    $("#add-service-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateService:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/service/updateService?token=' + this.token, 'post', this.newService, function(){
                    u.$store.dispatch('services/fetchServices');
                    u.$socket.emit('refreshModel', 'services');
                    toastr.success("Service updated successfully.");
                    $btn.button('reset');
                    $("#add-service-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        computed:{
            services(){
                return this.$store.state.services.services.map(function(item){
                    item.s_name = item.service_name===null?item.package_name:item.service_name;
                    item.service_gender_html = item.service_gender === 'female'? '<span class="badge badge-warning">Female</span>':
                                '<span class="badge badge-info">Male</span>';
                    return item;
                });
            },
            packages(){
                return this.$store.state.services.packages;
            },
            types(){
                return this.$store.state.services.types;
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