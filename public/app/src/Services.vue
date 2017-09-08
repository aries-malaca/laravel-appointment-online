<template>
    <div class="services">
        <div class="portlet light">
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
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="services">

                    </div>
                    <div class="tab-pane" id="service-types">
                        <button type="button" @click="showAddServiceTypeModal" class="btn green-meadow">New Service Type</button>
                        <br/><br/>
                        <data-table
                                title="Service Types"
                                :columns="serviceTypeTable.columns"
                                :rows="serviceTypes"
                                :paginate="true"
                                :onClick="serviceTypeTable.rowClicked"
                                styleClass="table table-bordered table-hover table-striped"
                        />
                    </div>
                    <div class="tab-pane" id="service-packages">

                    </div>
                    <div class="tab-pane" id="products">
                        <button type="button" @click="showAddProductModal" class="btn green-meadow">New Product</button>
                        <br/><br/>
                        <data-table
                            title="Products"
                            :columns="productTable.columns"
                            :rows="products"
                            :paginate="true"
                            :onClick="productTable.rowClicked"
                            styleClass="table table-bordered table-hover table-striped"
                        />
                    </div>
                </div>
            </div>
        </div>

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
                                <form role="form" class="form" enctype="multipart/form-data" onsubmit="return false;">
                                    <div class="form-group">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                <img v-bind:src="'images/services/' + this.newServiceType.service_picture" alt="" /> </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                            <div>
                                                <span class="btn default btn-file">
                                                    <span class="fileinput-new"> Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="file" id="file2">
                                                </span>
                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                <button @click="uploadServicePicture" type="button" class="btn btn-primary">Upload</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Product Code</label>
                                    <input type="text" v-model="newProduct.product_code" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Product Name</label>
                                    <input type="text" v-model="newProduct.product_name" class="form-control" />
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
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea v-model="newProduct.product_description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="newProduct.id != 0">
                            <div class="col-md-12">
                                <form role="form" class="form" enctype="multipart/form-data" onsubmit="return false;">
                                    <div class="form-group">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                <img v-bind:src="'images/products/' + this.newProduct.product_picture" alt="" /> </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                            <div>
                                                <span class="btn default btn-file">
                                                    <span class="fileinput-new"> Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="file" id="file">
                                                </span>
                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                <button @click="uploadPicture" type="button" class="btn btn-primary">Upload</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
    </div>
</template>

<script>
    import DataTable from './components/DataTable.vue';

    export default {
        name: 'Services',
        components:{ DataTable },
        props: ['token'],
        data: function(){
            return {
                title: 'Services',
                products:[],
                services:[],
                serviceTypes:[],
                servicePackages:[],
                productTable:{
                    columns: [
                        {
                            label: 'Photo', field: 'product_picture_html', html:true
                        },
                        {
                            label: 'Product Code', field: 'product_code', filterable: true
                        },
                        {
                            label: 'Product Name',  field: 'product_name', filterable: true,
                        },
                        {
                            label: 'Description', field: 'product_description', filterable: true,
                        },
                        {
                            label: 'Price', field: 'product_price', filterable: true,
                        },
                    ],
                    rowClicked: this.viewProduct,
                },
                serviceTypeTable:{
                    columns: [
                        {
                            label: 'Photo', field: 'service_picture_html', html:true
                        },
                        {
                            label: 'Service Name', field: 'service_name', filterable: true
                        },
                        {
                            label: 'Service Description',  field: 'service_description', filterable: true,
                        }
                    ],
                    rowClicked: this.viewServiceType,
                },
                serviceTable:{
                    columns: [
                        {
                            label: 'Photo', field: 'service_picture_html', html:true
                        },
                        {
                            label: 'Service Code', field: 'service_code', filterable: true
                        },
                        {
                            label: 'Service Name', field: 'service_name', filterable: true
                        },
                        {
                            label: 'Gender', field: 'service_gender_html', filterable: true, html:true
                        },
                        {
                            label: 'Price', field: 'service_price', filterable: true
                        }
                    ],
                    rowClicked: this.viewServiceType,
                },
                newProduct:{
                    id:0,
                    product_code:'',
                    product_name:'',
                    product_description:'',
                    product_price:0,
                    product_picture:''
                },
                newServiceType:{
                    id:0,
                    service_name:'',
                    service_description:'',
                    service_picture:'',
                }
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
            },
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
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
            getServiceTypes:function(){
                let u = this;
                axios.get('/api/service/getServiceTypes')
                .then(function (response) {
                    u.serviceTypes = [];
                    response.data.forEach(function(item){
                        item.service_picture_html = '<img src="images/services/'+item.service_picture+'" style="height:40px"/>';
                        u.serviceTypes.push(item);
                    });
                });
            },
            showAddProductModal:function(){
                this.newProduct = {
                    id:0,
                    product_code:'',
                    product_name:'',
                    product_description:'',
                    product_price:0,
                    product_picture:''
                };
                $("#add-product-modal").modal("show");
                try{
                    $("form")[0].reset();
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

                this.makeRequest('/api/product/updateProduct?token=' + this.token, 'patch', this.newProduct, function(){
                    u.getProducts();
                    toastr.success("Product updated successfully.");
                    $btn.button('reset');
                    $("#add-product-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewProduct:function(product){
                this.newProduct = {
                    id:product.id,
                    product_code:product.product_code,
                    product_name:product.product_name,
                    product_description:product.product_description,
                    product_price:product.product_price,
                    product_picture:product.product_picture
                };
                $("#add-product-modal").modal("show");
                try{
                    $("form")[0].reset();
                    $("form")[1].reset();
                }
                catch(error){}
            },
            uploadPicture:function(){
                let u = this;
                let data = new FormData();
                data.append('file', $('#file')[0].files[0]);

                $.ajax({
                    url:'/api/product/uploadPicture?token='+this.token+'&product_id=' + this.newProduct.id,
                    type:'POST',
                    data:data,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success:function(){
                        u.getProducts();
                        $("#add-product-modal").modal("hide");
                    },
                    error:function (error) {
                        XHRCatcher(error);
                    }
                });
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
                    $("form")[1].reset();
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

                this.makeRequest('/api/service/updateServiceType?token=' + this.token, 'patch', this.newServiceType, function(){
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
                    $("form")[1].reset();
                }
                catch(error){}
            },
            uploadServicePicture:function(){
                let u = this;
                let data = new FormData();
                data.append('file', $('#file2')[0].files[0]);

                $.ajax({
                    url:'/api/service/uploadServicePicture?token='+this.token+'&service_id=' + this.newServiceType.id,
                    type:'POST',
                    data:data,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success:function(){
                        u.getServiceTypes();
                        $("#add-service-type-modal").modal("hide");
                    },
                    error:function (error) {
                        XHRCatcher(error);
                    }
                });
            },
        },
        mounted:function(){
            this.emit();
            this.getProducts();
            this.getServiceTypes();
        }
    }
</script>