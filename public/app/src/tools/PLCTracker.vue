<template>
    <div id="plc-tracker">
        <div class="portlet light" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#approved" data-toggle="tab">Approved</a>
                    </li>
                    <li>
                        <a href="#processing" data-toggle="tab">Processing</a>
                    </li>
                    <li>
                        <a href="#delivery" data-toggle="tab">Delivery</a>
                    </li>
                    <li>
                        <a href="#ready" data-toggle="tab">Ready</a>
                    </li>
                    <li>
                        <a href="#denied" data-toggle="tab">Denied</a>
                    </li>
                    <li>
                        <a href="#deleted" data-toggle="tab">Deleted</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="approved">

                    </div>
                    <div class="tab-pane" id="processing">

                    </div>
                    <div class="tab-pane" id="delivery">

                    </div>
                    <div class="tab-pane" id="ready">

                    </div>
                    <div class="tab-pane" id="denied">

                    </div>
                    <div class="tab-pane" id="deleted">

                    </div>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>

</template>

<script>

    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    export default {
        name: 'PLCTracker',
        props: ['token','user','configs'],
        components:{ UnauthorizedError },
        data: function(){
            return {
                title: 'PLC Tracker',
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