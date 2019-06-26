<template>
    <div>
        <v-navigation-drawer :mini-variant="mini" v-model="sidebar_left" app fixed :permanent="$vuetify.breakpoint.mdAndUp" clipped mobile-break-point="960">
            <v-layout v-if="$vuetify.breakpoint.mdAndUp">
                <v-flex xs12>
                    <v-btn v-if="mini" style="padding: 0;margin: 0;min-width: 80px;" flat  v-on:click="mini=false"><v-icon>menu</v-icon></v-btn>
                    <v-btn v-else class="ma-0" style="left:calc(300px - 35px);" icon flat v-on:click="mini = true"><v-icon>close</v-icon></v-btn>
                </v-flex>
            </v-layout>
            <v-list dense>
                <template v-for="(item, i) in items">
                    <v-layout row v-if="item.heading" align-center :key="i">
                        <v-flex xs6>
                          <v-subheader>
                            {{ item.heading }}
                          </v-subheader>
                        </v-flex>
                        <v-flex xs6 ></v-flex>
                    </v-layout>
                    <v-divider dark v-else-if="item.divider" class="my-0" :key="i"></v-divider>
                    <v-list-tile :key="i" v-else v-bind:to="item.route" @click="extraFunc">
                        <v-list-tile-action :title="item.text">
                            <v-badge bottom color="red">
                                <template v-slot:badge v-if="item.badge">
                                    <span>{{item.badge}}</span>
                                </template>
                                <v-icon >{{ item.icon }}</v-icon>
                            </v-badge>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title class="grey--text">
                                {{ item.text }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </v-navigation-drawer>
    </div>
</template>
<script>
    export default{
        props:['items','sidebar'],
        data(){
            return{
                'sidebar_left':this.sidebar,
                'mini':true
            }
        },
        created(){
            this.mini = this.$vuetify.breakpoint.mdAndUp
        },
        watch:{
            sidebar:{
                handler(){
                    console.log('kitti')
                    this.sidebar_left = this.sidebar
                }
            },
            sidebar_left:{
                handler(){
                    console.log(this.sidebar_left)
                    if(this.sidebar_left == false){
                        this.$emit('close-sidebar')
                    }
                }
            }
        },
        methods:{
            extraFunc(){
                if(!this.$vuetify.breakpoint.mdAndUp){
                    this.sidebar_left = false
                }
            }
        }
    }
</script>
