<template>
    <div class="modal fade" id="add-branch-modal" data-backdrop="static" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title" v-if="newBranch.id==0">Add Branch</h4>
                    <h4 class="modal-title" v-else>Edit Branch</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">ID(BOSS)</label>
                                <input type="number" @change="searchBranch(newBranch.search_id)"  class="form-control" v-model="newBranch.search_id" />
                            </div>
                        </div>
                        <div class="col-md-2" v-if="newBranch.branch_data!==undefined">
                            <div class="form-group">
                                <label class="control-label">ID(EMS)</label>
                                <input type="number" class="form-control" v-model="newBranch.branch_data.ems_id" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Branch Name</label>
                                <input type="text" class="form-control" v-model="newBranch.branch_name" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Branch Code</label>
                                <input type="text" class="form-control" v-model="newBranch.branch_code" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Classification</label>
                                <select class="form-control" v-model="newBranch.branch_classification">
                                    <option value="company-owned">Co-owned</option>
                                    <option value="franchised">Franchised</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Region</label>
                                <select class="form-control" @change="newBranch.city_id=undefined" v-model="newBranch.region_id">
                                    <option v-for="region in regions" v-bind:value="region.id">{{ region.region_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">City</label>
                                <select class="form-control" v-model="newBranch.city_id">
                                    <option v-if="city.region_id==newBranch.region_id" v-for="city in cities" v-bind:value="city.id">{{ city.city_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Address</label>
                                <textarea class="form-control" v-model="newBranch.branch_address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Cluster</label>
                                <select v-model="newBranch.cluster_id" class="form-control">
                                    <option v-for="cluster in clusters" v-bind:value="cluster.id">{{ cluster.cluster_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Rooms</label>
                                <input type="number" class="form-control" v-model="newBranch.rooms_count"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Payment Methods</label>
                                <input type="text" class="form-control" placeholder="Cash" v-model="newBranch.payment_methods"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input type="text" class="form-control" v-model="newBranch.branch_email" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Contact No.</label>
                                <input type="text" class="form-control" v-model="newBranch.branch_contact" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Contact Person</label>
                                <input type="text" class="form-control" v-model="newBranch.branch_contact_person" />
                            </div>
                        </div>
                        <div class="col-md-3" v-if="newBranch.social_media_accounts!==undefined">
                            <label class="control-label">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a target="_blank" v-bind:href="'//facebook.com/'+newBranch.social_media_accounts[0]" class="btn blue" type="button"><i class="fa fa-facebook-official"></i></a>
                                </span>
                                <input type="text" class="form-control" v-model="newBranch.social_media_accounts[0]"/>
                            </div>
                        </div>
                        <div class="col-md-3" v-if="newBranch.social_media_accounts!==undefined">
                            <label class="control-label">Twitter</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a target="_blank" v-bind:href="'//twitter.com/'+newBranch.social_media_accounts[1]" class="btn blue" type="button"><i class="fa fa-twitter-square"></i></a>
                                </span>
                                <input type="text" class="form-control" v-model="newBranch.social_media_accounts[1]"/>
                            </div>
                        </div>
                    </div>
                    <div class="row" @mouseout="confirmMap">
                        <div class="col-md-12">
                            <div id="map-canvas"></div>
                            <input type="hidden" id="position" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Directions</label>
                                        <textarea rows="2" class="form-control" v-model="newBranch.directions"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Welcome Message</label>
                                        <textarea rows="2" class="form-control" v-model="newBranch.welcome_message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Opening Date</label>
                                        <input type="date" class="form-control" v-model="newBranch.opening_date" />
                                    </div>
                                </div>
                                <div class="col-md-4" v-if="newBranch.branch_data !== undefined">
                                    <div class="form-group">
                                        <label class="control-label">Establishment Type</label>
                                        <select class="form-control" v-model="newBranch.branch_data.type">
                                            <option value="stand-alone">Stand Alone</option>
                                            <option value="mall">Mall</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" v-if="newBranch.branch_data !== undefined">
                                    <div class="form-group">
                                        <label class="control-label" v-if="newBranch.branch_data.type === 'stand-alone'">Extension Minutes</label>
                                        <input type="number" class="form-control" v-if="newBranch.branch_data.type === 'stand-alone'"
                                               v-model="newBranch.branch_data.extension_minutes" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h4>Branch Kiosks <button class="btn btn-info" @click="addKioskItem">+</button></h4>
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Alias</th>
                                        <th>Serial</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="kiosk,key in newBranch.kiosk_data">
                                        <td>
                                            <input v-if="!kiosk.registered" type="text" class="form-control" v-model="newBranch.kiosk_data[key].alias"/>
                                            <span v-else>{{ kiosk.alias }}</span>
                                        </td>
                                        <td>
                                            <input v-if="!kiosk.registered" type="text" class="form-control" v-model="newBranch.kiosk_data[key].serial_no"/>
                                            <span v-else>{{ kiosk.serial_no }}</span>
                                        </td>
                                        <td>
                                            <div v-if="!kiosk.registered" >
                                                <button class="btn btn-success btn-xs" @click="emitRegister(kiosk)"><i class="fa fa-check"></i></button>
                                                <button class="btn btn-danger btn-xs" @click="removeKioskItem(key)"><i class="fa fa-close"></i></button>
                                            </div>
                                            <button v-else @click="emitUnregister(kiosk)" class="btn btn-danger btn-xs"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" v-if="operation==='add'" @click="addBranch($event)" data-loading-text="Saving..." class="btn green">Save</button>
                    <button type="button" v-else @click="updateBranch($event)" data-loading-text="Updating..." class="btn green">Save</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>
<script>
    export default {
        name: 'BranchModal',
        props:['operation'],
        data: function(){
            return {
                newBranch:{
                    id:0,
                    search_id:0,
                    branch_name:'',
                    branch_code:'',
                    region_id:0,
                    city_id:0,
                    branch_address:'',
                    branch_email:'',
                    branch_contact:'',
                    branch_contact_person:'',
                    opening_date:moment().format("YYYY-MM-DD"),
                    rooms_count:1,
                    social_media_accounts:['',''],
                    directions:'',
                    map_coordinates:{
                        lat:14.5698,
                        long:121.0167
                    },
                    branch_classification:'company-owned',
                    payment_methods:'',
                    welcome_message:'',
                    branch_pictures:[],
                    kiosk_data:[],
                    cluster_id:0,
                    branch_data:{
                        ems_id:0,
                        type:'stand-alone',
                        extension_minutes:60
                    },
                },
                verifying:false,
                verifying_serial:null,
            }
        },
        methods: {
            addKioskItem(){
                this.newBranch.kiosk_data.push({
                    alias:'',
                    serial_no:'',
                    registered:false
                });
            },
            removeKioskItem(key){
                this.newBranch.kiosk_data.splice(key, 1);
            },
            emitRegister(kiosk){
                let u = this;

                this.$socket.emit('checkSerial',kiosk);
                this.verifying = true;
                this.verifying_serial = kiosk.serial_no;

                setTimeout(()=>{
                    if(u.verifying)
                        toastr.error("Verification error, device with serial:  " + kiosk.serial_no + " not online.");
                },2000)
            },
            emitUnregister(kiosk){
                let x = confirm("Are you sure you want to unregister device?");
                if(!x)
                    return false;

                let u = this;
                kiosk.branch_id = this.newBranch.id;

                axios({url: '/api/branch/unregisterKiosk?token=' + u.token , method:'post', data:kiosk})
                    .then(function () {
                        toastr.success("Successfully unregistered kiosk.");
                        u.$socket.emit('unregisterSerial',kiosk);
                        u.getBranch();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            getBranch(){
                let u = this;
                axios.get('/api/branch/getBranch/' + this.branch.id)
                    .then(function (response) {
                        if(response.data.id !== undefined){
                            u.newBranch = response.data;
                            u.newBranch.search_id = response.data.id;
                            u.newBranch.opening_date = moment(response.data.opening_date).format("YYYY-MM-DD");
                            u.pictures = u.newBranch.branch_pictures;
                            u.newBranch.branch_data = {
                                ems_id:response.data.branch_data.ems_id,
                                type:response.data.branch_data.type!==undefined?response.data.branch_data.type:'',
                                extension_minutes:response.data.branch_data.extension_minutes!==undefined?response.data.branch_data.extension_minutes:0
                            };
                        }
                    });
            },
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = [];
                        response.data.forEach(function(item){
                            if(field === 'clusters'){
                                item.services = JSON.parse(item.services);
                                item.products = JSON.parse(item.products);
                            }
                            u[field].push(item);
                        });
                    });
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
            searchBranch:function(id){
                let u = this;
                axios.get( this.configs.SEARCH_BRANCH_API +id)
                    .then(function (response) {
                        if(response.data.branch_id !== undefined){
                            if(!confirm("Branch has found in BOSS Server. Do you want to auto-ill fields?"))
                                return false;

                            u.newBranch.branch_name = response.data.branch_name;
                            u.newBranch.branch_address = response.data.address;
                            u.newBranch.branch_email = response.data.contact_person_email_address;
                        }
                    });
            },
            confirmMap:function(){
                let position = document.getElementById("position").value;
                if(position !== ''){
                    this.newBranch.map_coordinates.lat = Number(position.split(',')[0]);
                    this.newBranch.map_coordinates.long = Number(position.split(',')[1]);
                }
            },
            addBranch:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/addBranch?token=' + this.token, 'post', this.newBranch, function(){
                    toastr.success("Branch added successfully.");
                    $btn.button('reset');
                    $("#add-branch-modal").modal('hide');
                    u.$socket.emit('refreshModel', 'branches');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
            updateBranch:function(){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                this.makeRequest('/api/branch/updateBranch?token=' + this.token, 'post', this.newBranch, function(){
                    toastr.success("Branch updated successfully.");
                    $btn.button('reset');
                    u.$socket.emit('refreshModel', 'branches');
                    u.$emit('refreshBranch');
                    $("#add-branch-modal").modal('hide');
                },function(error){
                    XHRCatcher(error);
                    $btn.button('reset');
                });
            },
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
            branch(){
                return this.$store.state.branches.editing_branch;
            },
            clusters(){
                return this.$store.state.branches.clusters;
            },
            cities(){
                return this.$store.state.branches.cities;
            },
            regions(){
                return this.$store.state.branches.regions;
            }
        },
        mounted(){
            let u = this;
            this.$options.sockets.verifiedSerial = function(data){
                data.kiosk.branch_id = u.newBranch.id;
                if(u.verifying_serial === data.kiosk.serial_no){
                    u.verifying = false;

                    if(data.result){
                        axios({url: '/api/branch/registerKiosk?token=' + u.token , method:'post', data:data.kiosk})
                            .then(function () {
                                toastr.success("Successfully registered kiosk.");
                                u.getBranch();
                                u.$socket.emit('registerSerial', data.kiosk);
                            })
                            .catch(function (error) {
                                XHRCatcher(error);
                            });
                    }
                }
            };
        },
        watch:{
            branch(){
                if(this.operation === 'edit'){
                    this.getBranch();
                }
            }
        }
    }
</script>
<style>
    @media (min-width: 992px){
        .modal-lg {
            width: 958px;
        }
    }
</style>