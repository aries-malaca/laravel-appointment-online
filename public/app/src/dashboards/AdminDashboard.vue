<template>
    <div class="client-dashboard">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light">
                    <!-- STAT -->
                    <div class="row list-separated profile-stat">
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.clients }} </div>
                            <div class="uppercase profile-stat-text"> Clients </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.products }} </div>
                            <div class="uppercase profile-stat-text"> Products </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.services }} </div>
                            <div class="uppercase profile-stat-text"> Services </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.appointments }} </div>
                            <div class="uppercase profile-stat-text"> Appointments </div>
                        </div>
                    </div>
                    <div class="row list-separated profile-stat">
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.technicians }} </div>
                            <div class="uppercase profile-stat-text"> Technicians </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.branches }} </div>
                            <div class="uppercase profile-stat-text"> Branches </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.registered }} </div>
                            <div class="uppercase profile-stat-text"> Registered Today </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.login }} </div>
                            <div class="uppercase profile-stat-text"> Admin Login </div>
                        </div>
                    </div>
                </div>
                <div class="portlet light bordered">
                    <div class="portlet-body tiles">
                        <a href="../../#/plctracker" class="tile bg-yellow-saffron">
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
                        <a href="../../#/careers" class="tile bg-blue-hoki">
                            <div class="tile-body">
                                <i class="icon icon-briefcase"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Careers </div>
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
                                <i class="icon icon-speech"></i>
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
        name: 'AdminDashboard',
        props:['user','token','configs'],
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