<template>
    <div class="client-dashboard">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-body tiles">
                        <a href="../../#/plc_tracker" class="tile bg-yellow-saffron">
                            <div class="tile-body">
                                <i class="icon icon-book-open"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> PLC Tracker </div>
                            </div>
                        </a>
                        <a href="../../#/queuing" class="tile bg-red-intense">
                            <div class="tile-body">
                                <i class="icon icon-list"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Queuing </div>
                            </div>
                        </a>
                        <a href="../../#/faqs" class="tile bg-green-seagreen">
                            <div class="tile-body">
                                <i class="icon icon-bubbles"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> FAQs </div>
                            </div>
                        </a>
                        <a href="../../#/news" class="tile bg-yellow-soft">
                            <div class="tile-body">
                                <i class="icon icon-speech"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> News Feeds </div>
                            </div>
                        </a>
                        <a href="../../#/promotions" class="tile bg-purple-studio">
                            <div class="tile-body">
                                <i class="fa fa-tags"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Promotions </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'BranchSupervisorDashboard',
        data: function(){
            return {
                stats:{
                    clients:0,
                    products:0,
                    services:0,
                    appointments:0,
                    technicians:0,
                    branches:0,
                    registered:0,
                    login:0
                }
            }
        },
        methods:{
            loadStats:function(){
                let u = this;
                axios.get('/api/stats/getAdminStats')
                    .then(function (response) {
                        u.stats = {
                            clients:response.data.clients,
                            products:response.data.products,
                            services:response.data.services,
                            appointments:response.data.appointments,
                            technicians:response.data.technicians,
                            branches:response.data.branches,
                            registered:response.data.registered,
                            login:response.data.login
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted:function(){
            this.loadStats();
        }
    }
</script>