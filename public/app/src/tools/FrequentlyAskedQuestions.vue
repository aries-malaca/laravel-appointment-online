<template>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-puzzle font-grey-gallery"></i>
                <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                <button class="btn btn-info"> Add FAQ </button>
            </div>
        </div>
        <div class="portlet-body">

        </div>

        <div class="modal fade" id="add-faq-modal" tabindex="-1" role="basic" aria-hidden="true">
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
                            <input type="text" v-model="newFAQ.question" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Answer</label>
                            <input type="text" v-model="newFAQ.answer" class="form-control" />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Answer</label>
                                    <input type="text" v-model="newFAQ.answer" class="form-control" />
                                </div>
                            </div>
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
    export default {
        name: 'FAQs',
        data: function(){
            return {
                title: 'FAQs',
            }
        },
        methods:{

        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
        }
    }
</script>