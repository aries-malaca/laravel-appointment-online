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
                    <transactions-view :client="user" :user="user" :configs="configs" :transactions="transactions"></transactions-view>
                    <div class="alert alert-info" v-else>
                        <b>No transaction found. </b> <br/> You may request account review for us to sync your transactions. <br/><br/>
                        <button class="btn btn-success" @click="showReviewModal">Click Here</button>
                    </div>
                </div>
                <div class="alert alert-info" v-else>
                    Please wait while we loading your transactions.
                </div>
            </div>
        </div>
        <premier-review-modal :user="user" v-else :configs="configs" :token="token"></premier-review-modal>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import TransactionsView from './components/TransactionsView.vue';
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import PremierReviewModal from './modals/PremierReviewModal.vue';

    export default {
        name: 'Transactions',
        props:["configs", "user", "token", "transactions"],
        components: { TransactionsView, PremierReviewModal, UnauthorizedError },
        data: function(){
            return {
                title: 'Transactions',
            }
        },
        methods:{
            showReviewModal:function(){
                $("#premier-review-modal").modal("show");
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');

            this.$emit('get_transactions');
        }
    }
</script>