<template>
    <div class="tab-pane" id="places">
        <div class="row">
            <div class="col-md-6">
                <button type="button" @click="showAddRegionModal" class="btn green-meadow">New Region</button>
                <br/><br/>
                <data-table
                        title="Regions"
                        :columns="regionTable.columns"
                        :rows="regions"
                        :paginate="true"
                        :onClick="regionTable.rowClicked"
                        styleClass="table table-bordered table-hover table-striped" />
            </div>
            <div class="col-md-6">
                <button type="button" @click="showAddCityModal" class="btn green-meadow">New City</button>
                <br/><br/>
                <data-table
                        title="Cities"
                        :columns="cityTable.columns"
                        :rows="cities"
                        :paginate="true"
                        :onClick="cityTable.rowClicked"
                        styleClass="table table-bordered table-hover table-striped" />
            </div>
        </div>

        <div data-backdrop="static" class="modal fade" id="add-region-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newRegion.id==0">Add Region</h4>
                        <h4 class="modal-title" v-else>Edit Region</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Region Name</label>
                                    <input type="text" v-model="newRegion.region_name" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newRegion.id==0" @click="addRegion($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateRegion($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="add-city-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" v-if="newCity.id==0">Add City</h4>
                        <h4 class="modal-title" v-else>Edit City</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">City Name</label>
                                    <input type="text" v-model="newCity.city_name" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Region</label>
                                    <select v-model="newCity.region_id" class="form-control">
                                        <option v-for="region in regions" v-bind:value="region.id">{{ region.region_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" v-if="newCity.id==0" @click="addCity($event)" data-loading-text="Saving..." class="btn green">Save</button>
                        <button type="button" v-else @click="updateCity($event)" data-loading-text="Updating..." class="btn green">Save</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
</template>
<script>
    import VueSelect from "vue-select"
    import DataTable from '../tables/DataTable.vue';
    export default {
        name: 'Places',
        components:{ VueSelect, DataTable },
        data: function(){
            return {
                cities:[],
                regions:[],
                newRegion:{},
                newCity:{},
                cityTable:{
                    columns: [
                        { label: 'City', field: 'city_name', filterable: true },
                        { label: 'Region', field: 'region_name', filterable: true }
                    ],
                    rowClicked: this.viewCity
                },
                regionTable:{
                    columns: [ { label: 'Region', field: 'region_name', filterable: true } ],
                    rowClicked: this.viewRegion
                }
            }
        },
        methods:{
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = response.data;
                    });
            },
            getCities:function(){
                this.getData('/api/city/getCities', 'cities');
            },
            getRegions:function(){
                this.getData('/api/region/getRegions', 'regions');
            },
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            },
            viewCity:function(city){
                this.newCity = {
                    id:city.id,
                    city_name:city.city_name,
                    region_id:city.region_id
                };
                $("#add-city-modal").modal("show");
            },
            viewRegion:function(region){
                this.newRegion = {
                    id:region.id,
                    region_name:region.region_name,
                };
                $("#add-region-modal").modal("show");
            },
            updateRegion:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/region/updateRegion?token=' + this.token, 'post', this.newRegion, function(){
                    u.getRegions();
                    toastr.success("Region updated successfully.");
                    $btn.button('reset');
                    $("#add-region-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            addRegion:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/region/addRegion?token=' + this.token, 'post', this.newRegion, function(){
                    u.getRegions();
                    toastr.success("Region updated added.");
                    $btn.button('reset');
                    $("#add-region-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateCity:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/city/updateCity?token=' + this.token, 'post', this.newCity, function(){
                    u.getCities();
                    toastr.success("City updated successfully.");
                    $btn.button('reset');
                    $("#add-city-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            addCity:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/city/addCity?token=' + this.token, 'post', this.newCity, function(){
                    u.getCities();
                    toastr.success("City updated added.");
                    $btn.button('reset');
                    $("#add-city-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },

            showAddCityModal:function(){
                this.newCity = {
                    id:0,
                    city_name:'',
                    region_id:0
                };
                $("#add-city-modal").modal("show");
            },
            showAddRegionModal:function(){
                this.newRegion = {
                    id:0,
                    region_name:''
                };
                $("#add-region-modal").modal("show");
            }
        },
        mounted:function(){
            this.getRegions();
            this.getCities();
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>