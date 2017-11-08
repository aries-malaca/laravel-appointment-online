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
    </div>
</template>

<script>
    export default {
        name: 'BranchLocator',
        props:['configs'],
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
                filter_nearby:true
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
            withinDistance:function(coordinates, name){
                var mylat= this.geolocation.lat;
                var mylong = this.geolocation.lng;
                var marker_lat = coordinates.lat;
                var marker_long = coordinates.long;

                var distance = ( 3959 * Math.acos( Math.cos( mylat *(Math.PI/180) ) *
                    Math.cos( marker_lat *(Math.PI/180) ) *
                    Math.cos( marker_long *(Math.PI/180)  - mylong *(Math.PI/180) ) +
                    Math.sin( mylat *(Math.PI/180) ) * Math.sin( marker_lat *(Math.PI/180)) ) );
                console.log(distance +" " + name);
                return distance<this.configs.NEARBY_BRANCH_DISTANCE;
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
                            '<p>Address: '+ branch.branch_address +'</p>' +
                            '<p>Phone: '+ branch.branch_contact +'</p>'
                        });

                        u.markers[x].addListener('click', function(){
                            info.open(map, marker);
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

                var branches = [];
                for(var x=0;x<this.branches.length;x++){
                   if(this.withinDistance(this.branches[x].map_coordinates,this.branches[x].branch_name ) && branches.length < 20)
                       branches.push(this.branches[x]);
                }
                return branches;
            }
        },
        watch:{
            filter_nearby:function(){
                this.initializeMarkers();
            }
        }
    }
</script>
<style>
    #map {
        height: 400px;
    }
</style>