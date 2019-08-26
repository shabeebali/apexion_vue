<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" light tabs>
                <v-toolbar-title>Create Order</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn tile color="" tile depressed class="mr-2"  :disabled="customerSelect == null && count == 1" @click="save">Save as Draft</v-btn>
                <v-btn tile color="primary" :disabled="customerSelect == null || count == 1" @click="create">Create</v-btn>
            </v-toolbar>
            <v-card>
                <v-row class="mx-0" justify="space-between">
                    <v-col cols=6>
                        <v-combobox
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
                        ></v-combobox>
                    </v-col>
                    <v-col cols=3>
                        <v-select v-model="pricelistSelect" :items="pricelist_items" label="Pricelist"></v-select>
                    </v-col>
                    <v-col cols=3>
                        <v-select v-model="userSelect" :items="users" label="Created By"></v-select>
                    </v-col>
                    <v-col cols=3 class="ml-4">
                        <v-select v-model="salesPersonSelect" :items="users" label="Sales Person"></v-select>
                    </v-col>
                    <v-col cols=3 class="">
                        <v-switch v-model="gstSwitch" true-value="1" false-value="0" label="GST Included"/>
                    </v-col>
                </v-row>
                <v-row class="mx-0">
                    <v-col class="pa-3">
                        <v-card elevation="10">
                            <v-card-text>
                                <v-simple-table dense >
                                    <thead>
                                        <tr>
                                            <th class="text-left">#</th>
                                            <th class="text-left">Product</th>
                                            <th class="text-right" >Quantity</th>
                                            <th class="text-right" >Rate</th>
                                            <th class="text-right" >GST</th>
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
                                            <td class="text-right">{{item.gst}}%</td>
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
                                            <td><v-text-field class="text-right" style="direction:rtl;" readonly :value="gst+'%'"></v-text-field></td>
                                            <td><v-text-field class="text-right" style="direction:rtl;" readonly :rules="[rules.decimal]" :value="(parseInt(qty)*parseFloat(rate)).toFixed(2)"></v-text-field></td>

                                            <td><v-btn icon rounded small :disabled="productSelect == null || isNaN(parseInt(qty)*parseFloat(rate))" @click.stop="addLine"><v-icon>mdi-plus-circle</v-icon></v-btn></td>
                                        </tr>
                                        <tr v-if="gstSwitch == '0'">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Tax</td>
                                            <td><v-text-field readonly class="text-end" style="direction:rtl;" v-model="tax" append-icon="mdi-plus"></v-text-field></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
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
                                            <td></td>
                                            <td class="text-right">Freight</td>
                                            <td><v-text-field class="text-end" style="direction:rtl;" v-model="freight" append-icon="mdi-plus"></v-text-field></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
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
            <v-snackbar v-model="snackbar" :timeout="timeout" color="success" bottom right>
                Saved
                <v-btn dark text @click="snackbar = false">Close</v-btn>
            </v-snackbar>
        </v-container>
    </v-content>
