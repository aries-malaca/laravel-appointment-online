<template>
    <div id="booking-modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Appointment Booking Form</h4>
                </div>
                <div class="modal-body">
                    <wizard
                            :steps="steps"
                            :onNext="nextClicked"
                            :onBack="backClicked">
                        <div slot="page1">

                        </div>
                        <div slot="page2">

                        </div>
                        <div slot="page3">

                        </div>
                    </wizard>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueGoodWizard from 'vue-good-wizard';
    import Wizard from '../components/Wizard.vue';
    export default {
        name: 'Booking Modal',
        props: ['user'],
        components: { Wizard },
        data: function(){
            return {
                steps: [
                    {
                        label: 'Appointment Info',
                        slot: 'page1',
                    },
                    {
                        label: 'Product/Services',
                        slot: 'page2',
                    },
                    {
                        label: 'Waiver',
                        slot: 'page3',
                    }
                ]
            }
        },
        methods: {
            nextClicked(currentPage) {
                console.log('next clicked', currentPage)
                return true; //return false if you want to prevent moving to next page
            },
            backClicked(currentPage) {
                console.log('back clicked', currentPage);
                return true; //return false if you want to prevent moving to previous page
            }
        }
    }
</script>