<template>
    <div class="client-dashboard">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-settings font-green-sharp"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Quick Access Menu</span>
                        </div>
                    </div>
                    <div class="portlet-body tiles">
                        <a href="../../#/plctracker" class="tile bg-green-meadow">
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
                    </div>
                </div>
            </div>
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
                            <div class="uppercase profile-stat-title"> {{ stats.new_users }} </div>
                            <div class="uppercase profile-stat-text"> Registered Today </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="uppercase profile-stat-title"> {{ stats.admin_login }} </div>
                            <div class="uppercase profile-stat-text"> Admin Login </div>
                        </div>
                    </div>
                </div>
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
                    new_users:0,
                    admin_login:0
                }
            }
        },
        methods:{
            loadStats:function(){

            }
        },
        mounted:function(){
            this.loadStats();
        }
    }
</script>