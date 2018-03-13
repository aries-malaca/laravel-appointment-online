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
                        <select class="form-control" v-model="selected_template">
                            <option v-for="template in templates" :value="template">{{template.name}}</option>
                        </select><br/>
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
                            <textarea class="form-control sms-text" v-model="selected_template.body" disabled rows="8"></textarea>
                            <small>Text count: {{ selected_template.body.length }} </small>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
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
                                <input type="radio" v-model="send_via" value="sms+email">Both &nbsp;
                                <span></span>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Recipient Selection:</label>
                            <select class="form-control" v-model="selected_group">
                                <option v-for="r in recipientGroups" :value="r">{{ r }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Filter:</label>
                            <input type="text" class="form-control" v-model="filter"/>
                        </div>
                    </div>
                </div>
                <label>Contact List</label>
                <div style="overflow-y:scroll; max-height:290px">
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
                                <button class="btn btn-info btn-xs" @click="sendCampaign(recipient,'preview')">Preview</button>
                                <button class="btn btn-success btn-xs" @click="sendCampaign(recipient,'send')">Send</button>
                            </td>
                            <td v-else>
                                <span class="badge badge-success">Sent!</span>
                                <button class="btn btn-info btn-xs" @click="logMessage(recipient.message)">View</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info">
                    <small>
                        Selection: {{ selectionCount }} | Selected: {{ checkedCount }} | Sent: {{ sentCount }}
                    </small>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-info">Manage Contacts</button>
                        <button class="btn btn-info">Manage Templates</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'CampaignManager',
        data(){
            return{
                contacts:[],
                templates:[],
                source:'template',
                custom_message:'',
                selected_template:null,
                selected_group:'',
                filter:'',
                title:'',
                send_via:'sms',
                force_sending:false,
            };
        },
        methods:{
            logMessage(message){
                alert(message);
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
            },
            sendCampaign(recipient, flag){
                let u = this;
                axios.post('/api/campaign/sendCampaign?token=' + this.token, {message:this.message, recipient:recipient, send_via:this.send_via, force_sending:this.force_sending, flag:flag, title:this.title})
                    .then(function (response) {
                        if(flag==='preview')
                            alert(response.data.message);
                        else{
                            toastr.success(response.data.message);
                            u.markAsSent(response.data.recipient, response.data.sent_message);

                            if(response.data.request_send_mail){
                                axios.post('/api/campaign/sendCampaign?token=' + u.token, {message:response.data.sent_message, recipient:recipient, send_via:'email', force_sending:true, flag:'send', title:response.data.title})
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
                        XHRCatcher(error);
                    });
            },
            markAsSent(recipient, sent_message){
                for(var x=0;x<this.contacts.length;x++){
                    if(recipient.id === this.contacts[x].id) {
                        this.contacts[x].sent = true;
                        this.contacts[x].message = sent_message;
                    }
                }
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
                return this.mappedContacts.filter((item)=>{
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
                return this.mappedContacts.filter((recipient)=>{
                    return (u.filter==='' ||  recipient.first_name.toLowerCase().indexOf(u.filter) !== -1
                    ||  recipient.last_name.toLowerCase().indexOf(u.filter) !== -1)
                    &&  recipient.remarks===u.selected_group;
                }).length;
            },
            token(){
                return this.$store.state.token;
            }
        }
    }
</script>
<style>
    .sms-text{
        font-family: monospace;
        font-size: 12px;
    }
</style>