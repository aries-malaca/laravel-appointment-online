<template>
    <div class="tab-pane" id="product-groups">
        <button type="button" @click="showAddProductGroupModal" class="btn green-meadow">New Product Group</button>
        <br/><br/>
        <data-table :columns="productGroupTable.columns" :rows="groups" :paginate="true"
                    :onClick="productGroupTable.rowClicked" styleClass="table table-bordered table-hover table-striped"/>
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
    import DataTable from '../tables/DataTable.vue';
    import UploadForm from '../uploader/UploadForm.vue';

    export default {
        name: 'Services',
        components:{ DataTable, UploadForm },
        data: function(){
            return {
                productGroupTable:{
                    columns: [
                        { label: 'Photo', field: 'product_picture_html', html:true, filterable: true },
                        { label: 'Product Group Name', field: 'product_group_name', filterable: true },
                        { label: 'Description', field: 'product_description', filterable: true },
                    ],
                    rowClicked: this.viewProductGroup
                },
                newProductGroup:{},
            }
        },
        methods:{
            getProductGroups(){
                this.$store.dispatch('products/fetchGroups');
                this.$socket.emit('refreshModel', 'products');
                $("#add-product-group-modal").modal("hide");
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
            addProductGroup:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/product/addProductGroup?token=' + this.token, 'post', this.newProductGroup, function(){
                    u.$store.dispatch('products/fetchGroups');
                    u.$socket.emit('refreshModel', 'products');
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
                    u.$store.dispatch('products/fetchGroups');
                    u.$socket.emit('refreshModel', 'products');
                    toastr.success("Product Group updated successfully.");
                    $btn.button('reset');
                    $("#add-product-group-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
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
            }
        },
        computed:{
            groups(){
                return this.$store.state.products.groups.map(function(item){
                    item.product_picture_html = '<img src="images/products/'+item.product_picture+'" style="height:40px"/>';
                    return item;
                });
            },
            token(){
                return this.$store.state.token;
            }
        }
    }
</script>