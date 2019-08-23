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
                <v-row class="mx-0" justify="space-between">
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
                          label="Customer"
                        ></v-autocomplete>
                    </v-col>
                    <v-col cols=4>
                        <v-select v-model="pricelistSelect" :items="pricelist_items" label="Pricelist"></v-select>
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
                                            <th class="text-right" >Quantity</th>
                                            <th class="text-right" >Rate</th>
                                            <th class="text-right" >Price</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item,index) in items" :key="index">
                                            <td>{{item.line}}</td>
                                            <td>{{item.product}}</td>
                                            <td class="text-right">{{item.qty}}</td>
                                            <td class="text-right">{{item.rate}}</td>
                                            <td class="text-right">{{item.price}}</td>
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
                                                  class="mx-0 py-0 mt-0"
                                                  flat
                                                  clearable
                                                  hide-no-data
                                                    hide-details
                                                ></v-autocomplete>
                                            </td>
                                            <td><v-text-field class="text-right" style="direction:rtl;" v-model="qty"></v-text-field></td>
                                            <td><v-text-field class="text-right" style="direction:rtl;" :loading="rateLoading" v-model="rate"></v-text-field></td>
                                            <td><v-text-field class="text-right" style="direction:rtl;" readonly :rules="[rules.decimal]" :value="parseInt(qty)*parseFloat(rate)"></v-text-field></td>
                                            <td><v-btn icon rounded small :disabled="productSelect == null || isNaN(parseInt(qty)*parseFloat(rate))" @click.stop="addLine"><v-icon>mdi-plus-circle</v-icon></v-btn></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                            <td class="text-right">Discount</td>
                                            <td><v-text-field class="text-end" style="direction:rtl;" v-model="discount" append-icon="mdi-minus"></v-text-field></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                            <td class="text-right">Total</td>
                                            <td><v-text-field readonly class="text-end" style="direction:rtl;" v-model="total"></v-text-field></td>
                                            <td></td>
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
            discount : 0,
            total : 0,
            items:[],
            qty:1,
            rate:0,
            rateLoading:false,
            pricelist_items:[],
            pricelistSelect:null,
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
    mounted(){
        axios.get('sale/orders/getpl').then((response)=>{
            if(response.status == 200){
                this.pricelist_items = response.data.items
            }
        })
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
        productSelect:{
            handler(){
                this.setRate() 
            }
        },
        pricelistSelect:{
            handler(){
                this.setRate() 
                if(this.items.length > 0){
                    Object.keys(this.items).forEach((key)=>{
                        axios.get('sale/orders/getrate?&id='+this.items[key].id+'&pl='+this.pricelistSelect).then((response)=>{
                            if(response.status == 200){
                                var gst = response.data.gst
                                var landing_price = response.data.landing_price
                                var value = response.data.value
                                this.items[key].rate = ((parseFloat(landing_price)*((parseFloat(value)/100)+1))*((parseFloat(gst)/100)+1)).toFixed(2)
                                this.items[key].price = parseFloat(this.items[key].rate)*parseInt(this.items[key].qty)
                                this.updateTotal()
                            }
                        })

                    })
                }
            }
        },
        discount:{
            handler(){
                this.updateTotal()
            }
        }
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
            this.updateTotal()
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
            this.updateTotal()
        },
        create(){
            var fD = new FormData()
            fD.append('customer_id',this.customerSelect)
            fD.append('items',JSON.stringify(this.items))
            fD.append('discount',this.discount)
            fD.append('total',this.total)
            axios.post('sale/orders/add',fD).then((response)=>{
                if(response.status == 200){
                    this.$router.push('/sale/orders')
                }
                else{
                    alert('Something went wrong!')
                }
            })
        },
        updateTotal(){
            var temp = 0;
            Object.keys(this.items).forEach((key)=>{
                temp = parseFloat(temp)+parseFloat(this.items[key].price)
            })
            this.total = parseFloat(temp)-parseFloat(this.discount)
        },
        setRate(){
            if(this.pricelistSelect != null && (this.productSelect != undefined ||this.productSelect != null)){
                this.rateLoading = true
                axios.get('sale/orders/getrate?&id='+this.productSelect+'&pl='+this.pricelistSelect).then((response)=>{
                    if(response.status == 200){
                        var gst = response.data.gst
                        var landing_price = response.data.landing_price
                        var value = response.data.value
                        this.rate = ((parseFloat(landing_price)*((parseFloat(value)/100)+1))*((parseFloat(gst)/100)+1)).toFixed(2)
                        this.rateLoading = false
                    }
                })
            }
            if(this.productSelect == undefined){
                this.rate = 0
            }
        }
    }
}
</script>
