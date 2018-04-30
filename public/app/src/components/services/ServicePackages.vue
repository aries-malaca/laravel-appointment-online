<template>
    <div class="tab-pane" id="service-packages">
        <button type="button" v-if="gate(user, 'services', 'add')" @click="showAddServicePackageModal" class="btn green-meadow">New Package</button>
        <br/><br/>
        <data-table :columns="servicePackageTable.columns" :onClick="servicePackageTable.rowClicked"
                    :rows="packages"  styleClass="table table-bordered table-hover table-striped" />

        <div data-backdrop="static" class="modal fade" id="add-service-package-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newServicePackage.id==0">Add Package</h4>
                        <h4 class="modal-title" v-else>Edit Package</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Package Name</label>
                                    <input type="text" v-model="newServicePackage.package_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="control-label">Services</label>
                                <vue-select multiple v-model="newServicePackage.package_services" :options="service_selection">
                                </vue-select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newServicePackage.id==0" @click="addServicePackage($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateServicePackage($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import VueSelect from "vue-select"

    export default {
        name: 'ServicePackages',
        components:{ DataTable, VueSelect },
        data: function(){
            return{
                servicePackageTable:{
                    columns: [
                        { label: 'Package Name', field: 'package_name', filterable: true },
                        { label: 'Services',  field: 'service_list', filterable: true }
                    ],
                    rowClicked: this.viewServicePackage
                },
                newServicePackage:{ },
            }
        },
        methods:{
            showAddServicePackageModal:function(){
                this.newServicePackage = {
                    id:0,
                    package_name:'',
                    package_services:[],
                };
                $("#add-service-package-modal").modal("show");
            },
            viewServicePackage:function(service_package){
                if(!this.gate(this.user, 'services', 'update'))
                    return false;

                this.newServicePackage = {
                    id:service_package.id,
                    package_name:service_package.package_name,
                    package_services:[]
                };

                for(var x=0;x<service_package.package_services.length;x++){
                    this.newServicePackage.package_services.push({
                        value:service_package.package_services[x],
                        label: this.getServiceName(service_package.package_services[x])
                    });
                }
                $("#add-service-package-modal").modal("show");
            },
            addServicePackage:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/service/addServicePackage?token=' + this.token, 'post', this.newServicePackage, function(){
                    u.$store.dispatch('services/fetchPackages');
                    u.$socket.emit('refreshModel', 'services');
                    toastr.success("Service Package added successfully.");
                    $btn.button('reset');
                    $("#add-service-package-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateServicePackage:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/service/updateServicePackage?token=' + this.token, 'post', this.newServicePackage, function(){
                    u.$store.dispatch('services/fetchPackages');
                    u.$socket.emit('refreshModel', 'services');
                    toastr.success("Service Package updated successfully.");
                    $btn.button('reset');
                    $("#add-service-package-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            getServiceName:function(id){
                for(var x=0;x<this.types.length;x++){
                    if(id === this.types[x].id)
                        return this.types[x].service_name;
                }
                return 'Unknown';
            },
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            }
        },
        computed:{
            service_selection:function(){
                var a = [];
                this.types.forEach(function(item){
                    a.push({label:item.service_name, value:item.id});
                });
                return a;
            },
            token(){
                return this.$store.state.token;
            },
            types(){
                return this.$store.state.services.types;
            },
            packages(){
                return this.$store.state.services.packages;
            },
            user(){
                return this.$store.state.user;
            }
        }
    }
</script>