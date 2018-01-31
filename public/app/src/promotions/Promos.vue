<template>
    <div class="tab-pane active" id="promos">
        <button type="button" class="btn green-meadow" @click="showAddPromoModal">New Promo</button>
        <br/><br/>
        <data-table :columns="promotionTable.columns" :rows="promotions" :paginate="true"
                    :onClick="promotionTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />

        <div class="modal fade" id="add-promo-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newPromotion.id==0">Add Promo</h4>
                        <h4 class="modal-title" v-else>Edit Promo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" v-model="newPromotion.title" class="form-control"/>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" v-if="newPromotion.type != 'display'">
                                        <div class="form-group">
                                            <label>Date Start</label>
                                            <input type="date" v-model="newPromotion.date_start" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="newPromotion.type != 'display'">
                                        <div class="form-group">
                                            <label>Date End</label>
                                            <input type="date" v-model="newPromotion.date_end" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Branches</label>
                                            <vue-select multiple v-model="newPromotion.branches" :options="branch_selection"></vue-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" v-model="newPromotion.type">
                                        <option value="display">Display</option>
                                        <option value="promo">Promo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="newPromotion.id != 0">
                                <label>Picture</label>
                                <upload-form :token="token" input_id="promo_file" form_id="promo_form" category="promotion"
                                             :placeholder_image="'images/promotions/'+newPromotion.promo_picture"
                                             :param_url="'promo_id='+newPromotion.id" @emit_host="getPromos" />
                            </div>
                        </div>
                        <div class="row" v-if="newPromotion.type != 'display'">
                            <div class="col-md-12">
                                <label>Description</label>
                                <div name="summernote" id="summernote_1"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h3>
                                    Promo Contents
                                    <button class="btn btn-info" @click="addPromoData">+</button>
                                </h3>
                                <table class="table table-responsive table-hover table-stripped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Promo Type</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="d in newPromotion.promo_data">
                                        <td style="width:100px">{{ d.promo_type }}</td>
                                        <td>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newPromotion.id==0" @click="addPromo($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updatePromo($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueSelect from "vue-select"
    import DataTable from '../components/DataTable.vue';
    import UploadForm from '../components/UploadForm.vue';
    export default {
        name: 'Promos',
        components:{ VueSelect, DataTable, UploadForm},
        props: ['token','configs','user'],
        data: function(){
            return {
                promotions:[],
                promotionTable:{
                    columns: [
                        { label: 'Title', field: 'title', filterable: true },
                        { label: 'Type', field: 'type', filterable: true },
                        { label: 'Posted By', field: 'posted_by_name', filterable: true },
                    ],
                    rowClicked: this.viewPromotion,
                },
                branches:[],
                newPromotion:{},
            }
        },
        methods:{
            getHtml: function (data) {
                this.newPromotion.description = data;
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
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = [];
                        response.data.forEach(function(item){
                            u[field].push(item);
                        });
                    });
            },
            getPromos:function () {
                this.getData('/api/promotion/getPromotions', 'promotions');
                $("#add-promo-modal").modal("hide");
            },
            getBranches:function () {
                this.getData('/api/branch/getBranches', 'branches');
            },
            showAddPromoModal:function(){
                this.newPromotion = {
                    id:0,
                    type:'display',
                    title:'',
                    description:'',
                    promo_picture:'',
                    date_start:moment().format("YYYY-MM-DD"),
                    date_end:moment().format("YYYY-MM-DD"),
                    branches:[],
                    promo_data:[],
                };
                $("#summernote_1").summernote({height:200});
                $("#add-promo-modal").modal("show");
            },
            viewPromotion:function(promotion){
                $("#add-promo-modal").modal("show");
                this.newPromotion = {
                    id:promotion.id,
                    title:promotion.title,
                    type:promotion.type,
                    description:promotion.description,
                    promo_picture:promotion.promo_picture,
                    date_start:moment().format("YYYY-MM-DD"),
                    date_end:moment().format("YYYY-MM-DD"),
                    branches:[],
                    promo_data:[]
                };
                setTimeout(function(){
                    $("#summernote_1").summernote({height:200}).summernote('code', promotion.description);
                },200);
                try{
                    $("form")[0].reset();
                }
                catch(error){}

                for(var x=0;x<promotion.branches.length;x++){
                    this.newPromotion.branches.push({
                        label:this.getBranchName(promotion.branches[x]),
                        value:promotion.branches[x]
                    });
                }
            },
            addPromo:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                var desc = $("#summernote_1").summernote('code');

                if(desc.context === undefined)
                    this.newPromotion.description = desc;

                this.makeRequest('/api/promotion/addPromotion?token=' + this.token, 'post', this.newPromotion, function(){
                    u.getPromos();
                    toastr.success("Promo added successfully.");
                    $btn.button('reset');
                    $("#add-promo-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updatePromo:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                var desc = $("#summernote_1").summernote('code');

                if(desc.context === undefined)
                    this.newPromotion.description = desc;

                this.makeRequest('/api/promotion/updatePromotion?token=' + this.token, 'post', this.newPromotion, function(){
                    u.getPromos();
                    toastr.success("Promo updated successfully.");
                    $btn.button('reset');
                    $("#add-promo-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            getBranchName:function(id){
                if(id===0)
                    return "ALL";
                for(var x=0;x<this.branches.length;x++){
                    if(id == this.branches[x].id)
                        return this.branches[x].branch_name;
                }
            },
            addPromoData:function(){
                this.newPromotion.promo_data.push({
                    promo_type:"net_discount",
                    discount:0,
                    discount_type:"percent",
                    qualifier_operator:"AND",
                    qualifiers:[],
                });
            }
        },
        mounted:function(){
            this.getPromos();
            this.getBranches();
            $("#summernote_1").summernote({height:200}).summernote('code', "");
        },
        watch:{
            'newPromotion.type':function(){
                let u = this;
                setTimeout(function(){
                    $("#summernote_1").summernote({height:200});
                    $("#summernote_1").summernote('code', u.newPromotion.description);
                },500);
            }
        },
        computed:{
            branch_selection:function(){
                var a = [];
                a.push({label:"ALL", value:0});
                this.branches.forEach(function(item, i){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            }
        }
    }
</script>
<style>
    .vm-editor{
        min-width: 500px !important;
    }
</style>