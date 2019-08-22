require('./bootstrap');

window.Vue = require('vue');
import vuetify from './components/vuetify.js'
import VueRouter from 'vue-router'
import {routes} from './router.js'
import AppComponent from './components/AppComponent.vue'
Vue.use(VueRouter)
const router = new VueRouter({
  routes,
  base:'/admin',
  mode:'history',
});
//Vue.prototype.$asset = '/' // /erp/
Vue.prototype.$asset = '/erp/'
Vue.mixin({
  methods: {
    confirmDialog(msg) {
      return new Promise(function (resolve, reject) {
      let confirmed = window.confirm(msg);

      return confirmed ? resolve(true) : reject(false);
      });
    }
  },
  forceLogout(){
    window.location.replace(window.axios.defaults.baseURL.substr(0,window.axios.defaults.baseURL.length-3)+'login')
  }
})
//Vue.component('app', require('./components/AppComponent.vue'));
const app = new Vue({
    vuetify,
    router:router,
    el:'#app',
    components:{
        'app-component':AppComponent,
    },
    created(){
        
        var localToken = localStorage.token;
        if (localToken === undefined){
          this.$router.push('/login')
        }
        else{
          window.axios.defaults.headers.common['Authorization'] = 'Bearer '+ localToken
        }
    },
    mounted(){
      
    },
    methods:{
      
    }
});
