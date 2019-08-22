<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" tabs>
                <v-toolbar-title>Product: {{data.name}}t</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="primary" :to="'/inventory/products/edit/'+$route.params.id">Edit</v-btn>
            </v-toolbar>
            <v-row>
                <v-col cols=6 v-for="(item,i) in data.items" :key="i">
                    <v-card >
                        <v-card-title class="subheading font-weight-bold">{{ item.name }}</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-simple-table>
                                <tbody>
                                    <tr v-for="(it,index) in item.list" :key="index">
                                        <td>{{it.key}}</td>
                                        <td>{{ it.value }}</td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
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
                        to:'/products',
                        exact:true,
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