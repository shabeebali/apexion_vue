<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" tabs>
                <v-toolbar-title>Product: {{data.name}}t</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="primary" :to="'/products/edit/'+$route.params.id">Edit</v-btn>
            </v-toolbar>
            <v-card>
                <v-data-iterator :items="data.items" content-tag="v-layout" hide-actions>
                    <template v-slot:item="props">
                        <v-flex xs12 md6 pa-2>
                            <v-card>
                                <v-card-title class="subheading font-weight-bold">{{ props.item.name }}</v-card-title>

                                <v-divider></v-divider>

                                <v-list dense>
                                    <v-list-tile class="mb-2 dataiterator-item" v-for="(it,index) in props.item.list" :key="index">
                                        <v-layout row wrap>
                                            <v-flex>
                                                <v-list-tile-content>{{it.key}}</v-list-tile-content>
                                            </v-flex>
                                            <v-flex>
                                                <v-list-tile-content class="align-end">{{ it.value }}</v-list-tile-content>
                                            </v-flex>
                                        </v-layout>
                                    </v-list-tile>
                                </v-list>
                            </v-card>
                        </v-flex>
                    </template>
                </v-data-iterator>
            </v-card>
        </v-container>
    </v-content>
</template>
<style scoped>
.dataiterator-item:hover {
  background-color: #ddd;
}
</style>
<script>
    export default{
        data(){
            return{
                data:{'name':''},
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
                        text:'view',
                        disabled:true,
                    },  
                ]
            }
        },
        mounted(){
            axios.get('products/view/'+this.$route.params.id).then((response)=>{
                this.data = response.data.data
                console.log(this.data)
            })
        }
    }
</script>