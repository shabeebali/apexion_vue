<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" light tabs>
                <v-toolbar-title>Create Order</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn tile color="primary" :disabled="customerSelect == null || count == 1" @click="create">Create</v-btn>
            </v-toolbar>
            <v-card>
                <v-row>
                    <v-col cols=6>
                        <v-autocomplete
                          v-model="customerSelect"
                          :loading="customerLoading"
                          :items="customerItems"
                          :search-input.sync="search"
                          cache-items
                          class="mx-4"
                          flat
                          clearable
                          hide-no-data
                            hide-details
                        ></v-autocomplete>
                    </v-col>
                </v-row>
                <v-row class="mx-0">
                    <v-col class="pa-3">
                        <v-card>
                            <v-card-text>
                                <v-simple-table dense >
                                    <thead>
                                        <tr>
                                            <th class="text-left">#</th>
                                            <th class="text-left">Product</th>
                                            <th class="text-left" >Quantity</th>
                                            <th class="text-left" >Rate</th>
                                            <th class="text-left" >Price</th>
                                            <th class="text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item,index) in items" :key="index">
                                            <td>{{item.line}}</td>
                                            <td>{{item.product}}</td>
                                            <td>{{item.qty}}</td>
                                            <td>{{item.rate}}</td>
                                            <td>{{item.price}}</td>
                                            <td><v-btn icon fab small @click.stop="removeItem(item.pos)"><v-icon>mdi-minus-circle</v-icon></v-btn></td>
                                        </tr>
                                        <tr>
                                            <td>{{count}}</td>
                                            <td>
                                                <v-autocomplete
                                                  v-model="productSelect"
                                                  :loading="productLoading"
                                                  :items="productItems"
                                                  :search-input.sync="searchProduct"
                                                  cache-items
                                                  class="mx-4"
                                                  flat
                                                  clearable
                                                  hide-no-data
                                                    hide-details
                                                ></v-autocomplete>
                                            </td>
                                            <td><v-text-field v-model="qty"></v-text-field></td>
                                            <td><v-text-field v-model="rate"></v-text-field></td>
                                            <td><v-text-field readonly :rules="[rules.decimal]" :value="parseInt(qty)*parseFloat(rate)"></v-text-field></td>
                                            <td><v-btn icon rounded small :disabled="productSelect == null || isNaN(parseInt(qty)*parseFloat(rate))" @click.stop="addLine"><v-icon>mdi-plus-circle</v-icon></v-btn></td>
                                        </tr>
                                    </tbody>
                                </v-simple-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card>

        </v-container>
    </v-content>
</template>
<script>
export default{
    data(){
        return{  
            search:null,
            customerItems:[],
            count:1,
            customerSelect:null,
            customerLoading:false,
            productItems:[],
            productSelect:null,
            productLoading:false,
            searchProduct:null,
            items:[],
            qty:1,
            rate:0,
            rules:{
                decimal: value => !isNaN(value) || 'Must be an number',
            },
            breadcrumbs:
            [
                {
                    text:'Home',
                    disabled:false,
                    to:'/'
                },
                {
                    text:'Orders',
                    disabled:false,
                    to:'/sale/orders',
                    exact:true
                },
                {
                    text:'Add',
                    disabled:true,
                    to:'/sale/orders/add'
                },  
            ]
        }
    },
    watch: {
        search (val) {
            this.customerLoading = true
            axios.get('customers/search?&search='+val).then((response)=>{
                if(response.status == 200){
                    this.customerItems = response.data.items
                    this.customerLoading = false
                }
                else{
                    console.log(response.status)
                }
            })
        },
        searchProduct (val) {
            this.productLoading = true
            axios.get('products/search?&search='+val).then((response)=>{
                if(response.status == 200){
                    this.productItems = response.data.items
                    this.productLoading = false
                }
                else{
                    console.log(response.status)
                }
            })
        },
    },
    mounted(){
    },
    computed:{
       
    },
    methods:{
        addLine(){
            this.items.push({
                'id':this.productSelect,
                'line':this.count,
                'product':this.searchProduct,
                'qty':this.qty,
                'rate':this.rate,
                'price': parseInt(this.qty)*parseFloat(this.rate),
                'pos': parseInt(this.count)-1
            })
            this.count=parseInt(this.count)+1
            this.qty = 1
            this.rate = 0
            this.searchProduct=null
            this.productSelect=null
        },
        removeItem(pos){
            this.items.splice(pos,1)
            var tempCount = 1
            Object.keys(this.items).forEach((key)=>{
                this.items[key].line = tempCount
                this.items[key].pos = parseInt(tempCount)-1
                tempCount = tempCount+1
            })
            this.count = parseInt(this.items.length)+1
        },
        create(){
            var fD = new FormData()
            fD.append('customer_id',customerSelect)
            fD.append('items',items)
            axios.post('sale/orders/add',fD).then((response)=>{
                if(response.status == 200){
                    
                }
            })
        }
    }
}
</script>
