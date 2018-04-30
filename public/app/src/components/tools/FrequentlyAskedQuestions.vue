<template>
    <div>
        <div class="portlet light" v-if="user.is_client === 1 || gate(user, 'faqs', 'view')">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                    <button v-if="user.is_client !== 1 || gate(user, 'faqs', 'add')" class="btn btn-info" @click="showAddModal"> Add FAQ </button>
                </div>
            </div>
            <div class="portlet-body">
                <div class="mt-element-list">
                    <div class="mt-list-head list-news font-white bg-blue">
                        <div class="list-head-title-container">
                            <h3 class="list-title">Frequently Asked Questions</h3>
                        </div>
                    </div>
                    <div>
                        <div class="panel-group accordion" id="accordion1" style="max-height:360px;overflow-y:scroll">
                            <div class="panel panel-default" v-for="faq,key in faqs.questions">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle collapsed" data-parent="#accordion1" data-toggle="collapse"
                                           v-bind:href="'#collapse_'+faq.id" aria-expanded="false">
                                            <span>{{ faq.question }}</span>
                                        </a>
                                    </h4>
                                </div>
                                <div v-bind:id="'collapse_'+faq.id" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <p v-html="faq.answer"></p>
                                        <div class="row" v-if="user.is_client !== 1 && gate(user, 'faqs', 'update')">
                                            <div class="col-md-12">
                                                <button class="btn btn-warning btn-sm" @click="moveFAQ(1, faq)">Move Up</button>
                                                <button class="btn btn-warning btn-sm" @click="moveFAQ(0, faq)">Move Down</button>
                                                <button class="btn btn-info btn-sm" @click="editFAQ(faq)">Edit</button>
                                                <button class="btn btn-danger btn-sm" @click="deleteFAQ(faq)">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <div data-backdrop="static" class="modal fade" id="add-faq-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newFAQ.id==0">Add FAQ</h4>
                        <h4 class="modal-title" v-else>Edit FAQ</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Question</label>
                            <textarea v-model="newFAQ.question" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Answer</label>
                            <textarea v-model="newFAQ.answer" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newFAQ.id==0" @click="addFAQ($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateFAQ($event)" data-loading-text="Updating..." class="btn green">Save</button>
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
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    export default {
        name: 'FAQs',
        components:{ UnauthorizedError },
        data: function(){
            return {
                title: 'FAQs',
                newFAQ:{
                    id:0,
                    question:'',
                    answer:'',
                },
                faqs:[]
            }
        },
        methods:{
            showAddModal:function(){
                this.newFAQ = {
                    id:0,
                    question:'',
                    answer:''
                };
                $("#add-faq-modal").modal("show");
            },
            editFAQ:function(faq){
                this.newFAQ = {
                    id:faq.id,
                    question:faq.question,
                    answer:faq.answer.replace(/<\/?[^>]+(>|$)/g, "")
                };
                $("#add-faq-modal").modal("show");
            },
            addFAQ:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.makeRequest('/api/faq/addFAQ?token='+ this.token, 'post', this.newFAQ, function(){
                    u.getFAQs();
                    toastr.success("Successfully added FAQ");
                    $btn.button('reset');
                    $("#add-faq-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateFAQ:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');
                this.makeRequest('/api/faq/updateFAQ?token='+ this.token, 'post', this.newFAQ, function(){
                    u.getFAQs();
                    toastr.success("Successfully updated FAQ");
                    $btn.button('reset');
                    $("#add-faq-modal").modal("hide");
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            deleteFAQ:function(faq){
                let u = this;
                let $btn = $(event.target);
                SweetConfirmation("Are you sure you want to delete this FAQ?", function(){
                    $btn.button('loading');
                    u.makeRequest('/api/faq/deleteFAQ?token='+ u.token, 'post', faq, function(){
                        u.getFAQs();
                        toastr.success("Successfully deleted FAQ");
                        $btn.button('reset');
                    },function(error){
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
                });
            },
            moveFAQ:function(direction, faq){
                u.makeRequest('/api/faq/deleteFAQ?token='+ u.token, 'post', {id: faq.id, direction: direction } , function(){
                    u.getFAQs();
                    toastr.success("Successfully moved FAQ");
                    $btn.button('reset');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
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
            getFAQs:function(){
                let u = this;
                axios.get('/api/faq/getFAQs')
                    .then(function (response) {
                        u.faqs = response.data;
                    });
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'FAQ');
            this.getFAQs();
        },
        computed:{
            user(){
                return this.$store.state.user;
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