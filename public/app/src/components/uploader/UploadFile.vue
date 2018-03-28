<template>
    <form role="form" class="form" enctype="multipart/form-data" onsubmit="return false;">
        <div class="form-group">
            <input type="file" name="file" id="file_input"/><br/>
            <button @click="uploadPicture" type="button" class="btn btn-primary">Add</button>
        </div>
    </form>
</template>
<script>
    export default {
        name: 'UploadFile',
        props:['category','path'],
        methods:{
            uploadPicture:function(){
                let u = this;
                let data = new FormData();
                data.append('file', $('#file_input')[0].files[0]);
                $.ajax({
                    url:'/api/'+ this.category +'/uploadFile?token='+this.token,
                    type:'POST',
                    data:data,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success:function(response){
                        u.$emit('setFileName', u.path +'/'+ response.filename);
                    },
                    error:function (error) {
                        XHRCatcher(error);
                    }
                });
            },
        },
        computed:{
            token(){
                return this.$store.state.token;
            }
        }
    }
</script>