<template>
    <div id="wallet">
        <div class="portlet light" v-if="user.is_client === 1">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">

            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>

</template>

<script>
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    export default {
        name: 'PromoWallet',
        components:{ UnauthorizedError },
        props:['user','configs','token'],
        data: function(){
            return {
                title: 'Promo Wallet',
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