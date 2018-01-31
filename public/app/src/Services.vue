<template>
    <div class="services">
        <div class="portlet light" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Services & Products </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#services" data-toggle="tab">Services</a>
                    </li>
                    <li>
                        <a href="#service-types" data-toggle="tab">Service Types</a>
                    </li>
                    <li>
                        <a href="#service-packages" data-toggle="tab">Service Packages</a>
                    </li>
                    <li>
                        <a href="#products" data-toggle="tab">Products</a>
                    </li>
                    <li>
                        <a href="#product-groups" data-toggle="tab">Product Groups</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="services">
                        <button type="button" @click="showAddServiceModal" class="btn green-meadow">New Service</button>
                        <br/><br/>
                        <data-table title="Services" :columns="serviceTable.columns" :rows="services" :onClick="serviceTable.rowClicked"
                                    :paginate="true" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="service-types">
                        <button type="button" @click="showAddServiceTypeModal" class="btn green-meadow">New Service Type</button>
                        <br/><br/>
                        <data-table title="Service Types" :columns="serviceTypeTable.columns" :rows="serviceTypes" :paginate="true"
                                    :onClick="serviceTypeTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="service-packages">
                        <button type="button" @click="showAddServicePackageModal" class="btn green-meadow">New Package</button>
                        <br/><br/>
                        <data-table title="Packages" :columns="servicePackageTable.columns" :onClick="servicePackageTable.rowClicked"
                                    :rows="servicePackages"  styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="products">
                        <button type="button" @click="showAddProductModal" class="btn green-meadow">New Product</button>
                        <br/><br/>
                        <data-table title="Products" :columns="productTable.columns" :rows="products" :paginate="true"
                                    :onClick="productTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="product-groups">
                        <button type="button" @click="showAddProductGroupModal" class="btn green-meadow">New Product Group</button>
                        <br/><br/>
                        <data-table title="Product Groups" :columns="productGroupTable.columns" :rows="productGroups" :paginate="true"
                                    :onClick="productGroupTable.rowClicked" styleClass="table table-bordered table-hover table-striped"/>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

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
                                        <option v-for="type in serviceTypes" v-bind:value="type.id">{{ type.service_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Package</label>
                                    <select v-model="newService.service_package_id" class="form-control">
                                        <option value="0">N/A</option>
                                        <option v-for="package in servicePackages" v-bind:value="package.id">{{ package.package_name }}</option>
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
        <div class="modal fade" id="add-service-package-modal" tabindex="-1" role="basic" aria-hidden="true">
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
        <div class="modal fade" id="add-service-type-modal" tabindex="-1" role="basic" aria-hidden="true">
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
                        <div class="row" v-if="newServiceType.id != 0">
                            <div class="col-md-12">
                                <upload-form :token="token"  input_id="service_file" form_id="service_form" category="service" :param_url="'service_id='+newServiceType.id"
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

        <div class="modal fade" id="add-product-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newProduct.id==0">Add Product</h4>
                        <h4 class="modal-title" v-else>Edit Product</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">ID</label>
                                    <input type="number" @change="searchItem(newProduct.search_id,'product')" v-model="newProduct.search_id" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Code</label>
                                    <input type="text" v-model="newProduct.product_code" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="control-label">Product Name</label>
                                    <select class="form-control" v-model="newProduct.product_group_id">
                                        <option v-bind:value="group.id" v-for="group in productGroups">{{ group.product_group_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Price</label>
                                    <input type="number" v-model="newProduct.product_price" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Size</label>
                                    <input type="text" v-model="newProduct.product_size" placeholder="Ex: 30ml" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Variant</label>
                                    <input type="text" v-model="newProduct.product_variant" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newProduct.id==0" @click="addProduct($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateProduct($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="add-product-group-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newProductGroup.id==0">Add Product Group</h4>
                        <h4 class="modal-title" v-else>Edit Product Group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">Product Group Name</label>
                                    <input type="text" v-model="newProductGroup.product_group_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="control-label">Product Description</label>
                                    <textarea v-model="newProductGroup.product_description" rows="3" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label">How To use</label>
                                <textarea v-model="newProductGroup.instructions" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <br/>
                        <div class="row" v-if="newProductGroup.id !=0">
                            <div class="col-md-12">
                                <upload-form :token="token" input_id="product_file" form_id="product_form" category="product"
                                         :placeholder_image="'images/products/'+newProductGroup.product_picture"
                                         :param_url="'product_id='+newProductGroup.id" @emit_host="getProductGroups" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newProductGroup.id==0" @click="addProductGroup($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateProductGroup($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import DataTable from './components/DataTable.vue';
    import UploadForm from './components/UploadForm.vue';
    import VueSelect from "vue-select"

    export default {
        name: 'Services',
        components:{ DataTable, UploadForm, VueSelect, UnauthorizedError },
        props: ['token','configs','user'],
        data: function(){
            return {
                title: 'Services',
                products:[],
                productGroups:[],
                services:[],
                servicePackages:[],
                serviceTypes:[],
                productTable:{
                    columns: [
                        { label: 'ID', field: 'id', filterable:true, type:'number'},
                        { label: 'Product Code', field: 'product_code', filterable: true },
                        { label: 'Product Name', field: 'product_group_name', filterable: true },
                        { label: 'Size',  field: 'product_size', filterable: true },
                        { label: 'Variant',  field: 'product_variant', filterable: true },
                        { label: 'Price', field: 'product_price', filterable: true, type:'decimal' }
                    ],
                    rowClicked: this.viewProduct
                },
                productGroupTable:{
                    columns: [
                        { label: 'Photo', field: 'product_picture_html', html:true, filterable: true },
                        { label: 'Product Group Name', field: 'product_group_name', filterable: true },
                        { label: 'Description', field: 'product_description', filterable: true },
                    ],
                    rowClicked: this.viewProductGroup
                },
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
                serviceTypeTable:{
                    columns: [
                        { label: 'Photo', field: 'service_picture_html', html:true },
                        { label: 'Service Name', field: 'service_name', filterable: true },
                        { label: 'Service Description',  field: 'service_description', filterable: true }
                    ],
                    rowClicked: this.viewServiceType
                },
                servicePackageTable:{
                    columns: [
                        { label: 'Package Name', field: 'package_name', filterable: true },
                        { label: 'Services',  field: 'service_list', filterable: true }
                    ],
                    rowClicked: this.viewServicePackage
                },
                newProduct:{},
                newProductGroup:{},
                newServiceType:{},
                newServicePackage:{ },
                newService:{ }
            }
        },
        methods:{
            getServices:function(){
                let u = this;
                axios.get('/api/service/getServices')
                .then(function (response) {
                    u.services = [];
                    response.data.forEach(function(item){
                        var color = 'info';
                        if(item.service_gender=='male')
                            color = 'success';
                        else if(item.service_gender == 'female')
                            color = 'warning';

                        item.s_name = item.service_name===null?item.package_name:item.service_name;
                        item.service_gender_html = '<span class="badge badge-'+ color +'">'+item.service_gender.toUpperCase()+'</span>';
                        u.services.push(item);
                    });
                });
            },
            getProducts:function(){
                let u = this;
                axios.get('/api/product/getProducts')
                .then(function (response) {
                    u.products = [];
                    response.data.forEach(function(item){
                        item.product_picture_html = '<img src="images/products/'+item.product_picture+'" style="height:40px"/>';
                        u.products.push(item);
                    });
                });
            },
            getProductGroups:function(){
                let u = this;
                axios.get('/api/product/getProductGroups')
                    .then(function (response) {
                        u.productGroups = [];
                        response.data.forEach(function(item){
                            item.product_picture_html = '<img src="images/products/'+item.product_picture+'" style="height:40px"/>';
                            u.productGroups.push(item);
                        });
                        $("#add-product-group-modal").modal("hide");
                    });
            },
            getServicePackages:function(){
                let u = this;
                axios.get('/api/service/getServicePackages')
                    .then(function (response) {
                        u.servicePackages = [];
                        response.data.forEach(function(item){
                            item.service_picture_html = '<img src="images/services/'+item.service_picture+'" style="height:40px"/>';
                            item.package_services = JSON.parse(item.package_services);
                            u.servicePackages.push(item);
                        });
                        $("#add-service-package-modal").modal("hide");
                    });
            },
            getServiceTypes:function(){
                let u = this;
                axios.get('/api/service/getServiceTypes')
                .then(function (response) {
                    u.serviceTypes = [];
                    response.data.forEach(function(item){
                        item.service_picture_html = '<img src="images/services/'+item.service_picture+'" style="height:40px"/>';
                        u.serviceTypes.push(item);
                    });
                    $("#add-service-type-modal").modal("hide");
                });
            },
            searchItem:function(id, type){
                let u = this;
                axios.get( this.configs.SEARCH_ITEM_API +id+'&type='+type)
                .then(function (response) {
                    if(response.data.item_id !== undefined) {
                        if (type == 'product' && response.data.gender == null) {
                            if(!confirm("Product has found in BOSS Server ("+ response.data.item_name +"). Do you want to auto-ill fields?"))
                                return false;

                            u.newProduct.product_code = response.data.item_code;
                            u.newProduct.product_name = response.data.item_name;
                            u.newProduct.product_description = response.data.description;
                            u.newProduct.product_price = response.data.price;
                            return false;
                        }

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
            showAddProductModal:function(){
                this.newProduct = {
                    id:0,
                    search_id:0,
                    product_code:'',
                    product_name:'',
                    product_price:0,
                };
                $("#add-product-modal").modal("show");
            },
            showAddProductGroupModal:function(){
                this.newProductGroup = {
                    id:0,
                    product_group_name:'',
                    product_description:'',
                    product_picture:'',
                    instructions:''
                };
                $("#add-product-group-modal").modal("show");
                try{
                    $("form")[1].reset();
                }
                catch(error){}
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
            addProduct:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/product/addProduct?token=' + this.token, 'post', this.newProduct, function(){
                    u.getProducts();
                    toastr.success("Product added successfully.");
                    $btn.button('reset');
                    $("#add-product-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateProduct:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/product/updateProduct?token=' + this.token, 'post', this.newProduct, function(){
                    u.getProducts();
                    toastr.success("Product updated successfully.");
                    $btn.button('reset');
                    $("#add-product-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            addProductGroup:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/product/addProductGroup?token=' + this.token, 'post', this.newProductGroup, function(){
                    u.getProductGroups();
                    toastr.success("Product Group added successfully.");
                    $btn.button('reset');
                    $("#add-product-group-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateProductGroup:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/product/updateProductGroup?token=' + this.token, 'post', this.newProductGroup, function(){
                    u.getProductGroups();
                    toastr.success("Product Group updated successfully.");
                    $btn.button('reset');
                    $("#add-product-group-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewProduct:function(product){
                this.newProduct = {
                    id:product.id,
                    search_id:product.id,
                    product_code:product.product_code,
                    product_size:product.product_size,
                    product_variant:product.product_variant,
                    product_price:product.product_price,
                    product_group_id:product.product_group_id
                };
                $("#add-product-modal").modal("show");
            },
            viewProductGroup:function(product){
                this.newProductGroup = {
                    id:product.id,
                    product_group_name:product.product_group_name,
                    product_description:product.product_description,
                    product_picture:product.product_picture,
                    instructions:product.instructions,
                };
                $("#add-product-group-modal").modal("show");
                try{
                    $("form")[1].reset();
                }
                catch(error){}
            },
            showAddServiceTypeModal:function(){
                this.newServiceType = {
                    id:0,
                    service_name:'',
                    service_description:'',
                    service_picture:'',
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
                    u.getServiceTypes();
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
                    u.getServiceTypes();
                    toastr.success("Service Type updated successfully.");
                    $btn.button('reset');
                    $("#add-service-type-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewServiceType:function(service_type){
                this.newServiceType = {
                    id:service_type.id,
                    service_name:service_type.service_name,
                    service_description:service_type.service_description,
                    service_picture:service_type.service_picture
                };
                $("#add-service-type-modal").modal("show");
                try{
                    $("form")[0].reset();
                }
                catch(error){}
            },
            showAddServicePackageModal:function(){
                this.newServicePackage = {
                    id:0,
                    package_name:'',
                    package_services:[],
                };
                $("#add-service-package-modal").modal("show");
            },
            viewServicePackage:function(service_package){
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
                    u.getServicePackages();
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
                    u.getServicePackages();
                    toastr.success("Service Package updated successfully.");
                    $btn.button('reset');
                    $("#add-service-package-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
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
                    u.getServices();
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
                    u.getServices();
                    toastr.success("Service updated successfully.");
                    $btn.button('reset');
                    $("#add-service-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            getServiceName:function(id){
                for(var x=0;x<this.serviceTypes.length;x++){
                    if(id == this.serviceTypes[x].id)
                        return this.serviceTypes[x].service_name;
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
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getProducts();
            this.getServices();
            this.getProductGroups();
            this.getServiceTypes();
            this.getServicePackages();
        },
        computed:{
            service_selection:function(){
                var a = [];
                this.serviceTypes.forEach(function(item){
                    a.push({label:item.service_name, value:item.id});
                });
                return a;
            }
        }
    }
</script>