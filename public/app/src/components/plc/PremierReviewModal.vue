<template>
    <div data-backdrop="static" class="modal fade" id="premier-review-modal" tabindex="1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Premier Review Request</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs tabbable-line">
                        <li class="active">
                            <a href="#send-request" data-toggle="tab">Send Request</a>
                        </li>
                        <li>
                            <a href="#request-history" data-toggle="tab">Request History</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="send-request">
                            <div v-if="!hasPending">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Message:</label>
                                        <textarea v-model="message" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Attachment: (In JPG, JPEG, PNG format)</label>
                                            <small>Please provide your Valid ID</small>
                                            <div id="croppie"></div>
                                            <div id="upload-wrapper">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group">
                                                        <div class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                            <span class="fileinput-filename"> </span>
                                                        </div>
                                                        <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Add Attachment </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="file" id="input_id" @change="setupUploader($event)">
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success" v-if="croppie!==null" @click="sendRequest($event)">Send Request</button>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info" v-else>
                                <b>Notice:</b>
                                <p>
                                    You already have pending request for account review/merging. You'll be notified via email once
                                    your account has been merged.
                                </p>
                            </div>
                        </div>
                        <div class="tab-pane" id="request-history">
                            <div class="panel-group accordion" id="accordion3">
                                <div v-bind:class=" request.status==='completed'?'panel panel-success':'panel panel-warning'" v-for="request,key in requests">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse"
                                               data-parent="#accordion3" v-bind:href="'#collapse_'+key" aria-expanded="false">
                                                {{ request.status.toUpperCase() }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div v-bind:id="'collapse_'+key" class="panel-collapse collapse" aria-expanded="false">
                                        <div class="panel-body">
                                            <div class="row" v-if="request.valid_id_url !== null">
                                                <div class="col-md-12">
                                                    <img class="img img-responsive" v-bind:src="'../../images/ids/'+request.valid_id_url" alt="image" />
                                                </div>
                                            </div>
                                            <div class="row" v-else>
                                                <div class="col-md-12">
                                                    <table class="table table-condensed">
                                                        <thead>
                                                        <tr>
                                                            <th>BOSS ID</th>
                                                            <th>Birth Date</th>
                                                            <th>Last Branch</th>
                                                            <th>Service</th>
                                                            <th>Visited</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="account in request.plc_review_request_data.transactions">
                                                            <td>{{ account.custom_client_id }}</td>
                                                            <td>{{ account.birthdate }}</td>
                                                            <td>
                                                                <span v-if="account.last_transaction">
                                                                    {{ account.last_transaction.branch }}
                                                                </span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                            <td>
                                                                <span v-if="account.last_transaction">
                                                                    <span v-if="account.last_transaction.services.length>0">
                                                                        {{ account.last_transaction.services[0].item_name }}
                                                                    </span>
                                                                </span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                            <td>
                                                                <span v-if="account.last_transaction">
                                                                    {{ account.last_transaction.date }}
                                                                </span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-hover table-light">
                                                        <tbody>
                                                        <tr>
                                                            <td>Message: </td>
                                                            <td>{{ request.message }}</td>
                                                        </tr>
                                                        <tr v-if="request.remarks !== null">
                                                            <td>Remarks: </td>
                                                            <td>{{ request.remarks }}</td>
                                                        </tr>
                                                        <tr v-if="request.processed_date !== null">
                                                            <td>Date Processed: </td>
                                                            <td>{{ request.processed_date_formatted }}</td>
                                                        </tr>
                                                        <tr v-if="request.processed_date !== null">
                                                            <td>Processed By: </td>
                                                            <td>{{ request.updated_by }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <button class="btn btn-danger" v-if="request.valid_id_url !== null" @click="deleteRequest(request)">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>

<script>
    export default {
        name: 'PremierReviewModal',
        props:['token', 'user', 'configs'],
        data:function(){
            return {
                message:'',
                croppie:null,
                image:null,
                requests:[]
            }
        },
        computed:{
            hasPending(){
                return this.requests.filter((item)=>{
                    return item.status === 'pending';
                }).length > 0;
            }
        },
        methods:{
            setupUploader:function(e){
                let files = e.target.files || e.dataTransfer.files;

                if(this.croppie !==null){
                    this.croppie.destroy();
                    this.croppie = null;
                }

                if(files.length === 0){
                    return false;
                }

                this.createImage(files[0]);
            },
            createImage:function(file){
                var reader = new FileReader();
                var u = this;

                reader.onload = (e)=>{
                    u.image = e.target.result;
                    u.$emit('imageUploaded', e.target.result);
                };

                reader.readAsDataURL(file);
            },
            setupCroppie:function(){
                let u = this;
                let el = document.getElementById('croppie');
                this.croppie = new Croppie(el, {
                    viewport: {width:200,height:300, type:'square'},
                    boundary: {width:200,height:300},
                    showZoomer: true,
                    enableOrientation:true,
                    enableResize: true,
                });
                this.croppie.bind({
                    url: u.image,
                });
            },
            getRequests:function(){
                let u = this;
                axios.get('/api/premier/getRequests?token='+this.token)
                    .then(function (response) {
                        u.requests = response.data;
                    });
            },
            deleteRequest:function(data){
                if(!confirm("Are you sure you want to delete this item?"))
                    return false;

                let u = this;

                axios({url:'/api/premier/deleteRequest?token='+u.token, method:'post', data:data})
                    .then(function () {
                        u.getRequests();
                        toastr.success("Delete success.")
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            },
            sendRequest:function(event){
                let u = this;
                let $btn = $(event.target);
                $btn.button('loading');

                var file_type = this.image.split(';')[0];
                if(file_type == 'data:image/jpeg' || file_type == 'data:image/png' || file_type == 'data:image/gif'){
                    this.croppie.result({
                        type:'canvas',
                        format: 'jpeg',
                        size:{height:720}
                    }).then(response=>{
                        axios({url:'/api/premier/sendReviewRequest?token='+u.token, method:'post', data:{message:u.message, valid_id_url:response}})
                            .then(function () {
                                u.getRequests();
                                u.croppie.destroy();
                                u.croppie = null;
                                toastr.success("Request successfully sent.");
                                $("#premier-review-modal").modal("hide");
                                $btn.button('reset');
                            })
                            .catch(function (error) {
                                $btn.button('reset');
                                XHRCatcher(error);
                            });

                    });
                }
                else{
                    toastr.error("Invalid file format.");
                    $btn.button('reset');
                }
            },
        },
        mounted:function(){
            this.$on('imageUploaded', function(imageData){

                this.image = imageData;
                if(this.croppie !== null){
                    this.croppie.destroy();
                }

                this.setupCroppie();
            });

            this.getRequests();
        },
    }
</script>

<style>
    .croppie-container {
        width: 100%;
        height: 100%;
    }

    .croppie-container .cr-image {
        z-index: -1;
        position: absolute;
        top: 0;
        left: 0;
        transform-origin: 0 0;
        max-height: none;
        max-width: none;
    }

    .croppie-container .cr-boundary {
        position: relative;
        overflow: hidden;
        margin: 0 auto;
        z-index: 1;
        width: 100%;
        height: 100%;
    }

    .croppie-container .cr-viewport,
    .croppie-container .cr-resizer {
        position: absolute;
        border: 2px solid #fff;
        margin: auto;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        box-shadow: 0 0 2000px 2000px rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    .croppie-container .cr-resizer {
        z-index: 2;
        box-shadow: none;
        pointer-events: none;
    }

    .croppie-container .cr-resizer-vertical,
    .croppie-container .cr-resizer-horisontal {
        position: absolute;
        pointer-events: all;
    }

    .croppie-container .cr-resizer-vertical::after,
    .croppie-container .cr-resizer-horisontal::after {
        display: block;
        position: absolute;
        box-sizing: border-box;
        border: 1px solid black;
        background: #fff;
        width: 10px;
        height: 10px;
        content: '';
    }

    .croppie-container .cr-resizer-vertical {
        bottom: -5px;
        cursor: row-resize;
        width: 100%;
        height: 10px;
    }

    .croppie-container .cr-resizer-vertical::after {
        left: 50%;
        margin-left: -5px;
    }

    .croppie-container .cr-resizer-horisontal {
        right: -5px;
        cursor: col-resize;
        width: 10px;
        height: 100%;
    }

    .croppie-container .cr-resizer-horisontal::after {
        top: 50%;
        margin-top: -5px;
    }

    .croppie-container .cr-original-image {
        display: none;
    }

    .croppie-container .cr-vp-circle {
        border-radius: 50%;
    }

    .croppie-container .cr-overlay {
        z-index: 1;
        position: absolute;
        cursor: move;
        touch-action: none;
    }

    .croppie-container .cr-slider-wrap {
        width: 75%;
        margin: 15px auto;
        text-align: center;
    }

    .croppie-result {
        position: relative;
        overflow: hidden;
    }

    .croppie-result img {
        position: absolute;
    }

    .croppie-container .cr-image,
    .croppie-container .cr-overlay,
    .croppie-container .cr-viewport {
        -webkit-transform: translateZ(0);
        -moz-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
    }

    /*************************************/
    /***** STYLING RANGE INPUT ***********/
    /*************************************/
    /*http://brennaobrien.com/blog/2014/05/style-input-type-range-in-every-browser.html */
    /*************************************/

    .cr-slider {
        -webkit-appearance: none;
        /*removes default webkit styles*/
        /*border: 1px solid white; *//*fix for FF unable to apply focus style bug */
        width: 300px;
        /*required for proper track sizing in FF*/
        max-width: 100%;
        padding-top: 8px;
        padding-bottom: 8px;
        background-color: transparent;
    }

    .cr-slider::-webkit-slider-runnable-track {
        width: 100%;
        height: 3px;
        background: rgba(0, 0, 0, 0.5);
        border: 0;
        border-radius: 3px;
    }

    .cr-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        border: none;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        background: #ddd;
        margin-top: -6px;
    }

    .cr-slider:focus {
        outline: none;
    }
    /*
    .cr-slider:focus::-webkit-slider-runnable-track {
    background: #ccc;
    }
    */

    .cr-slider::-moz-range-track {
        width: 100%;
        height: 3px;
        background: rgba(0, 0, 0, 0.5);
        border: 0;
        border-radius: 3px;
    }

    .cr-slider::-moz-range-thumb {
        border: none;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        background: #ddd;
        margin-top: -6px;
    }

    /*hide the outline behind the border*/
    .cr-slider:-moz-focusring {
        outline: 1px solid white;
        outline-offset: -1px;
    }

    .cr-slider::-ms-track {
        width: 100%;
        height: 5px;
        background: transparent;
        /*remove bg colour from the track, we'll use ms-fill-lower and ms-fill-upper instead */
        border-color: transparent;/*leave room for the larger thumb to overflow with a transparent border */
        border-width: 6px 0;
        color: transparent;/*remove default tick marks*/
    }
    .cr-slider::-ms-fill-lower {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
    }
    .cr-slider::-ms-fill-upper {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
    }
    .cr-slider::-ms-thumb {
        border: none;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        background: #ddd;
        margin-top:1px;
    }
    .cr-slider:focus::-ms-fill-lower {
        background: rgba(0, 0, 0, 0.5);
    }
    .cr-slider:focus::-ms-fill-upper {
        background: rgba(0, 0, 0, 0.5);
    }
    /*******************************************/

    /***********************************/
    /* Rotation Tools */
    /***********************************/
    .cr-rotate-controls {
        position: absolute;
        bottom: 5px;
        left: 5px;
        z-index: 1;
    }
    .cr-rotate-controls button {
        border: 0;
        background: none;
    }
    .cr-rotate-controls i:before {
        display: inline-block;
        font-style: normal;
        font-weight: 900;
        font-size: 22px;
    }
    .cr-rotate-l i:before {
        content: '↺';
    }
    .cr-rotate-r i:before {
        content: '↻';
    }
</style>