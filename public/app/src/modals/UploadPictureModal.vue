<template>
    <div class="modal fade" id="upload-picture-modal" tabindex="1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Update Picture</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="form" enctype="multipart/form-data" onsubmit="return false;">
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                    <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                <div>
                                        <span class="btn default btn-file">
                                            <span class="fileinput-new"> Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="file" id="file">
                                        </span>
                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button @click="uploadPicture" type="button" class="btn btn-primary">Upload</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>

<script>
    export default {
        name: 'UploadPictureModal',
        props:['user','token'],
        methods:{
            uploadPicture:function(){
                let u = this;
                let data = new FormData();
                data.append('file', $('#file')[0].files[0]);

                $.ajax({
                    url:'/api/user/uploadPicture?token='+this.token+'&user_id=' + this.user.id,
                    type:'POST',
                    data:data,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success:function(){
                        u.$emit('get_profile');
                        u.$emit('update_user');
                        $('#upload-picture-modal').modal('hide');
                    },
                    error:function (error) {
                        XHRCatcher(error);
                    }
                });
            },
        }
    }
</script>