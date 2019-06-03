<template>
    <div>
        <main-header v-bind:main_menu_items="main_menu_items" v-on:sidebar-toggle="sidebarToggle = !sidebarToggle"></main-header>
        <router-view :sidebarToggle="sidebarToggle"></router-view>
    </div>
</template>
<script>
import MainHeader from './MainHeaderComponent.vue'
export default{
    data:function(){
        return{
            //token:'',
            main_menu_items:'',
            sidebarToggle:0,
        }
    },
    components:{
        'main-header': MainHeader,
    },
    created:function(){
        var thisObj = this;
        if(typeof localStorage.token !== "undefined"){
            var token = localStorage.token;
            window.axios.defaults.headers.common['Authorization'] = 'Bearer '+ token;
            axios.get('/validate_token').then((response)=>{
                if(response.status !== 200){
                    var bu_length = window.axios.defaults.baseURL.length;
                    window.location = window.axios.defaults.baseURL.substr(0,bu_length-3)+"login";
                }
                else{
                    console.log('token authenticated');
                }
                this.getMainMenu();
            }).catch(function(error){
                console.log(error);
                alert('wrong2');
                var bu_length = window.axios.defaults.baseURL.length;
                window.location = window.axios.defaults.baseURL.substr(0,bu_length-3)+"login";
            });
        }
        else{
            var bu_length = window.axios.defaults.baseURL.length;
            axios.get(window.axios.defaults.baseURL.substr(0,bu_length-3)+"generate_token").then((response)=>{
                if(response.status == 200){
                    var token = response.data.token;
                    localStorage.setItem('token', token);
                    window.axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
                }
                else{
                    window.location = window.axios.defaults.baseURL.substr(0,bu_length-3)+"login";
                }
                this.getMainMenu();
            }).catch(function(error){
                console.log(error);
                alert('wrong');
                window.location = window.axios.defaults.baseURL.substr(0,bu_length-3)+"login";
            });
        }
    },
    methods:{
        getMainMenu:function(){
            axios.get('/config/menu').then((response)=>{
                if(response.data){
                    this.main_menu_items = response.data
                }
            }).catch(function(error){
                alert('Somethig went wrong!!!');
                console.log(error.response.status);
                console.log(error.response);
            });
            return true;
        },
    }
}
</script>
