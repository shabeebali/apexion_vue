<template>
    <div>
      <v-toolbar dense app clipped-left clipped-right fixed dark color="teal darken-4">
        <v-toolbar-side-icon v-if="$vuetify.breakpoint.smAndDown" @click="$emit('sidebar-toggle')"></v-toolbar-side-icon>
        <v-btn icon @click.stop="main_menu_dialog = true">
            <v-icon>apps</v-icon>
        </v-btn>

        <v-toolbar-title class="white--text">Title</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn icon>
          <v-icon>search</v-icon>
        </v-btn>

        <v-btn icon>
          <v-icon>apps</v-icon>
        </v-btn>

        <v-btn icon @click="logout">
          <v-icon>logout</v-icon>
        </v-btn>

        <v-btn icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </v-toolbar>
      <v-dialog v-model="main_menu_dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
          <v-card>
              <v-toolbar dark color="primary">
                  <v-btn icon dark @click="main_menu_dialog = false">
                      <v-icon>close</v-icon>
                  </v-btn>
                  <v-toolbar-title>Main Menu</v-toolbar-title>
              </v-toolbar>
              <v-list v-for="item in main_menu_items" :key="item.index">
                  <v-list-tile v-bind:to="item.route" @click="main_menu_dialog = false">
                      <v-list-tile-action>
                          <v-icon>{{item.name.charAt(0)}}</v-icon>
                      </v-list-tile-action>
                      <v-list-tile-title>{{item.name}}</v-list-tile-title>
                  </v-list-tile>
              </v-list>
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
            }
        },
        props:['main_menu_items'],
        created:function(){

        },
        methods:{
            logout:function(){
                axios.post('http://localhost/apexion/public/logout').then(
                    function(response){
                        localStorage.removeItem('token');
                        window.location = 'http://localhost/apexion/public';
                    }
                )
            }
        }
    }
</script>
