require('./bootstrap');

window.Vue = require('vue');
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import colors from 'vuetify/es5/util/colors'
import {routes} from './router.js'

import AppComponent from './components/AppComponent.vue'
Vue.use(Vuetify)
Vue.use(VueRouter)
console.log(routes)
const router = new VueRouter({
  routes,
  base:'/apexion/public/admin',
  mode:'history',
});
Vue.prototype.$asset = '/apexion/public'
//Vue.component('app', require('./components/AppComponent.vue'));
const app = new Vue({
    router:router,
    el:'#app',
    components:{
        'app-component':AppComponent,
    },
    created(){
        this.$vuetify.theme.primary = colors.teal.darken1
    }
});
