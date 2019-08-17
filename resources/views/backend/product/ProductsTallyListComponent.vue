<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <div v-if="loader" >
                    <pulse-loader style="top:50%;left:50%;position:absolute;"></pulse-loader>
                </div>
                <div v-else>
                    <list-component
                    listHeadline="Products to be synced with Tally"
                    listClass="products-list"
                    :baseRoute="baseRoute"
                    :filterables="filterables"
                    :listSettingsRoute="listSettingsRoute"
                    :triggerUpdate="triggerUpdate"
                    v-on:triggered-update="triggerUpdate=false"
                    v-on:open-add-dialog="redirectAdd"
                    v-on:open-edit-dialog="editItem"
                    extraButton
                    extraButtonLabel="Tally Sync"
                    extraButtonRoute="tally_sync"
                    ></list-component>
                </div>
            </v-container>
        </v-content>
    </div>
</template>
<script>
    import ListComponent from '../../../js/components/ListComponent.vue'
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
    export default{
        components:{
            'list-component':ListComponent,
            'pulse-loader':PulseLoader,
        },
        data(){
            return{
                loader : true,
                baseRoute:'/products?tally=1&',
                listSettingsRoute:'/products/list_settings',
                filterables:[],
                addDialog:false,
                triggerUpdate:false,
                editDialog:false,
                editId:0,
                breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Products',
                        disabled:false,
                        to:'/products/list'
                    },
                    {
                        text:'Tally Sync',
                        disabled:true,
                        to:'/products/tally'
                    },  
                ]
            }
        },
        mounted(){
            this.loader = true
            axios.get('/products/filterables').then((response)=>{
                if(response.status == 200)
                {
                    this.filterables = response.data.filterables
                    this.loader = false
                }
                else{
                    alert('Something went wrong!!!')
                    console.log(response)
                }
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
