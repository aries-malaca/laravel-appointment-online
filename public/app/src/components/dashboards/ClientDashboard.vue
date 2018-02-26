<template>
    <div class="client-dashboard">
        <div class="row widget-row">
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading" v-if="branch_visited.length>1">Last {{ branch_visited.length }} Branch Visited</h4>
                    <h4 class="widget-thumb-heading" v-else>Last Branch Visited</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-green icon-home"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="branch in branch_visited">{{ branch }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading">Most Service Availed</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-red icon-badge"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="service in services_availed">{{ service }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
            <div class="col-md-4">
                <!-- BEGIN WIDGET THUMB -->
                <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                    <h4 class="widget-thumb-heading">Purchased Products</h4>
                    <div class="widget-thumb-wrap">
                        <i class="widget-thumb-icon bg-purple icon-basket-loaded"></i>
                        <div class="widget-thumb-body">
                            <span class="widget-thumb-subtitle" v-for="product in purchased_products">{{ product }}</span>
                        </div>
                    </div>
                </div>
                <!-- END WIDGET THUMB -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="portlet light bordered">
                    <div class="portlet-body tiles">
                        <a href="../../#/calendar" class="tile bg-green-meadow">
                            <div class="tile-body">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Personal Calendar </div>
                            </div>
                        </a>
                        <div class="tile bg-green" @click="toggle = !toggle" >
                            <div class="tile-body">
                                <i class="fa fa-send-o"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Book Appointment </div>
                            </div>
                        </div>
                        <a class="tile bg-purple-studio" href="../../#/locator" >
                            <div class="tile-body">
                                <i class="fa fa-map-marker"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Branch Locator </div>
                            </div>
                        </a>
                        <a href="../../#/plc" class="tile bg-yellow-saffron">
                            <div class="tile-body">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Premier Loyalty Card </div>
                            </div>
                        </a>
                        <a class="tile bg-red-intense" target="_blank" v-bind:href="configs.EGIFT_LINK">
                            <div class="tile-body">
                                <i class="fa fa-gift"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> E-Gift </div>
                            </div>
                        </a>
                        <a href="../../#/wallet" class="tile bg-blue-hoki">
                            <div class="tile-body">
                                <i class="icon icon-wallet"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Promo Wallet </div>
                            </div>
                        </a>
                        <a href="../../#/testimonials" class="tile bg-yellow-soft">
                            <div class="tile-body">
                                <i class="icon icon-like"></i>
                            </div>
                            <div class="tile-object">
                                <div class="name" style="text-align:center"> Testimonials </div>
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
            <div class="col-md-4">
                <div class="mt-element-list">
                    <div class="mt-list-head list-news font-white bg-blue">
                        <div class="list-head-title-container">
                            <span class="badge badge-success pull-right" v-if="active_appointments.length>0">
                                {{ active_appointments.length }}
                            </span>
                            <h3 class="list-title">Active Appointments</h3>
                        </div>
                    </div>
                    <div class="mt-list-container list-news" style="background-color: white" v-if="active_appointments.length>0">
                        <ul>
                            <li class="mt-list-item" v-for="appointment in active_appointments">
                                <div class="list-icon-container">
                                    <a href="javascript:;" @click="viewAppointment(appointment)">
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </div>
                                <div class="list-datetime bold uppercase font-green">
                                    {{ appointment.transaction_date_formatted }}
                                    {{ appointment.transaction_time_formatted }}
                                </div>
                                <div class="list-item-content">
                                    <h3 class="uppercase">
                                        <a href="javascript:;" @click="viewAppointment(appointment)">{{ appointment.branch_name }}</a>
                                    </h3>
                                    <p>
                                        <span v-for="item in appointment.items"> {{ item.item_name }}</span>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <div class="alert alert-warning">
                            No Active Appointments
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <booking-modal :toggle="toggle" :default_branch="user.branch" :lock_branch="false" :default_client="client" :lock_client="true"
                       :branches="branches"/>

        <appointment-modal @refresh_list="refreshList"></appointment-modal>
    </div>
</template>

<script>
    import BookingModal from "../booking/BookingModal.vue";
    import AppointmentModal from "../appointment/AppointmentModal.vue";
    export default {
        name: 'ClientDashboard',
        components:{ BookingModal, AppointmentModal },
        data: function(){
            return {
                display_appointment:{},
                client:{},
                toggle:false,
                branch_visited:['Aguirre','Bicutan','Orlando'],
                services_availed:['Brazilian','Biniki','Package 3'],
                purchased_products:['Minty Breeze Feminine Wash','Ethyl Alcohol','Sweet Sakura'],
            }
        },
        methods:{
            getAppointments:function(){
                let u = this;
                var url = '/api/appointment/getAppointments/client/'+ this.user.id +'/active';

                if(this.user.is_client !== 1)
                    url = '/api/appointment/getAppointments/all/all/active';

                axios.get(url)
                    .then(function (response) {
                        var active_appointments = [];
                        response.data.forEach(function(item){
                            if(u.user.is_client !== 1 && u.user.user_data.branches.indexOf(item.branch_id) === -1)
                                return false;

                            active_appointments.push(item);
                        });
                        u.$store.commit('appointments/updateActiveAppointments', active_appointments);
                    });
            },
            viewAppointment:function(appointment) {
                this.display_appointment = appointment;
                this.$store.commit('appointments/updateViewingID', appointment.id);
                $("#appointment-modal").modal("show");
            },
            refreshList:function(){
                this.getAppointments();
            }
        },
        watch:{
            'user':function(){
                this.client = {
                    label:this.user.username,
                    value:this.user.id,
                    gender:this.user.gender,
                    user_mobile:this.user.user_mobile,
                    picture_html_big:this.user.picture_html_big,
                };
            }
        },
        mounted:function(){
            this.client = {
                label:this.user.username,
                value:this.user.id,
                gender:this.user.gender,
                user_mobile:this.user.user_mobile,
                picture_html_big:this.user.picture_html_big,
            };

            this.getAppointments();
            let u = this;

            this.$options.sockets.refreshAppointments = function(data){
                if(data.client_id !== u.user.id)
                    return false;

                u.getAppointments();
            };
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            branches(){
                return this.$store.getters['branches/activeBranches'];
            },
            active_appointments(){
                return this.$store.state.appointments.active_appointments;
            }
        }
    }
</script>