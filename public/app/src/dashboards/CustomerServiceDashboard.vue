<template>
    <div class="client-dashboard">
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light bordered">
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

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'CustomerServiceDashboard',
        props:['user','token','configs'],
        data: function(){
            return {
            }
        }
    }
</script>