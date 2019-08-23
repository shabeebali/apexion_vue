<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs class="" :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <div v-if="loader" >
                    <pulse-loader style="top:50%;left:50%;position:absolute;"></pulse-loader>
                </div>
                <div v-else>
                    <list-component
                    listHeadline="Sale Order"
                    listClass="sale-order-list"
                    :baseRoute="baseRoute"
                    :filterables="filterables"
                    :listSettingsRoute="listSettingsRoute"
                    :triggerUpdate="triggerUpdate"
                    v-on:triggered-update="triggerUpdate=false"
                    v-on:open-add-dialog="redirectAdd"
                    v-on:open-view-dialog="viewItem"
                    exportable
                    importable></list-component>
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
                baseRoute:'/sale/orders?',
                loader:false,
                listSettingsRoute:'',
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
                        text:'Sale Order',
                        disabled:true,
                        to:'/sale/orders'
                    },
                ]
            }
        },
        mounted(){
            this.$emit('top-menu-url','sale/get_menu')
        },
        methods:{
            viewItem($event){
                this.$router.push('/sale/orders/view/'+$event)
            },
            redirectAdd(){
                this.$router.push('/sale/orders/add')
            },
            goBAck(){
                this.$router.back()
            }
        }
    }
</script>
