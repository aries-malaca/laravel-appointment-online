<template>
    <div class="branch-locator">
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
                <div class="row" v-show="show_map">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Show: </label>
                            <select class="form-control" v-model="filter_nearby" style="width:300px">
                                <option :value="true">Nearby Branches</option>
                                <option :value="false">All Branches</option>
                            </select>
                        </div>
                        <div id="map"></div>
                    </div>
                </div>
                <div class="alert alert-info" v-show="!show_map">
                    Please allow the System to access your location first. <button class="btn btn-success btn-xs" @click="initializeMap">Proceed</button>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>

        <booking-modal :toggle="toggle" :default_branch="default_branch" :lock_branch="true" :default_client="client"
                       :lock_client="true"/>
    </div>
</template>

<script>
    import BookingModal from "../booking/BookingModal.vue";
    import UnauthorizedError from '../errors/UnauthorizedError.vue';

    export default {
        name: 'BranchLocator',
        components:{ BookingModal, UnauthorizedError },
        data: function(){
            return {
                title: 'Branch Locator',
                map:undefined,
                geolocation:{
                    lat:0,
                    lng:0
                },
                show_map:false,
                markers:[],
                infos:[],
                filter_nearby:true,
                client:{},
                default_branch:{},
                current_window:false,
                toggle:false,
            }
        },
        methods:{
            getDistance:function(coordinates){
                let mylat= this.geolocation.lat;
                let mylong = this.geolocation.lng;
                let marker_lat = coordinates.lat;
                let marker_long = coordinates.long;

               return ( 3959 * Math.acos( Math.cos( mylat *(Math.PI/180) ) *
                    Math.cos( marker_lat *(Math.PI/180) ) *
                    Math.cos( marker_long *(Math.PI/180)  - mylong *(Math.PI/180) ) +
                    Math.sin( mylat *(Math.PI/180) ) * Math.sin( marker_lat *(Math.PI/180)) ) );
            },
            clearMarkers:function(){
                for (var i = 0; i < this.markers.length; i++)
                    this.markers[i].setMap(null);

                this.markers = [];
            },
            initializeMap:function(){
                this.$store.dispatch('saveLocation');
                let u = this;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var geocoder = new google.maps.Geocoder;
                        geocoder.geocode({'location': { lat: position.coords.latitude, lng: position.coords.longitude}},
                            function(results, status) {
                                if (status === 'OK' && results.lat === undefined) {
                                    axios({url:'/api/user/saveLocation?token=' + u.token, method:'post', data:{ geolocation:results, coordinates: { lat: position.coords.latitude, lng: position.coords.longitude} }})
                                        .then(function () {
                                            u.$store.dispatch('fetchAuthenticatedUser');
                                        });
                                }
                            });
                    });
                }

                setTimeout(()=>{
                    if(this.token_location !== false)
                        u.geolocation = {
                            lat: this.token_location.lat,
                            lng: this.token_location.lng
                        };
                    if(u.geolocation.lat===0 && u.geolocation.lng===0)
                        u.geolocation = {
                            lat: 14.596970599999999,
                            lng: 121.03923239999999
                        };
                    u.map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 12,
                        center: u.geolocation
                    });

                    u.show_map = true;
                    u.initializeMarkers();
                },4000);
            },
            initializeMarkers:function(){
                this.clearMarkers();
                let u = this;
                setTimeout(function(){
                    for(var x=0;x<u.nearbyBranches.length;x++){
                        u.markers.push(new google.maps.Marker({
                            position: {
                                lat: u.nearbyBranches[x].map_coordinates.lat,
                                lng: u.nearbyBranches[x].map_coordinates.long
                            },
                            map: u.map,
                            animation: google.maps.Animation.DROP,
                            icon: '../../images/pointer.png'
                        }));

                        let branch = u.nearbyBranches[x];
                        let map = u.map;
                        let marker =  u.markers[x];

                        let info = new google.maps.InfoWindow({
                            content: '<a href="../../#/branches/'+ branch.id  +'" target="_blank"><h3><b>'+ branch.branch_name +' </b></h3></a>' +
                            '<p>Address: '+ branch.branch_address +'<br/>' +
                            'Phone: '+ branch.branch_contact +'</p>' +
                            '<button class="btn btn-success btn-md" id="btn-book">Book Appointment</button> &nbsp' +
                            '<a target="_blank" class="btn btn-info btn-md" href="../../queuing/web/'+ branch.id +'">View Queue</a>&nbsp'+
                            '<button class="btn btn-warning btn-md" id="btn-message">Message</button>'
                        });

                        u.markers[x].addListener('click', function(){
                            if(u.current_window)
                                u.current_window.close();

                            u.current_window = info;
                            info.open(map, marker);
                            setTimeout(function(){
                                $('#btn-book').unbind( "click" );
                                $('#btn-book').click(function(){
                                    u.default_branch = {
                                        branch_address:branch.branch_address,
                                        branch_data:branch.branch_data,
                                        cluster_data:branch.cluster_data,
                                        label:branch.branch_name,
                                        rooms:branch.rooms,
                                        schedules:branch.schedules,
                                        products:branch.products,
                                        services:branch.services,
                                        value:branch.id,
                                    };
                                    u.toggle = !u.toggle;
                                });
                                $('#btn-message').unbind( "click" );
                                $('#btn-message').click(function(){
                                    axios.get('../../api/branch/getBranchSupervisor/' + branch.id)
                                        .then(function (response) {
                                            if(u.partner===false){
                                                u.$store.commit('messages/updatePartner', false);
                                            }

                                            if(response.data === false)
                                                toastr.error("No available receptionist for this branch.");
                                            else{
                                                u.$store.commit('messages/toggleVisibility', true);
                                                $("body").addClass("page-quick-sidebar-open");
                                                u.$store.commit('messages/updatePartnerByID', response.data.id);
                                            }
                                        });
                                });
                            }, 50);
                        });
                    }
                    google.maps.event.trigger(u.map, 'resize');
                    u.map.setCenter(u.geolocation);
                }, 2000);
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Branch Locator');
            this.client = {
                label:this.user.username,
                value:this.user.id,
                gender:this.user.gender,
                user_mobile:this.user.user_mobile,
                picture_html_big:this.user.picture_html_big,
            };

        },
        computed:{
            nearbyBranches:function(){
                let u = this;
                if(!this.filter_nearby)
                    return this.branches;

                var b = this.branches.sort(function(a, b) {
                    return u.getDistance(a.map_coordinates) - u.getDistance(b.map_coordinates);
                });

                var branches = [];
                for(var x=0;x<b.length;x++){
                   if(u.getDistance(this.branches[x].map_coordinates)<=this.configs.NEARBY_BRANCH_DISTANCE && branches.length < 10)
                       branches.push(this.branches[x]);
                }
                return branches
            },
            user(){
                return this.$store.state.user;
            },
            partner(){
                return this.$store.state.messages.partner;
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
            token_location(){
                var e = false;
                let u = this;
                this.user.device_data.forEach((token)=>{
                    if(u.token === token.token){
                        if(token.coordinates !== undefined)
                            e = token.coordinates;
                    }
                });
                return e;
            }
        },
        watch:{
            filter_nearby:function(){
                this.initializeMarkers();
            },
            branches:function(){
                for(var x=0;x<this.branches.length;x++)
                    this.branches[x].distance = this.getDistance(this.branches[x].map_coordinates);
            },
            geolocation:function(){
                new google.maps.Marker({
                    position: this.geolocation,
                    map: this.map,
                    animation: google.maps.Animation.DROP
                })
            }
        }
    }
</script>
<style>
    #map {
        height: 400px;
    }
</style>