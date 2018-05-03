<template>
    <div class="tab-pane" id="products">
        <button type="button" v-if="gate(user, 'products', 'add')" @click="showAddProductModal" class="btn green-meadow">New Product</button>
        <br/><br/>
        <data-table :columns="productTable.columns" :rows="products" :paginate="true"
                    :onClick="productTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />

        <div data-backdrop="static" class="modal fade" id="add-product-modal" tabindex="-1" role="basic" aria-hidden="true">
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
                                        <option v-bind:value="group.id" v-for="group in groups">{{ group.product_group_name }}</option>
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
    </div>
</template>

<script>
    import DataTable from '../tables/DataTable.vue';

    export default {
        name: 'Products',
        components:{ DataTable },
        data: function(){
            return {
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
                newProduct:{}
            }
        },
        methods:{
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
                    u.$store.dispatch('products/fetchProducts');
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
                    u.$store.dispatch('products/fetchProducts');
                    toastr.success("Product updated successfully.");
                    $btn.button('reset');
                    $("#add-product-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            viewProduct:function(product){
                if(!this.gate(this.user, 'products', 'update'))
                    return false;

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
            }
        },
        computed:{
            products(){
                return this.$store.state.products.products;
            },
            groups(){
                return this.$store.state.products.groups;
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