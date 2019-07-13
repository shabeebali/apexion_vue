<template>
    <div style="height:100%">
        <main-header v-if="this.$router.currentRoute.name !== 'Login'" v-on:sidebar-toggle="sidebarToggle = !sidebarToggle"></main-header>
        <router-view :sidebarToggle="sidebarToggle"></router-view>
    </div>
</template>
<script>
import MainHeader from './MainHeaderComponent.vue'
export default{
    data:function(){
        return{
            //token:'',
            sidebarToggle:0,
        }
    },
    components:{
        'main-header': MainHeader,
    },
    created:function(){
        /*
        var thisObj = this;
        if(typeof localStorage.token !== "undefined"){
            var token = localStorage.token;
            window.axios.defaults.headers.common['Authorization'] = 'Bearer '+ token;
            axios.get('/validate_token').then((response)=>{
                console.log('token authenticated');
                this.getMainMenu();
            })
        }
        else{
            var bu_length = window.axios.defaults.baseURL.length;
            axios.get(window.axios.defaults.baseURL.substr(0,bu_length-3)+"generate_token").then((response)=>{
                if(response.status == 200){
                    var token = response.data.token;
                    localStorage.setItem('token', token);
                    window.axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
                }
                this.getMainMenu();
            })
        }
        */
    },
    methods:{
    }
}
</script>
