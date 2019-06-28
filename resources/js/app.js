require('./bootstrap');

window.Vue = require('vue');
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import colors from 'vuetify/es5/util/colors'
import {routes} from './router.js'
import AppComponent from './components/AppComponent.vue'


Vue.use(Vuetify)
Vue.use(VueRouter)
const router = new VueRouter({
  routes,
  base:'/erp/admin', // /er/admin
  mode:'history',
});
Vue.prototype.$asset = '/erp/' // /erp/
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
    router:router,
    el:'#app',
    components:{
        'app-component':AppComponent,
    },
    created(){
        this.$vuetify.theme.primary = colors.teal.darken1
    },
    methods:{
      
    }
});
