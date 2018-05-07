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
                <transactions-view :client="user" v-if="user.is_confirmed === 1"></transactions-view>
                <verify-account-alert v-else @resend="resend"></verify-account-alert>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import TransactionsView from './transactions/TransactionsView.vue';
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import VerifyAccountAlert from './transactions/VerifyAccountAlert.vue';

    export default {
        name: 'Transactions',
        components: { TransactionsView, UnauthorizedError, VerifyAccountAlert },
        data: function(){
            return {
                title: 'Transactions',
                has_sent:false
            }
        },
        methods:{
            resend(){
                this.has_sent = true;
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Transactions');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            configs(){
                return this.$store.state.configs;
            },
            transactions(){
                return this.$store.state.transactions;
            }
        }
    }
</script>