<template>
    <div class="modal fade" id="template-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Manage Templates</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Contents</th>
                            <th>Length</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="template in templates">
                            <td>{{ template.name }}</td>
                            <td>{{ (template.body.length > 40?template.body.substr(0,39) + ' ...':template.body) }}</td>
                            <td>{{ template.body.length }}</td>
                            <td>
                                <div v-if="template.id !== newTemplate.id">
                                    <button class="btn btn-xs btn-info" @click="editTemplate(template)">Edit</button>
                                    <button class="btn btn-xs btn-danger" @click="deleteTemplate(template)">Delete</button>
                                </div>
                                <span v-else class="badge badge-warning">Editing...</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <h4 v-if="newTemplate.id===0">Add Template</h4>
                    <h4 v-else>Update Template</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" v-model="newTemplate.name" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <table class="table table-condensed table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parameters</th>
                                            <th>
                                                <button class="btn btn-info btn-xs" @click="addParameter">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="param,key in newTemplate.parameters" >
                                            <td>
                                                <input type="text" v-model="newTemplate.parameters[key]" class="form-control"/>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-xs" @click="removeParameter(key)">X</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Content</label>
                                <textarea rows="5" v-model="newTemplate.body" class="form-control"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div v-if="newTemplate.id !== 0">
                                        <button class="btn btn-warning" @click="cancelEditing">Cancel</button>
                                        <button class="btn btn-success" @click="updateTemplate" data-loading-text="Please Wait...">Update</button>
                                    </div>
                                    <button class="btn btn-success" v-else @click="addTemplate" data-loading-text="Please Wait...">Save</button>
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
    export default {
        name:'TemplateManager',
        props:['templates'],
        data(){
            return {
                newTemplate:{}
            };
        },
        methods:{
            deleteTemplate(template){
                if(confirm("Are you sure you want to delete this template?")){
                    let u = this;
                    axios.post('/api/campaign/deleteTemplate?token=' + this.token, template)
                        .then(function () {
                            toastr.success("Template has been deleted.");
                            u.$emit('refresh_host');
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                }
            },
            editTemplate(template){
                let u = this;
                this.newTemplate = {
                    id:template.id,
                    name:template.name,
                    body:template.body,
                    parameters:[]
                };
                template.parameters.forEach((item)=>{
                    u.newTemplate.parameters.push(item);
                });
            },
            addParameter(){
                this.newTemplate.parameters.push('');
            },
            removeParameter(key){
                this.newTemplate.parameters.splice(key, 1);
            },
            addTemplate(){
                let $btn = $(event.target);
                $btn.button('loading');

                let u = this;
                axios.post('/api/campaign/addTemplate?token=' + this.token, this.newTemplate)
                    .then(function () {
                        $btn.button('reset');
                        toastr.success("Template has been added.");
                        u.$emit('refresh_host');
                        u.cancelEditing();
                    })
                    .catch(function (error) {
                        $btn.button('reset');
                        XHRCatcher(error);
                    });

            },
            updateTemplate(){
                let $btn = $(event.target);
                $btn.button('loading');

                let u = this;
                axios.post('/api/campaign/updateTemplate?token=' + this.token, this.newTemplate)
                    .then(function () {
                        $btn.button('reset');
                        toastr.success("Template has been updated.");
                        u.$emit('refresh_host');
                        u.cancelEditing();
                    })
                    .catch(function (error){
                        $btn.button('reset');
                        XHRCatcher(error);
                    });
            },
            cancelEditing(){
                this.newTemplate = {
                    id:0,
                    name:'',
                    body:'',
                    parameters:[]
                }
            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            }
        },
        mounted(){
            this.cancelEditing();
        }
    }
</script>