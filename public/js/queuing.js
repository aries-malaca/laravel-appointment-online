var socket = io.connect('https://lbo-express.azurewebsites.net');

socket.on('refreshAppointments', function(data){
    if(Number(document.getElementById("branch_id").value) == data.branch_id){
        vue_queuing.getAppointments();
    }
});

socket.on('callClient', function(data){
    if(Number(document.getElementById("branch_id").value) == data.branch_id) {
        if(data.action === 'call')
            new Audio('../../../audio/bell.ogg').play();

        vue_queuing.refresh();
    }
});
// the instance should be accessible by other objects...
var vue_queuing = new Vue({
    //inside the element with id main, vue_queing data, and methods will be accessible.
    el:'#app',
    //stores our vuejs instance's data.
    data:{
        branch_id: Number(document.getElementById("branch_id").value),
        current_time:moment().format("MM/DD/YYYY hh:mm:ss A"),
        appointments:[],
        calling:[],
        serving:[],
        show:true,
        current_image:1,
        limit:22
    },
    //stores our vuejs instance's methods/functions.
    methods:{
        getAppointments:function(){
            if(this.branch === null)
                return false;

            let u = this;
            let url = '../../api/appointment/getAppointments/queue/'+ this.branch_id +'/queue';

            axios.get(url)
                .then(function (response) {
                    u.appointments = [];
                    response.data.forEach(function(item){
                        u.appointments.push(item);
                    });
                });
        },
        refresh(){
            let u = this;
            axios.get('https://lbo-express.azurewebsites.net/api/queuing/' +this.branch_id)
                .then(function (response) {
                    u.calling = response.data.calling;
                    u.serving = response.data.serving;
                });
        },
        nextImage:function(){
            let u = this;
            this.show = false;
            setTimeout(function(){
                if(u.current_image===u.limit)
                    u.current_image = 1;
                else
                    u.current_image++;

                u.show = true;
            },100);
        },
        getStatus:function(item){
            let find_s = this.serving.find(function(i){
                return item.client_id === i.client_id;
            });
            let find_c = this.calling.find(function(i){
                return item.client_id === i.client_id;
            });

            if(find_s !== undefined)
                return 'serving';
            if(find_c !== undefined)
                return 'calling';

            return 'pending';
        },
        exists:function(clients, appointment) {
            return clients.filter(function(item){
                return item.client_id === appointment.client_id
            }).length > 0;
        }
    },
    computed:{
        clients:function(){
            let u = this;
            var clients = [];
            for(var x=0;x<this.appointments.length;x++){
                if(!this.exists(clients, this.appointments[x]))
                    clients.push({
                        client_name: this.appointments[x].client_shortname,
                        transaction: this.appointments[x].items[0],
                        transaction_time: moment(this.appointments[x].items[0].book_start_time).format("hh:mm A"),
                        technician_name: this.appointments[x].technician_shortname,
                        client_id:this.appointments[x].client_id
                    });
            }

            return clients.map(function(item){
                    item.status = u.getStatus(item);
                    return item;
                }).sort(function(a, b) {
                var nameA = a.status.toUpperCase(); // ignore upper and lowercase
                var nameB = b.status.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB)
                    return -1;
                if (nameA > nameB)
                    return 1;
            }).slice(0, 6);
        },
    },
    mounted:function(){
        let u = this;
        setInterval(function () {
            u.current_time = moment().format("MM/DD/YYYY hh:mm:ss A");
        },1000);

        setInterval(function(){
            u.nextImage();
        },10000);

        this.getAppointments();
    }
});