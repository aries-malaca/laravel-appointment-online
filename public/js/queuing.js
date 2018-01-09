var socket = io.connect('https://system.lay-bare.com:5000');

socket.on('refreshAppointments', function(data){
    if(Number(document.getElementById("branch_id").value) == data.branch_id){
        vue_queuing.getAppointments();
    }
});

socket.on('callItem', function(data){
    if(Number(document.getElementById("branch_id").value) == data.branch_id){
        vue_queuing.getAppointments();
        new Audio('../../../audio/bell.ogg').play();
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
        getStatus:function(items){
            for(var x=0;x<items.length;x++){
                if(this.isOnCall(items[x]))
                    return 'calling';

                if(this.isOnServe(items[x]))
                    return 'serving';
            }

            return 'pending';
        },
        isOnCall:function(item){
            return (Number(moment().format('X'))- item.item_data.called)<60 && !this.isOnServe(item);
        },
        isOnServe:function(item){
            return item.serve_time !== null && item.item_status == 'reserved';
        },
        hasPending:function(items){
            for(var x=0;x<items.length;x++){
                if(items[x].item_status === 'reserved')
                    return true
            }
            return false;
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
        }
    },
    computed:{
        clients:function(){
            var clients = [];
            for(var x=0;x<this.appointments.length;x++){
                if(this.hasPending(this.appointments[x].items))
                    clients.push({
                        client_name: this.appointments[x].client_shortname,
                        transaction: this.appointments[x].items[0],
                        transaction_time: moment(this.appointments[x].items[0].book_start_time).format("hh:mm A"),
                        technician_name: this.appointments[x].technician_shortname,
                        status:this.getStatus(this.appointments[x].items)
                    });
            }

            return clients.sort(function(a, b) {
                var nameA = a.status.toUpperCase(); // ignore upper and lowercase
                var nameB = b.status.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB)
                    return -1;
                if (nameA > nameB)
                    return 1;
            }).slice(0, 6);
        }
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