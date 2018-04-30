<template>
    <div class="tab-pane" id="perks">
        <button type="button" class="btn green-meadow" v-if="gate(user, 'perks', 'add')" @click="showAddPerkModal">New Perk</button>
        <br/><br/>
        <div class="row">
            <div v-for="perk in perks" :class="perk.perk_data.classname" >
                <img :src="'../../images/perks/'+perk.perk_picture" alt="" class="img-responsive"
                     style="margin-bottom:25px; cursor: pointer;" @click="viewPerk(perk)">
            </div>
        </div>
        <div data-backdrop="static" class="modal fade" id="add-perk-modal" tabindex="-1" role="basic" aria-hidden="true">
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
                                <label>Picture</label>
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
    import VueSelect from "vue-select"
    import DataTable from '../tables/DataTable.vue';
    import UploadForm from '../uploader/UploadForm.vue';

    export default {
        name: 'Perks',
        components:{ VueSelect, DataTable, UploadForm},
        data: function(){
            return {
                perks:[],
                newPerk:{},
            }
        },
        methods:{
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
            getPerks:function () {
                this.getData('/api/promotion/getPerks', 'perks');
                $("#add-perk-modal").modal("hide");
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

            viewPerk:function(perk){
                if(!this.gate(this.user, 'perks', 'add'))
                    return false;

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

                this.makeRequest('/api/promotion/updatePerk?token=' + this.token, 'post', this.newPerk, function(){
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
            this.getPerks();
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            user(){
                return this.$store.state.user;
            },
        }
    }
</script>