</template>
<script>
export default{
    data(){
        return{ 
            routerCheck:true,
            tax:0,
            freight:0,
            gstSwitch:'1',
            draftId:0,
            timeout:2000,
            snackbar:false,
            search:null,
            customerItems:[],
            count:1,
            users:[],
            userSelect:null,
            salesPersonSelect:null,
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
            gst:0,
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
        axios.get('sale/orders/users').then((response)=>{
            if(response.status == 200){
                this.users = response.data.users
                Object.keys(this.users).forEach((key)=>{
                    if(this.users[key].default){
                        this.userSelect = this.users[key].value
                    }
                })
            }
        })
    },
    beforeRouteLeave (to, from, next) {
        if(this.routerCheck){
            const answer = window.confirm('Do you really want to leave? your unsaved changes will be lost!')
            if (answer) {
                next()
            } 
            else {
                next(false)
            }
        }
        else{
            next()
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
        productSelect:{
            handler(){
                this.setRate() 
            }
        },
        pricelistSelect:{
            handler(){
                this.setRate() 
                this.updateBill()
            }
        },
        discount:{
            handler(){
                this.updateTotal()
            }
        },
        freight:{
            handler(){
                this.updateTotal()
            }
        },
        gstSwitch:{
            handler(){
                this.setRate() 
                this.updateBill()
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
                'gst':this.gst,
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
            this.routerCheck = false
            var fD = new FormData()
            fD.append('customer_id',this.customerSelect.value)
            fD.append('items',JSON.stringify(this.items))
            fD.append('discount',this.discount)
            fD.append('total',this.total)
            fD.append('freight',this.freight)
            fD.append('status','processing')
            fD.append('draft_id',this.draftId)
            fD.append('created_by',this.userSelect)
            fD.append('pricelist_id',this.pricelistSelect)
            fD.append('salesperson',this.salesPersonSelect)
            fD.append('gst_included',this.gstSwitch)
            fD.append('tax',this.tax)
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
            var temp2 = 0
            Object.keys(this.items).forEach((key)=>{
                temp = parseFloat(temp)+parseFloat(this.items[key].price)
                temp2 = (parseFloat(this.items[key].price)*(parseFloat(this.items[key].gst)/100)).toFixed(2)
            })
            console.log(temp2)
            this.tax = temp2
            if(this.gstSwitch === '0'){
                this.total = (parseFloat(temp)+parseFloat(temp2)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
            }
            else{
                this.total = (parseFloat(temp)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
            }
        },
        updateBill(){
            if(this.items.length > 0){
                Object.keys(this.items).forEach((key)=>{
                    axios.get('sale/orders/getrate?&id='+this.items[key].id+'&pl='+this.pricelistSelect).then((response)=>{
                        if(response.status == 200){
                            var gst = response.data.gst
                            var landing_price = response.data.landing_price
                            var value = response.data.value
                            this.gst = response.data.gst
                            this.items[key].gst = response.data.gst
                            if(this.gstSwitch === '0'){
                                this.items[key].rate = (parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
                            }
                            else {
                                this.items[key].rate = (((parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
)*((parseFloat(gst)/100)+1)).toFixed(2)
                            }
                            this.items[key].price = (parseFloat(this.items[key].rate)*parseInt(this.items[key].qty)).toFixed(2)
                            this.updateTotal()
                        }
                    })

                })
            }   
        },
        setRate(){
            if(this.pricelistSelect != null && (this.productSelect != undefined ||this.productSelect != null)){
                this.rateLoading = true
                axios.get('sale/orders/getrate?&id='+this.productSelect+'&pl='+this.pricelistSelect).then((response)=>{
                    if(response.status == 200){
                        var gst = response.data.gst
                        var landing_price = response.data.landing_price
                        var value = response.data.value //value = margin
                        this.gst = response.data.gst
                        if(this.gstSwitch === '0'){
                            this.rate = (parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
                        }
                        else {
                            this.rate = (((parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
)*((parseFloat(gst)/100)+1)).toFixed(2)
                        }
                        this.rateLoading = false
                    }
                })
            }
            if(this.productSelect == undefined){
                this.rate = 0
            }
        },
        save(){
            this.routerCheck = false
            var fD = new FormData()
            fD.append('customer_id',this.customerSelect.value)
            fD.append('items',JSON.stringify(this.items))
            fD.append('discount',this.discount)
            fD.append('total',this.total)
            fD.append('freight',this.freight)
            fD.append('status','draft')
            fD.append('draft_id',this.draftId)
            fD.append('created_by',this.userSelect)
            fD.append('pricelist_id',this.pricelistSelect)
            fD.append('salesperson',this.salesPersonSelect)
            fD.append('gst_included',this.gstSwitch)
            fD.append('tax',this.tax)
            axios.post('sale/orders/add',fD).then((response)=>{
                if(response.status == 200){
                    this.draftId = response.data.draft_id
                    this.snackbar = true
                    this.routerCheck = true
                }
                else{
                    alert('Something went wrong!')
                }
            })
        }
    }
}
</script>
