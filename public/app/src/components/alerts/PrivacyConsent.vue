<template>
    <div class="modal fade" id="consent-modal" data-backdrop="static" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Acknowledgement & Consent</h4>
                </div>
                <div v-html="computedConsent" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" @click="approveConsent"  class="btn green">Yes, I Agree</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</template>
<script>
    export default {
        name: 'PrivacyConsent',
        methods:{
            approveConsent(){
                axios.post('/api/user/approveConsent?token=' + this.token)
                    .then(function () {
                        $("#consent-modal").modal("hide")
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        computed:{
            computedConsent(){
                return this.configs.PRIVACY_CONSENT.replace("{first_name}", this.user.first_name).replace("{last_name}", this.user.last_name);
            },
            configs(){
                return this.$store.state.configs;
            },
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            }
        },
        mounted(){
            setTimeout(()=>{
                if(this.user !== null){
                    if(this.user.id !== undefined){
                        if(this.user.is_agreed === 0)
                            $("#consent-modal").modal("show")
                    }
                }
            },3000);
        }
    }
</script>