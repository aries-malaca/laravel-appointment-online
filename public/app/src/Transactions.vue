<template>
    <div class="transactions">
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
                <div v-if="transactions">
                    <transactions-view :client="user" :user="user" :configs="configs" :token="token" :transactions="transactions"></transactions-view>
                </div>
                <div class="alert alert-info" v-else>
                    Please wait while we loading your transactions.
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import TransactionsView from './components/TransactionsView.vue';
    import UnauthorizedError from './errors/UnauthorizedError.vue';

    export default {
        name: 'Transactions',
        props:["configs", "user", "token", "transactions"],
        components: { TransactionsView, UnauthorizedError },
        data: function(){
            return {
                title: 'Transactions',
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.$emit('get_transactions');
        }
    }
</script>