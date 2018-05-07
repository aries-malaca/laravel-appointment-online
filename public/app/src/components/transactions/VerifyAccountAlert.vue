<template>
    <div class="alert alert-danger">
        <strong>Important!</strong>
        Please verify your email address first to access your transactions. &nbsp;<br/><br/>
        <button data-loading-text="Sending..." v-if="!resent" class="btn btn-success" @click="resendConfirmation($event)"> Resend Email </button>
    </div>
</template>

<script>
    export default {
        name: 'VerifyAccountAlert',
        data(){
            return {
                resent:false
            };
        },
        methods:{
            resendConfirmation:function(event){
                var $btn = $(event.target);
                $btn.button('loading');
                let u = this;
                axios.get('/api/user/sendConfirmation?token=' + this.token)
                    .then(function (response) {
                        if(response.data.result === 'success'){
                            toastr.success("Email sent! check your email to verify your account.");
                            $btn.button('reset');
                            u.$emit('resend');
                            u.resent = true;
                        }
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                        $btn.button('reset');
                    });
            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            }
        }
    }
</script>