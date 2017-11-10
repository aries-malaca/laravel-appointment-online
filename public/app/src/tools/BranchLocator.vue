<template>
    <div class="branch-locator">
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
                <div class="alert alert-warning" v-show="!show_map">
                    Please Allow site the access your location first.
                </div>
            </div>
        </div>
        <booking-modal :toggle="toggle" :default_branch="default_branch" :lock_branch="true" :default_client="client" :lock_client="true"
                       :branches="branches" :token="token" :user="user" :configs="configs" />
    </div>
</template>

<script>
    import BookingModal from "../modals/BookingModal.vue";

    export default {
        name: 'BranchLocator',
        props:['configs','user','token'],
        components:{ BookingModal },
        data: function(){
            return {
                title: 'Branch Locator',
                map:undefined,
                geolocation:{
                    lat:0,
                    lng:0
                },
                branches:[],
                show_map:false,
                markers:[],
                infos:[],
                filter_nearby:true,
                toggle:false,
                client:{},
                default_branch:{},
                current_window:false
            }
        },
        methods:{
            getBranches:function() {
                let u = this;
                axios.get('/api/branch/getBranches/active')
                    .then(function (response) {
                        u.branches = response.data;
                    });
            },
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
                for (var i = 0; i < this.markers.length; i++) {
                    this.markers[i].setMap(null);
                }
                this.markers = [];
            },
            initializeMap:function(){
                let u = this;
                setTimeout(function(){
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            u.geolocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            u.map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 12,
                                center: u.geolocation
                            });

                            u.getBranches();
                            u.show_map = true;
                        });
                    }
                    else{
                        u.show_map = false;
                    }
                },1000);
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
                            icon: 'http://lay-bare.com/wp-content/uploads/2016/11/marker3.png'
                        }));

                        let branch = u.nearbyBranches[x];
                        let map = u.map;
                        let marker =  u.markers[x];

                        let info = new google.maps.InfoWindow({
                            content: '<h3><b>'+ branch.branch_name +' </b></h3>' +
                            '<p>Address: '+ branch.branch_address +'<br/>' +
                            'Phone: '+ branch.branch_contact +'</p>' +
                            '<button class="btn btn-success btn-md" id="btn-book">Book Appointment</button> &nbsp' +
                            '<button class="btn btn-info btn-md">View Queue</button>'
                        });


                        u.markers[x].addListener('click', function(){
                            if(u.current_window)
                                u.current_window.close();

                            u.current_window = info;
                            info.open(map, marker);
                            setTimeout(function(){
                                $('#btn-book').click(function(){
                                    u.default_branch = {
                                        branch_address:branch.branch_address,
                                        branch_data:branch.branch_data,
                                        label:branch.branch_name,
                                        rooms:branch.rooms,
                                        schedules:branch.schedules,
                                        value:branch.id,
                                    };
                                    u.toggle = !u.toggle;
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
            this.$emit('update_title', this.title);
            this.$emit('update_user');
            this.initializeMap();
            this.initializeMarkers();
        },
        computed:{
            nearbyBranches:function(){
                if(!this.filter_nearby)
                    return this.branches;

                var b = this.branches.sort(function(a, b) {
                    return a.distance - b.distance;
                });

                var branches = [];
                for(var x=0;x<b.length;x++){
                   if(this.branches[x].distance<=this.configs.NEARBY_BRANCH_DISTANCE && branches.length < 10)
                       branches.push(this.branches[x]);
                }
                return branches
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
            user:function(){
                this.client = {
                    label:this.user.username,
                    value:this.user.id,
                    gender:this.user.gender,
                    user_mobile:this.user.user_mobile,
                    picture_html_big:this.user.picture_html_big,
                };
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