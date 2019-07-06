<template>
    <div>
      <v-toolbar dense app clipped-left clipped-right fixed dark color="teal darken-4">
        <v-toolbar-side-icon v-if="$vuetify.breakpoint.smAndDown" @click="$emit('sidebar-toggle')"></v-toolbar-side-icon>
        <v-btn icon @click.stop="main_menu_dialog = true" title="Apps Menu">
          <v-icon>apps</v-icon> 
        </v-btn>
        <img src="/erp/images/apexion_logo.svg"/>
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
            <v-list-tile @click="profile">
              <v-list-tile-title>Profile</v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="logout">
              <v-list-tile-title>Logout</v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </v-toolbar>
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
            }
        },
        props:['main_menu_items'],
        created:function(){

        },
        mounted(){
          axios.get('/user').then((response)=>{
            this.username = response.data.username
          })
        },
        methods:{
            logout:function(){
                axios.post('http://apexiondental.com/erp/logout').then(
                    function(response){
                        localStorage.removeItem('token');
                        window.location = 'hhttp://apexiondental.com/erp';
                    }
                )
                /*
                axios.post('http://localhost:8000/logout').then(
                    function(response){
                        localStorage.removeItem('token');
                        window.location = 'http://localhost:8000';
                    }
                )
                */
            },
            profile(){

            }
        }
    }
</script>
