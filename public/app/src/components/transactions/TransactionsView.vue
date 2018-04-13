<template>
    <div>
        <div class="row" v-if="client.transaction_data.length === 0 && is_loading">
            <div class="col-md-12">
                <div class="alert alert alert-info">
                    <h3>Loading transactions data, please wait...</h3>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-8">
                    <div class="mt-element-list">
                        <div class="mt-list-head list-news font-white bg-blue">
                            <div class="list-head-title-container">
                                <h3 class="list-title">Transaction History</h3>
                            </div>
                        </div>
                        <div v-if="client.transaction_data.length>0">
                            <div class="panel-group accordion" id="accordion1" style="max-height:360px;overflow-y:scroll">
                                <div class="panel panel-default" v-for="transaction,key in sortedTransactions">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle collapsed" data-parent="#accordion1" data-toggle="collapse" v-bind:href="'#collapse_'+key" aria-expanded="false">
                                                <span>{{ transaction.branch }} ({{ moment(transaction.date).format("MM/DD/YYYY") }})</span>
                                                <span class="pull-right"> {{ formatNumber(transaction.net_amount) }} </span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div v-bind:id="'collapse_'+key" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <table class="table table-hover table-light">
                                                        <tbody>
                                                        <tr>
                                                            <td> Transaction ID :</td>
                                                            <td> {{ transaction.transaction_id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td> Invoice :</td>
                                                            <td> {{ transaction.inv }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td> Gross Price :</td>
                                                            <td> {{ formatNumber(transaction.gross_price) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td> Discount :</td>
                                                            <td> {{ formatNumber(transaction.price_discount) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td> Net Amount :</td>
                                                            <td> {{ formatNumber(transaction.net_amount) }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-7">
                                                    <h4>Services/Products</h4>
                                                    <table class="table table-hover table-light">
                                                        <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="service in transaction.services">
                                                            <td>{{ service.item_name }}</td>
                                                            <td>
                                                                <span v-if="service.quantity ==='' "></span>
                                                                <span v-else>{{ service.quantity }}</span>
                                                            </td>
                                                            <td>
                                                                <span v-if="service.quantity ==='' "></span>
                                                                <span v-else>{{ formatNumber(service.unit_price) }}</span>
                                                            </td>
                                                            <td>
                                                                <span v-if="service.quantity ==='' "></span>
                                                                <span v-else>{{ formatNumber(service.sub_total) }}</span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-hover table-light">
                                                        <tbody>
                                                        <tr>
                                                            <td> Remarks :</td>
                                                            <td> {{ transaction.remarks }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning" v-else>
                            No transactions found for your account.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <transaction-summary :transactions="client.transaction_data"></transaction-summary>
                    <div v-if="user.is_client === 1 && this.configs.PLC_MINIMUM_TRANSACTIONS_AMOUNT !== undefined">
                        <div class="alert alert-success" v-if="net_amount >= this.configs.PLC_MINIMUM_TRANSACTIONS_AMOUNT">
                            <strong>Hi {{ client.username }}!</strong> You are qualified to apply for a <p>Premier Loyalty Card</p>
                            <br/>
                            <a class="btn btn-info btn-md" href="../../../#/plc" >Apply Here</a>
                        </div>
                        <div class="alert alert-info" v-else>
                            <b>Not reached the minimum amount </b> <br/> You may request account review for us to sync your transactions. <br/><br/>
                            <button class="btn btn-success" @click="showReviewModal">Click Here</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <premier-review-modal :user="user" :configs="configs" :token="token"></premier-review-modal>
    </div>
</template>

<script>
    import TransactionSummary from './TransactionsSummary.vue';
    import PremierReviewModal from '../plc/PremierReviewModal.vue';

    export default {
        name: 'TransactionsView',
        components:{ TransactionSummary, PremierReviewModal },
        props:["client"],
        data(){
            return{
                is_loading:false
            };
        },
        methods:{
            formatNumber:function(number){
                number = Number(number);
                return number.toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits:2}) // "1,234.57"
            },
            showReviewModal:function(){
                $("#premier-review-modal").modal("show");
            },
            getBossTransactions:function(){
                let u = this;
                if(this.configs.FETCH_BOSS_TRANSACTIONS === undefined && this.client.is_client === 1)
                    return false;

                if(this.transactions !== false && this.client.is_client === 1)
                    return false;

                if(this.client.transaction_data.length > 0){
                    var link = this.configs.FETCH_BOSS_TRANSACTIONS + this.client.email + '?size='+ this.client.transaction_data.length ;
                }
                else
                    var link = this.configs.FETCH_BOSS_TRANSACTIONS + this.client.email;

                if(this.client.user_data.boss_id !== null && this.client.user_data.boss_id !== undefined)
                    link = link + "?boss_id=" + this.client.user_data.boss_id;

                this.is_loading = true;
                axios.get(link)
                    .then(function (response) {
                        if(response.data)
                            axios.post('/api/client/updateTransactionData?token=' + u.token, {id:u.client.id, data:response.data})
                                .then(function () {
                                    u.$emit('refreshClient');
                                    u.is_loading = false;
                                })
                                .catch(function (error) {
                                    XHRCatcher(error);
                                });
                        else
                            u.$emit('refreshClient');
                    })
                    .catch(function (error) {
                        u.is_loading = false;
                        XHRCatcher(error);
                    });
            }
        },
        computed:{
            net_amount:function(){
                var net = 0;
                this.client.transaction_data.forEach((item)=>{
                    net += item.net_amount;
                });
                return net;
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            transactions(){
                return this.$store.state.transactions;
            },
            sortedTransactions(){
                return this.client.transaction_data.sort(function(b,a){
                    return (a.date > b.date) ? 1 : ((b.date > a.date) ? -1 : 0);
                } );
            }
        },
        mounted(){
            this.getBossTransactions();
        }
    }
</script>
