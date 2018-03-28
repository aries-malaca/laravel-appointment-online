<template>
    <div class="modal fade" id="contact-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Manage Contacts</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import DataTable from '../../tables/DataTable.vue';
    export default {
        name:'ContactManager',
        components:{ DataTable },
        props:['contacts'],
        data(){
            return {
                newContact:{}
            };
        },
        methods:{

        },
        computed:{

        }
    }
</script>