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
    </div>
</template>

<script>
    export default {
        name: 'TransactionSummary',
        props:[ "transactions"],
        methods:{
            formatNumber:function(number){
                number = Number(number);
                return number.toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits:2}) // "1,234.57"
            }
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
            }
        }
    }
</script>

