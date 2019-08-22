<template>
    <div>
      <v-app-bar dense app clipped-left clipped-right fixed dark color="teal darken-4">
        <v-btn icon @click.stop="main_menu_dialog = true" title="Apps Menu">
          <v-icon>apps</v-icon> 
        </v-btn>
        <v-toolbar-items>
          <template v-for="items in top_menu_items">
            <v-btn class="headline" text disabled  v-if="items.menu_name">{{items.menu_name}}</v-btn>
            <v-btn v-else class="text-capitalize"text text :to="items.route">{{items.text}}</v-btn>
          </template>
        </v-toolbar-items>
        <v-spacer></v-spacer>
        Welcome {{username}}
        <v-menu offset-y>
          <template v-slot:activator="{ on }">
            <v-btn
              v-on="on"
              icon
            >
              <v-icon>more_vert</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="profile">
              <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item @click="logout">
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-app-bar>
      <v-dialog v-model="main_menu_dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
          <v-card color="teal" :style="{background: menuBackground, backgroundSize:'cover'}">
              <v-toolbar dark dense color="primary">
                  <v-btn icon dark @click="main_menu_dialog = false">
                      <v-icon>close</v-icon>
                  </v-btn>
              </v-toolbar>
              <v-layout row wrap px-5 py-5 class="justify-center">
                 <v-flex xs12 sm4 md2 lg1>
                  <v-layout column class="align-center">
                    <v-flex>
                      <v-btn color="primary" fab dark :large="$vuetify.breakpoint.mdAndUp" :small="$vuetify.breakpoint.xsOnly" :medium="$vuetify.breakpoint.smAndUp" to="/" @click="main_menu_dialog = false">
                        <v-icon>
                          dashboard
                        </v-icon>
                      </v-btn>
                    </v-flex>
                    <v-flex class="white--text font-weight-bold text-uppercase">
                      Dashboard
                    </v-flex>
                  </v-layout>
                </v-flex>
                <v-flex xs12 sm4 md2 lg1  v-for="item in main_menu_items" :key="item.index">
                  <v-layout column class="align-center">
                    <v-flex>
                      <v-btn color="primary" fab dark :large="$vuetify.breakpoint.mdAndUp" :small="$vuetify.breakpoint.xsOnly" :medium="$vuetify.breakpoint.smAndUp" v-bind:to="item.route" @click="main_menu_dialog = false">
                        <v-icon>
                          {{item.icon}}
                        </v-icon>
                      </v-btn>
                    </v-flex>
                    <v-flex class="white--text font-weight-bold text-uppercase">
                      {{item.name}}
                    </v-flex>
                  </v-layout>
                </v-flex>
              </v-layout>
          </v-card>
      </v-dialog>
  </div>
</template>
<script>
    export default{
        data () {
            return {
              main_menu_dialog: false,
              //main_menu_items : '',
              menuBackground:'url("'+this.$asset+'/svg/apexion_logo.svg")',
              username : '',
              main_menu_items:[],
              top_menu_items:[],
            }
        },
        props:['topMenuUrl'],
        watch:{
          topMenuUrl:{
            handler(){
              if(this.topMenuUrl != ''){
                axios.get(this.topMenuUrl).then((response)=>{
                    this.top_menu_items = response.data.items
                    console.log(this.top_menu_items)
                })
              }
              else{
                this.top_menu_items = []
              }
            }
          }
        },
        created:function(){

        },
        mounted(){
          axios.get('/user').then((response)=>{
            this.username = response.data.username
          })
          axios.get('/config/menu').then((response)=>{
              if(response.data){
                  this.main_menu_items = response.data
              }
          })
        },
        methods:{
            logout:function(){
              /*
                axios.get('http://www.apexiondental.com/erp/admin/logout').then(
                    function(response){
                        localStorage.removeItem('token');
                        this.$router.push('/login')
                    }
                )
              */  
              axios.post('logout').then((response)=>{
                      localStorage.removeItem('token');
                      this.$router.push('/login')
                  }
              )
                
            },
            profile(){

            },
            updateTopMenu(url){
               var url = this.topMenuUrl
            }
        }
    }
</script>
