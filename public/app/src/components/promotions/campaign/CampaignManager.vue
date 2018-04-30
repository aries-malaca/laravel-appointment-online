<template>
    <div class="tab-pane" id="campaign-manager">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Message Source:</label>
                            <select class="form-control" v-model="source">
                                <option value="template">Template</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="source === 'template'" class="col-md-8">
                        <label>Select Template:</label>
                        <div class="input-group">
                            <select class="form-control" v-model="selected_template">
                                <option v-for="template in templates" :value="template">{{template.name}}</option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn blue" type="button" @click="showTemplateModal"><i class="fa fa-folder"></i></button>
                            </span>
                        </div>
                        <br/>
                    </div>
                    <div class="col-md-8" v-else></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" v-model="title" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" v-if="source === 'custom'">
                        <label>Custom Message:</label>
                        <textarea rows="8" class="form-control sms-text" v-model="custom_message"></textarea>
                        <small>Text count: {{ custom_message.length }} </small>
                    </div>
                    <div class="col-md-12" v-else>
                        <div v-if="selected_template!==null">
                            <textarea class="form-control sms-text" v-model="selected_template.body" disabled rows="7"></textarea>
                            <small>Text count: {{ selected_template.body.length }} </small>
                        </div>
                    </div>
                </div>
                <table class="table table-condensed table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Attachments</th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="att,key in attachments">
                            <td>
                                <a :href="att" target="_blank">{{ att }}</a>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm" @click="removeFile(att,key)">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <upload-file path="/files/blast" category="campaign" @setFileName="addFile"></upload-file>
                <div class="row">
                    <div class="col-md-12">
                        <label>Send via:</label>
                        <div>
                            <label class="mt-radio">
                                <input type="radio" v-model="send_via" value="sms" checked>SMS &nbsp;
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" v-model="send_via" value="email">Email &nbsp;
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" v-model="send_via" value="sms+email">SMS+Email &nbsp;
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" v-model="send_via" value="app">Push Notification &nbsp;
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Content Control:</label>
                        <div>
                            <label>
                                <input type="checkbox" v-model="disable_content" />
                                <span>Attachment Only</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Force Sending:</label>
                        <div>
                            <label class="mt-radio">
                                <input type="radio" v-model="force_sending" :value="true" checked>Yes &nbsp;
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" v-model="force_sending" :value="false">No
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Recipient Type:</label>
                            <select class="form-control" v-model="recipient_type">
                                <option value="contacts">Contact List</option>
                                <option value="clients">Clients</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group" v-if="recipient_type === 'contacts'">
                            <label>Select Group:</label>
                            <div class="input-group">
                                <select class="form-control" v-model="selected_group">
                                    <option v-for="r in recipientGroups" :value="r">{{ r }}</option>
                                </select>
                                <span class="input-group-btn">
                                <button class="btn blue" type="button" @click="showContactModal"><i class="fa fa-folder"></i></button>
                            </span>
                            </div>
                        </div>
                        <div v-else>
                            <br/>
                            <button class="pull-right btn btn-success" @click="showFilterModal">Filter Clients</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Search:</label>
                            <input type="text" class="form-control" v-model="filter"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <br/>
                        <button class="btn btn-warning btn-sm pull-right" @click="refreshContacts"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
                <div style="overflow-y:scroll; max-height:310px" v-if="recipient_type==='contacts'">
                    <table class="table table-bordered">
                        <tbody>
                        <tr v-for="recipient, x in mappedContacts"
                            v-show="(filter==='' ||  recipient.first_name.toLowerCase().indexOf(filter) !== -1
                                ||  recipient.last_name.toLowerCase().indexOf(filter) !== -1)
                                &&  recipient.remarks===selected_group">
                            <td style="cursor:pointer">
                                <strong>{{ recipient.first_name }} {{ recipient.last_name }}</strong>
                            </td>
                            <td v-if="!recipient.sent">
                                <button class="btn btn-info btn-xs" @click="sendCampaign($event,recipient,'preview')" data-loading-text="Please Wait...">Preview</button>
                                <button class="btn btn-success btn-xs" @click="sendCampaign($event,recipient,'send')" data-loading-text="Please Wait...">Send</button>
                            </td>
                            <td v-else>
                                <span class="badge badge-success">Message Sent! </span> &nbsp;
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="overflow-y:scroll; max-height:310px" v-if="recipient_type==='clients'">
                    <table class="table table-bordered" v-if="using_filtered_client">
                        <tbody>
                        <tr v-for="recipient, x in filtered_clients" v-show="(filter==='' ||  recipient.first_name.toLowerCase().indexOf(filter) !== -1
                                ||  recipient.last_name.toLowerCase().indexOf(filter) !== -1)">
                            <td style="cursor:pointer">
                                <strong>{{ recipient.first_name }} {{ recipient.last_name }}</strong>
                            </td>
                            <td v-if="!recipient.sent">
                                <button class="btn btn-info btn-xs" @click="sendCampaign($event,recipient,'preview')" data-loading-text="Please Wait...">Preview</button>
                                <button class="btn btn-success btn-xs" @click="sendCampaign($event,recipient,'send')" data-loading-text="Please Wait...">Send</button>
                            </td>
                            <td v-else>
                                <span class="badge badge-success">Message Sent! </span> &nbsp;
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-info" v-else>Please filter the client to create list of recipients</div>
                </div>
                <div class="alert alert-info">
                    <small>
                        Selection: {{ selectionCount }} | Sent: {{ sentCount }}
                    </small>
                </div>
            </div>
        </div>
        <template-manager :templates="templates" @refresh_host="getTemplates"></template-manager>
        <contact-manager :contacts="contacts" @refresh_host="getContacts"></contact-manager>
        <div data-backdrop="static" class="modal fade" id="filter-modal" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Filter Clients</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" v-model="filter_object.gender">
                                                <option :value="null"></option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" v-model="filter_object.premier_status">
                                                <option :value="null"></option>
                                                <option :value="true">Premier</option>
                                                <option :value="false">Non-premier</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Age</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" v-model.number="filter_object.age[0]" class="form-control" />
                                            <small>Age From</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" v-model.number="filter_object.age[1]" class="form-control" />
                                            <small>Age To</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <div class="input-group" v-for="address,key in filter_object.address">
                                        <input type="text" v-model="filter_object.address[key]" class="form-control"/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger" type="button" @click="removeAddress(key)"><i class="fa fa-trash"></i></button>
                                        </span>
                                    </div>
                                    <small><a @click="addAddress">Add..</a></small>
                                </div>
                                <div class="form-group">
                                    <label>Home Branch</label>
                                    <vue-select v-model="filter_object.home_branch" multiple :options="branch_selection"></vue-select>
                                </div>
                                <button class="btn btn-success btn-block" @click="filterClients">Apply Filter</button>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Gender</th>
                                            <th>Age</th>
                                            <th>Home Branch</th>
                                            <th>Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="client in filtered_clients">
                                            <td>{{ client.first_name }}</td>
                                            <td>{{ client.last_name }}</td>
                                            <td>
                                                <span class="badge badge-info" v-if="client.gender==='male'">Male</span>
                                                <span class="badge badge-warning" v-else>Female</span>
                                            </td>
                                            <td>{{ client.age }}</td>
                                            <td>{{ client.home_branch }}</td>
                                            <td>{{ client.user_address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="useData">Use</button>
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueSelect from 'vue-select';
    import TemplateManager from './TemplateManager.vue';
    import ContactManager from './ContactManager.vue';
    import UploadFile from '../../uploader/UploadFile.vue';
    export default {
        name: 'CampaignManager',
        components:{ VueSelect, TemplateManager, ContactManager, UploadFile },
        data(){
            return{
                contacts:[],
                clients:[],
                templates:[],
                attachments:[],
                attachments_selection:[],
                source:'template',
                custom_message:'',
                selected_template:null,
                selected_group:'',
                recipient_type:'contacts',
                filter:'',
                title:'',
                send_via:'sms',
                force_sending:false,
                disable_content:false,
                newTemplate:{},
                filtered_clients:[],
                filter_object:{
                    gender:null,
                    age:[13, 70],
                    address:[],
                    home_branch:[],
                    premier_status:null
                },
                using_filtered_client:false,
            };
        },
        methods:{
            useData(){
                this.using_filtered_client = true;
                $("#filter-modal").modal("hide");
            },
            addAddress(){
              this.filter_object.address.push("");
            },
            removeAddress(key){
                this.filter_object.address.splice(key,1);
            },
            addFile(filename){
                this.attachments.push(filename);
            },
            filterClients(){
                let u = this;

                axios.post('/api/client/filterClients', this.filter_object)
                    .then(function (response) {
                        u.filtered_clients = [];
                        response.data.forEach((item)=>{
                            item.home_branch = function(){
                                for(var x=0;x<u.branches.length;x++){
                                    if(u.branches[x].id === Number(item.user_data.home_branch))
                                        return u.branches[x].branch_name;
                                }
                                return 'N/A';
                            }();
                            item.sent = false;
                            u.filtered_clients.push(item);
                        });
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            removeFile(filename, key){
                let u = this;
                axios.post('/api/campaign/removeFile?token=' + this.token, {filename:filename})
                    .then(function () {
                        u.attachments.splice(key, 1);
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            showTemplateModal(){
                $("#template-modal").modal("show");
            },
            showContactModal(){
                $("#contact-modal").modal("show");
            },
            showFilterModal(){
                $("#filter-modal").modal("show");
                this.using_filtered_client = false;
            },
            getContacts(){
                let u = this;
                axios.get('/api/contact/getContacts')
                    .then(function (response) {
                        u.contacts = response.data.map((item)=>{
                            item.mobile = item.mobiles[0];
                            item.email = item.email_addresses[0];
                            item.sent = false;
                            item.message = '';
                            return item;
                        });
                    });
            },
            getTemplates(){
                let u = this;
                axios.get('/api/campaign/getTemplates')
                    .then(function (response) {
                        u.templates = response.data;
                    });
                this.selected_template = null;
            },
            sendCampaign(event, recipient, flag){
                let $btn = $(event.target);
                $btn.button('loading');
                let u = this;
                axios.post('/api/campaign/sendCampaign?token=' + this.token, {message:this.message, recipient:recipient, send_via:this.send_via, force_sending:this.force_sending, flag:flag, title:this.title, attachments:this.attachments, disable_content:this.disable_content})
                    .then(function (response) {
                        if(flag==='preview') {
                            $btn.button('reset');
                            alert(response.data.message);
                        }
                        else{
                            $btn.button('reset');
                            toastr.success(response.data.message);
                            u.markAsSent(response.data.recipient, response.data.sent_message);

                            if(response.data.request_send_mail){
                                axios.post('/api/campaign/sendCampaign?token=' + u.token, {message:response.data.sent_message, recipient:recipient, send_via:'email', force_sending:true, flag:'send', title:response.data.title, attachments:u.attachments,  disable_content:u.disable_content})
                                    .then(function (res) {
                                        toastr.success(res.data.message);
                                    })
                                    .catch(function (error) {
                                        XHRCatcher(error);
                                    });
                            }
                        }
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            markAsSent(recipient, sent_message){
                if(this.recipient_type==='contacts'){
                    for(var x=0;x<this.contacts.length;x++){
                        if(recipient.id === this.contacts[x].id) {
                            this.contacts[x].sent = true;
                            this.contacts[x].message = sent_message;
                        }
                    }
                }
                else{
                    for(var x=0;x<this.filtered_clients.length;x++){
                        if(recipient.email === this.filtered_clients[x].email) {
                            this.filtered_clients[x].sent = true;
                            this.filtered_clients[x].message = sent_message;
                        }
                    }
                }

            },
            refreshContacts(){
                for(var x=0;x<this.contacts.length;x++)
                    this.contacts[x].sent = false;
                for(var x=0;x<this.filtered_clients.length;x++)
                    this.filtered_clients[x].sent = false;
            }
        },
        mounted(){
            this.getContacts();
            this.getTemplates();
        },
        watch:{
            selected_template(){
                this.title = this.selected_template.name;
            },
            source(){
                this.title = '';
            }
        },
        computed:{
            branches(){
                return this.$store.getters['branches/activeBranches'];
            },
            mappedContacts(){
                return this.contacts.map((item)=>{
                    item.gender = item.gender === 'female' ? 'Ms.':'Mr.';
                    return item;
                });
            },
            message(){
                return this.source === 'template'? this.selected_template:this.custom_message;
            },
            sentCount(){
                if(this.recipient_type==='contacts')
                    return this.mappedContacts.filter((item)=>{
                        return item.sent
                    }).length;
                else
                    return this.filtered_clients.filter((item)=>{
                        return item.sent
                    }).length;
            },
            checkedCount(){
                return this.mappedContacts.filter((item)=>{
                    return item.checked
                }).length;
            },
            recipientGroups(){
                let array = [];
                for(var x=0;x<this.contacts.length;x++){
                    if(array.indexOf(this.contacts[x].remarks) === -1)
                        array.push(this.contacts[x].remarks);
                }
                return array;
            },
            selectionCount(){
                let u = this;
                if(this.recipient_type==='contacts')
                    return this.mappedContacts.filter((recipient)=>{
                        return (u.filter==='' ||  recipient.first_name.toLowerCase().indexOf(u.filter) !== -1
                        ||  recipient.last_name.toLowerCase().indexOf(u.filter) !== -1)
                        &&  recipient.remarks===u.selected_group;
                    }).length;
                else
                    return this.filtered_clients.filter((recipient)=>{
                        return (u.filter==='' ||  recipient.first_name.toLowerCase().indexOf(u.filter) !== -1
                            ||  recipient.last_name.toLowerCase().indexOf(u.filter) !== -1);
                    }).length;
            },
            token(){
                return this.$store.state.token;
            },
            branch_selection:function(){
                var a = [];
                this.branches.forEach(function(item, i){
                    a.push({label:item.branch_name, value:item.id});
                });
                return a;
            },
        }
    }
</script>
<style>
    .sms-text{
        font-family: monospace;
        font-size: 12px;
    }
</style>