<template>
    <div class="portlet sale-summary">
        <div class="portlet-title">
            <div class="caption font-red sbold"> Transaction Summary </div>
        </div>
        <div class="portlet-body">
            <ul class="list-unstyled">
                <li>
                    <span class="sale-info"> GROSS AMOUNT </span>
                    <span class="sale-num"> {{ formatNumber(gross_amount) }} </span>
                </li>
                <li>
                    <span class="sale-info"> DISCOUNT AMOUNT</span>
                    <span class="sale-num"> {{ formatNumber(discount_amount) }} </span>
                </li>
                <li>
                    <span class="sale-info"> NET AMOUNT </span>
                    <span class="sale-num"> {{ formatNumber(net_amount) }} </span>
                </li>
            </ul>
        </div>
        <div v-if="user.is_client === 1 && this.configs.PLC_MINIMUM_TRANSACTIONS_AMOUNT !== undefined">
            <div class="alert alert-success" v-if="net_amount >= this.configs.PLC_MINIMUM_TRANSACTIONS_AMOUNT">
                <strong>Hi {{ user.username }}!</strong> You are qualified to apply for a <p>Premier Loyalty Card</p>
                <br/>
                <a class="btn btn-info btn-md" href="../../../#/plc" >Apply Here</a>
            </div>
            <div class="alert alert-info" v-else>
                <b>Not reached the minimum amount </b> <br/> You may request account review for us to sync your transactions. <br/><br/>
                <button class="btn btn-success" @click="showReviewModal">Click Here</button>
            </div>
        </div>
        <premier-review-modal :user="user" :configs="configs" :token="token"></premier-review-modal>
    </div>
</template>

<script>
    import PremierReviewModal from '../plc/PremierReviewModal.vue';
    export default {
        name: 'TransactionSummary',
        components:{ PremierReviewModal },
        props:[ "transactions"],
        methods:{
            formatNumber:function(number){
                number = Number(number);
                return number.toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits:2}) // "1,234.57"
            },
            showReviewModal:function(){
                $("#premier-review-modal").modal("show");
            },
        },
        computed:{
            gross_amount:function(){
                var total=0;
                for(var x=0;x<this.transactions.length;x++){
                    total += Number(this.transactions[x].gross_price);
                }
                return total;
            },
            discount_amount:function(){
                var total=0;
                for(var x=0;x<this.transactions.length;x++){
                    total += Number(this.transactions[x].price_discount);
                }
                return total;
            },
            net_amount:function(){
                var total=0;
                for(var x=0;x<this.transactions.length;x++){
                    total += Number(this.transactions[x].net_amount);
                }
                return total;
            },
            configs(){
                return this.$store.state.configs;
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>

