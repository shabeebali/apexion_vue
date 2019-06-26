<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <v-layout row wrap>
                    <v-flex lg3 md4 sm6 xs12 pa-2 v-if="pending_count > 0">
                        <v-card color="blue-grey darken-4 white--text" class="pa-2">
                            <v-card-text class="pa-0">
                                <div class="ma-0"><v-chip dark color="blue-grey darken-3"><v-avatar><v-icon>hourglass_empty</v-icon></v-avatar> Products Pending Approval:</v-chip><v-chip color="amber">{{pending_count}}</v-chip></div>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn icon dark to="pending">
                                    <v-icon>arrow_forward</v-icon>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                    <v-flex lg3 md4 sm6 xs12 pa-2 v-if="tally_count > 0">
                        <v-card color="blue-grey darken-3 white--text" class="pa-2">
                            <v-card-text class="pa-0">
                                <div class="ma-0"><v-chip dark color="blue-grey darken-4"><v-avatar><v-icon>T</v-icon></v-avatar>To be synced with Tally:</v-chip><v-chip color="amber">{{tally_count}}</v-chip></div>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn icon dark to="tally">
                                    <v-icon>arrow_forward</v-icon>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>

                <list-component
                listHeadline="Products"
                listClass="products-list"
                :baseRoute="baseRoute"
                :filterables="filterables"
                :listSettingsRoute="listSettingsRoute"
                :triggerUpdate="triggerUpdate"
                v-on:triggered-update="triggerUpdate=false"
                v-on:open-add-dialog="redirectAdd"
                v-on:open-edit-dialog="editItem"
                exportable
                importable></list-component>
            </v-container>
        </v-content>
    </div>
</template>
<script>
    import ListComponent from '../../../js/components/ListComponent.vue'
    export default{
        components:{
            'list-component':ListComponent,
        },
        data(){
            return{
                baseRoute:'/products?',
                listSettingsRoute:'/products/list_settings',
                filterables:[],
                addDialog:false,
                triggerUpdate:false,
                editDialog:false,
                editId:0,
                pending_count:0,
                tally_count:0,
                breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Products',
                        disabled:true,
                        to:'/products/list'
                    },
                ]
            }
        },
        mounted(){
            axios.get('/products/filterables').then((response)=>{
                if(response.status == 200)
                {
                    this.filterables = response.data.filterables
                }
                else{
                    alert('Something went wrong!!!')
                    console.log(response)
                }
            })
            axios.get('/products/pending_count').then((response)=>{
                this.pending_count = response.data.count;
            })
            axios.get('/products/tally_count').then((response)=>{
                this.tally_count = response.data.count;
            })
        },
        methods:{
            editItem($event){
                this.$router.push('edit/'+$event)
            },
            redirectAdd(){
                this.$router.push('add')
            }
        }
    }
</script>
