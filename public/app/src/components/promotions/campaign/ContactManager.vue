<template>
    <div data-backdrop="static" class="modal fade" id="contact-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Manage Contacts</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <data-table
                                    :columns="contactTable.columns"
                                    :rows="computed_contacts"
                                    :paginate="true"
                                    :onClick="contactTable.rowClicked"
                                    styleClass="table table-bordered table-hover table-striped"
                            />
                        </div>
                        <div class="col-md-4">
                            <h4 v-if="newContact.id===0">Add Contact</h4>
                            <h4 v-else>Edit Contact</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" v-model="newContact.first_name"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" v-model="newContact.last_name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" v-model="newContact.email"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" class="form-control" v-model="newContact.mobile"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select v-model="newContact.gender" class="form-control">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Group</label>
                                        <select v-model="newContact.remarks" class="form-control">
                                            <option :value="group" v-for="group in groups">{{ group }}</option>
                                            <option value="new">New Group</option>
                                        </select>
                                    </div>
                                    <div class="form-group" v-if="newContact.remarks === 'new'">
                                        <label>Specify</label>
                                        <input type="text" class="form-control" v-model="newContact.new_group"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span v-if="newContact.id !== 0">
                                        <button class="btn btn-warning" @click="cancelEditing">Cancel</button>
                                        <button class="btn btn-danger" @click="deleteContact">Delete</button>
                                    </span>
                                    <button class="btn btn-success" v-if="newContact.id===0" @click="addContact">Save</button>
                                    <button class="btn btn-success" v-else @click="updateContact">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import DataTable from '../../tables/DataTable.vue';
    export default {
        name:'ContactManager',
        components:{ DataTable },
        props:['contacts'],
        data(){
            return {
                newContact:{},
                contactTable:{
                    columns: [
                        { label: 'First Name', field: 'first_name', filterable:true },
                        { label: 'Last Name', field: 'last_name', filterable: true},
                        { label: 'Gender', field: 'gender', filterable: true},
                        { label: 'Email', field: 'email', filterable: true},
                        { label: 'Mobile', field: 'mobile', filterable: true},
                        { label: 'Group', field: 'remarks', filterable: true},
                        { label: 'Status', field: 'status', html: true, },
                    ],
                    rowClicked: this.editContact,
                }
            };
        },
        methods:{
            editContact(contact){
                this.newContact = {
                    id:contact.id,
                    first_name:contact.first_name,
                    last_name:contact.last_name,
                    gender:contact.gender==='Ms.'?'female':'male',
                    email:contact.email,
                    mobile:contact.mobile,
                    remarks:contact.remarks,
                    new_group:''
                };
            },
            addContact(){
                let u = this;
                axios.post('/api/contact/addContact?token=' + this.token, this.newContact)
                    .then(function () {
                        toastr.success("Contact has been added.");
                        u.$emit('refresh_host');
                        u.cancelEditing();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            updateContact(){
                let u = this;
                axios.post('/api/contact/updateContact?token=' + this.token, this.newContact)
                    .then(function () {
                        toastr.success("Contact has been updated.");
                        u.$emit('refresh_host');
                        u.cancelEditing();
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            deleteContact(){
                if(confirm("Are you sure you want to delete this contact?")){
                    let u = this;
                    axios.post('/api/contact/deleteContact?token=' + this.token, this.newContact)
                        .then(function () {
                            toastr.success("Contact has been deleted.");
                            u.$emit('refresh_host');
                            u.cancelEditing();
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                }
            },
            cancelEditing(){
                this.newContact = {
                    id:0,
                    first_name:'',
                    last_name:'',
                    gender:'female',
                    email:'',
                    mobile:'',
                    remarks:'new',
                    new_group:''
                };
            }
        },
        computed:{
            computed_contacts(){
                let u = this;
                return this.contacts.map((item)=>{
                    item.status = item.id !== u.newContact.id?'<span class="badge badge-info">Active</span>':'<span class="badge badge-warning">Editing</span>';
                    return item;
                });
            },
            groups(){
                var groups = [];
                for(var x=0;x<this.contacts.length;x++){
                    if(groups.indexOf(this.contacts[x].remarks) === -1)
                        groups.push(this.contacts[x].remarks);
                }
                return groups;
            },
            token(){
                return this.$store.state.token;
            }
        },
        mounted(){
            this.cancelEditing();
        }
    }
</script>