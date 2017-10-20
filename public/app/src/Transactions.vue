<template>
    <div class="transactions">
        <div class="portlet light">
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
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel-group accordion" id="accordion1" style="max-height:400px;overflow-y:scroll">
                            <div class="panel panel-default" v-for="transaction,key in transactions">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle collapsed" data-parent="#accordion1" data-toggle="collapse" v-bind:href="'#collapse_'+key" aria-expanded="false">
                                            <span>{{ transaction.branch }}</span>
                                            <span class="pull-right">{{ transaction.date }}</span>
                                        </a>
                                    </h4>
                                </div>
                                <div v-bind:id="'collapse_'+key" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="panel-body">
                                        <div class="col-md-5">
                                            <table class="table table-hover table-light">
                                                <tbody>
                                                <tr>
                                                    <td> Transaction :</td>
                                                    <td> {{ transaction.transaction_id }}</td>
                                                </tr>
                                                <tr>
                                                    <td> Invoice :</td>
                                                    <td> {{ transaction.inv }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-7">
                                            <h4>Services</h4>
                                            <table class="table table-hover table-light">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td> Transaction :</td>
                                                        <td> {{ transaction.transaction_id }}</td>
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
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Transactions',
        props:["configs", "user", "token", "transactions"],
        data: function(){
            return {
                title: 'Transactions',
                transactions:[]
            }
        },
        methods:{

        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.getBossTransactions();
        }
    }
</script>