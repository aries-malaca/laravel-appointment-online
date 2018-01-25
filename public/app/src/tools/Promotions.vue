<template>
    <div class="calendar">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#promos" data-toggle="tab">Promos</a>
                    </li>
                    <li>
                        <a href="#perks" data-toggle="tab">Perks</a>
                    </li>
                    <li>
                        <a href="#surveys" data-toggle="tab">Surveys</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="promos">
                        <button type="button" class="btn green-meadow" @click="showAddPromoModal">New Promo</button>
                        <br/><br/>
                        <data-table :columns="promotionTable.columns" :rows="promotions" :paginate="true"
                                    :onClick="promotionTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                    <div class="tab-pane" id="perks">
                        <button type="button" class="btn green-meadow" @click="showAddPerkModal">New Perk</button>
                        <br/><br/>
                        <div class="row">
                            <div v-for="perk in perks" :class="perk.perk_data.classname" >
                                <img :src="'../../images/perks/'+perk.perk_picture" alt="" class="img-responsive"
                                     style="margin-bottom:25px; cursor: pointer;" @click="viewPerk(perk)">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="surveys">
                        <br/><br/>
                        <data-table :columns="surveyTable.columns" :rows="surveys" :paginate="true"
                                    :onClick="surveyTable.rowClicked" styleClass="table table-bordered table-hover table-striped" />
                    </div>
                </div>
            </div>
        </div>
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" v-model="newPromotion.type">
                                        <option value="display">Display</option>
                                        <option value="promo">Promo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" v-if="newPromotion.id != 0">
                                <upload-form :token="token" input_id="promo_file" form_id="promo_form" category="promotion"
                                 :placeholder_image="'images/promotions/'+newPromotion.promo_picture"
                                 :param_url="'promo_id='+newPromotion.id" @emit_host="getPromos" />
                            </div>
                            <div class="col-md-4" v-if="newPromotion.type != 'display'">
                                <div class="form-group">
                                    <label>Date Start</label>
                                    <input type="date" v-model="newPromotion.date_start" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="newPromotion.type != 'display'">
                                <div class="form-group">
                                    <label>Date End</label>
                                    <input type="date" v-model="newPromotion.date_end" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="newPromotion.type != 'display'">
                            <div class="col-md-12">
                                <div name="summernote" id="summernote_1"></div>
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

        <div class="modal fade" id="add-perk-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newPerk.id==0">Add Perk</h4>
                        <h4 class="modal-title" v-else>Edit Perk</h4>
                    </div>
                    <div class="modal-body" v-if="newPerk.perk_data !== undefined">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Perk Name</label>
                                    <input type="text" v-model="newPerk.perk_name" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" v-model="newPerk.perk_description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Grid Type</label>
                                <select class="form-control" v-model="newPerk.perk_data.classname">
                                    <option value="col-sm-4">col-sm-4</option>
                                    <option value="col-sm-3">col-sm-3</option>
                                </select>
                            </div>
                            <div class="col-md-6" v-if="newPerk.id != 0">
                                <upload-form :token="token" input_id="perk_file" form_id="perk_form" category="promotion"
                                             :placeholder_image="'images/perks/'+newPerk.perk_picture"
                                             :param_url="'perk_id='+newPerk.id" @emit_host="getPerks" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newPerk.id==0" @click="addPerk($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updatePerk($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from '../components/DataTable.vue';
    import UploadForm from '../components/UploadForm.vue';

    export default {
        name: 'Promotions',
        components:{ DataTable, UploadForm },
        props: ['token','configs','user'],
        data: function(){
            return {
                title: 'Promotions',
                promotions:[],
                perks:[],
                surveys:[],
                branches:[],
                newPromotion:{},
                newPerk:{},
                promotionTable:{
                    columns: [
                        { label: 'Title', field: 'title', filterable: true },
                        { label: 'Type', field: 'type', filterable: true },
                        { label: 'Posted By', field: 'posted_by_name', filterable: true },
                    ],
                    rowClicked: this.viewPromotion,
                },
                surveyTable:{
                    columns: [
                        { label: 'Title', field: 'title', filterable: true },
                    ],
                    rowClicked: this.viewSurvey,
                },
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
            getPerks:function () {
                this.getData('/api/promotion/getPerks', 'perks');
                $("#add-perk-modal").modal("hide");
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
                    branches:[0]
                };
                $("#summernote_1").summernote({height:200});
                $("#add-promo-modal").modal("show");
            },
            showAddPerkModal:function(){
                this.newPerk = {
                    id:0,
                    perk_name:'',
                    perk_description:'',
                    perk_picture:'',
                    perk_order:0,
                    perk_data:{
                        classname:'col-sm-4'
                    }
                };
                $("#add-perk-modal").modal("show");
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
                    branches:[0]
                };
                setTimeout(function(){
                    $("#summernote_1").summernote({height:200}).summernote('code', promotion.description);
                },200);
                try{
                    $("form")[0].reset();
                }
                catch(error){}

            },
            viewPerk:function(perk){
                this.newPerk = {
                    id:perk.id,
                    perk_name:perk.perk_name,
                    perk_description:perk.perk_description,
                    perk_picture:perk.perk_picture,
                    perk_order:perk.perk_order,
                    perk_data:{
                        classname:perk.perk_data.classname
                    }
                };

                try{
                    $("form")[1].reset();
                }
                catch(error){}
                $("#add-perk-modal").modal("show");
            },
            viewSurvey:function(survey){

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

                this.makeRequest('/api/promotion/updatePromotion?token=' + this.token, 'patch', this.newPromotion, function(){
                    u.getPromos();
                    toastr.success("Promo updated successfully.");
                    $btn.button('reset');
                    $("#add-promo-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            addPerk:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/promotion/addPerk?token=' + this.token, 'post', this.newPerk, function(){
                    u.getPerks();
                    toastr.success("Perk added successfully.");
                    $btn.button('reset');
                    $("#add-perk-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updatePerk:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/promotion/updatePerk?token=' + this.token, 'patch', this.newPerk, function(){
                    u.getPerks();
                    toastr.success("Perk updated successfully.");
                    $btn.button('reset');
                    $("#add-perk-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getPromos();
            this.getPerks();
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
        }
    }
</script>
<style>
    .vm-editor{
        min-width: 500px !important;
    }
</style>