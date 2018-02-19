<template>
    <div class="modal fade" id="add-technician-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" v-if="operation==='add'">Add Technician</h4>
                    <h4 class="modal-title" v-else>Add Technician</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" v-if="operation==='add'" @click="addTechnician($event)" data-loading-text="Saving..." class="btn green">Save</button>
                    <button type="button" v-else @click="updateTechnician($event)" data-loading-text="Saving..." class="btn green">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</template>
<script>
    export default {
        name: 'TechnicianModal',
        props:['operation'],
        data: function(){
            return {
                newTechnician:{}
            }
        },
        methods:{
            addTechnician(){

            },
            updateTechnician(){

            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            }
        }
    }
</script>