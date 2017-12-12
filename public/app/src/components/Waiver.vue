<template>
    <div style="height:400px;overflow:scroll">
        <table class="table-bordered table-hover table">
            <tbody>
                <tr v-for="question,key in waiver.questions">
                    <td>
                        <p style="margin:0px 0px 5px">{{ question.question }}</p>
                        <div v-if="question.question_type==='text_if_no' && !waiver.questions[key].selected">
                            <input type="text" class="form-control" placeholder="Type remarks here..." v-model="waiver.questions[key].data.answer" />
                        </div>

                        <div v-if="question.question_type==='text_if_yes_with_message' && waiver.questions[key].selected">
                            <div class="alert alert-info">
                                {{ question.data.message }}
                                <br/>
                                <input type="text" class="form-control" placeholder="Type remarks here..." v-model="waiver.questions[key].data.answer" />
                            </div>
                        </div>

                        <div v-if="question.question_type==='radio_if_yes' && waiver.questions[key].selected">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        {{ question.data.message }}
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" v-model="waiver.questions[key].data.selected_option">
                                            <option v-bind:value="k" v-for="option,k in question.data.options">{{ option.label }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-danger">
                                {{ question.data.options[question.data.selected_option].message }}
                                <br/>
                                <input type="text" placeholder="Type remarks here..." v-if="question.data.options[question.data.selected_option].textbox" v-model="question.data.options[question.data.selected_option].answer" class="form-control">
                            </div>
                        </div>
                    </td>
                    <td style="width:100px">
                        <div class="md-checkbox">
                            <input type="checkbox" v-bind:id="'check'+key" class="md-check" v-model="waiver.questions[key].selected"/>
                            <label v-bind:for="'check'+key">
                                <span></span>
                                <span class="check"></span>
                                <span class="box"></span>
                                {{ waiver.questions[key].selected?'YES':'NO' }}
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="signature-pad" class="signature-pad" style="background-color:#cccccc">
                                    <div class="signature-pad--body">
                                        <span style="padding:10px">Signature:</span>
                                        <canvas style="margin:0px 20% 10px"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger button clear btn-block">Clear</button><br/>
                                <button type="button" class="btn btn-warning undo btn-block">Undo</button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    import ToggleButton from 'vue-js-toggle-button'
    export default {
        name: 'Waiver',
        props:['appointment','the_waiver'],
        components:{ToggleButton},
        data:function(){
            return {
                waiver:{
                    signature:'',
                    questions:[],
                    strokes:0
                },
                questions:[]
            }
        },
        methods:{
            getWaiverQuestions:function(){
                let u = this;
                axios.get('/api/waiver/getWaiverQuestions')
                    .then(function (response) {
                        u.questions = response.data;
                    });
            }
        },
        mounted:function(){
            if(this.the_waiver !== undefined){
                this.waiver = this.the_waiver;
            }
            else{
                this.getWaiverQuestions();
            }
            let u = this;



            var wrapper = document.getElementById("signature-pad");
            var canvas = wrapper.querySelector("canvas");
            var signaturePad = new SignaturePad(canvas, {
                // It's Necessary to use an opaque color when saving image as JPEG;
                // this option can be omitted if only saving as PNG or SVG
                backgroundColor: 'rgb(255, 255, 255)',
                onEnd:function(){
                    u.waiver.signature = signaturePad.toDataURL();
                    u.strokes++;
                }
            });


            setInterval(function(){
                u.$emit('sync_waiver_data', u.waiver);
            },1000);

            // Adjust canvas coordinate space taking into account pixel ratio,
            // to make it look crisp on mobile devices.
            // This also causes canvas to be cleared.
            function resizeCanvas() {
                // When zoomed out to less than 100%, for some very strange reason,
                // some browsers report devicePixelRatio as less than 1
                // and only part of the canvas is cleared then.
                var ratio =  Math.max(window.devicePixelRatio || 1, 1);

                // This part causes the canvas to be cleared
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                // This library does not listen for canvas changes, so after the canvas is automatically
                // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
                // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
                // that the state of this library is consistent with visual state of the canvas, you
                // have to clear it manually.
                signaturePad.clear();
                u.strokes = 0;
            }

            // On mobile devices it might make more sense to listen to orientation change,
            // rather than window resize events.
            window.onresize = resizeCanvas;
            resizeCanvas();

            $(".clear").click(function(){
                signaturePad.clear();
                u.strokes = 0;
                u.waiver.signature = signaturePad.toDataURL();
            });

            $(".undo").click(function(){
                var data = signaturePad.toData();

                if (data) {
                    data.pop(); // remove the last dot or line
                    signaturePad.fromData(data);
                    u.stroke--;

                }
                u.waiver.signature = signaturePad.toDataURL();
            });
        },
        watch:{
            questions:function(){
                this.waiver.questions = [];
                for(var x=0;x<this.questions.length;x++){
                    var data  = {};

                    if(this.questions[x].question_type === 'text_if_no'){
                        data.answer = ''
                    }
                    else if(this.questions[x].question_type === 'text_if_yes_with_message'){
                        data = {
                            message: this.questions[x].question_data.message,
                            answer:'',
                            disallowed: this.questions[x].question_data.disallowed_services
                        }
                    }
                    else if(this.questions[x].question_type === 'radio_if_yes'){
                        var options = this.questions[x].question_data.options;
                        for(var y=0;y<options.length;y++){
                            options[y].answer = '';
                        }


                        data = {
                            message: this.questions[x].question_data.message,
                            options: options,
                            selected_option: options.length-1,
                            disallowed: this.questions[x].question_data.disallowed_services
                        }
                    }

                    var item = {
                        question: this.questions[x].question,
                        selected: this.questions[x].question_data.default_selected === 'YES',
                        data:data,
                        question_type:this.questions[x].question_type
                    };

                    if(this.appointment.client.gender === this.questions[x].target_gender || this.questions[x].target_gender === 'both')
                        this.waiver.questions.push(item);
                }

                this.$emit('sync_waiver_data', this.waiver)
            }
        }
    }
</script>