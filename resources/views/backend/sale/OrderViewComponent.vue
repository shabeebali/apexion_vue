<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" light tabs>
                <v-toolbar-title>Order Reference No: {{ref}}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn depressed color="" v-if="status === 'draft'" tile @click.stop="edit">Edit</v-btn>
                <v-btn depressed color="primary" tile @click.stop="complete" v-if="status === 'processing'">Complete</v-btn>
            </v-toolbar>
            <v-card>
                <v-row class="mx-0">
                    <v-col cols=6>
                        <v-text-field readonly label="Customer" append-icon="file_copy" @click:append="textCopy(customer)" :value="customer"></v-text-field>
                    </v-col>
                    <v-col cols=3>
                        <v-text-field readonly label="Pricelist" :value="pricelist"></v-text-field>
                    </v-col>
                    <v-col cols=3>
                        <v-text-field readonly label="Created By" :value="created_by"></v-text-field>
                    </v-col>
                    <v-col cols=3 class="">
                        <v-text-field readonly label="Sales Person" :value="salesperson"></v-text-field>
                    </v-col>
                    <v-col cols=3 class="">
                        <div :class="gstColor">GST Included: {{gstText}}</div>
                    </v-col>
                </v-row>
                <v-row class="mx-0">
                    <v-col class="pa-3">
                        <v-card>
                            <v-card-text>
                                <v-simple-table >
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-left">Product</th>
                                            <th class="text-right" >Quantity</th>
                                            <th class="text-right" >Rate</th>
                                            <th class="text-right" >GST</th>
                                            <th class="text-right" >Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item,index) in items" :key="index">
                                            <td>{{item.line}}</td>
                                            <td>
                                                <v-row>
                                                    <v-col cols=10>
                                                        {{item.product}}
                                                    </v-col>
                                                    <v-col xs2 align-self-center class="text-xs-right">
                                                        <v-tooltip bottom>
                                                            <template v-slot:activator="{ on }">
                                                                <v-btn icon v-on="on" @click="textCopy(item.product)">
                                                                    <v-icon  light>file_copy</v-icon>
                                                                </v-btn>
                                                            </template>
                                                            <span>Copy</span>
                                                        </v-tooltip>
                                                    </v-col>
                                                </v-row>
                                            </td>
                                            <td class="text-right">{{item.qty}}</td>
                                            <td class="text-right">{{item.rate}}</td>
                                            <td class="text-right">{{item.gst}}</td>
                                            <td class="text-right">{{item.price}}</td>
                                        </tr>
                                        <tr v-if="gst_included === '0'">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Tax</td>
                                            <td class="text-right">{{tax}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Discount</td>
                                            <td class="text-right">-{{discount}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Freight</td>
                                            <td class="text-right">+{{freight}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Total</td>
                                            <td class="text-right">{{total}}</td>
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
            pricelist:'',
            status:'',
            id:null,
            ref:'',
            freight:0,
            gstColor:'',
            gstText:'',
            created_by:'',
            salesperson:'',
            tax:0,
            discount : 0,
            total : 0,
            customer:'',
            order_id:'',
            items:[],
            gst_included:'',
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
                    text:'View',
                    disabled:true,
                    to:'/sale/orders/view'
                },  
            ]
        }
    },
    watch:{
        gst_included:{
            handler(){
                if(this.gst_included ==='1'){
                    this.gstText = 'Yes'
                    this.gstColor = 'green--text'
                }
                else{
                    this.gstColor="red--text"
                    this.gstText = 'No'
                }
            }
        }
    },
    mounted(){
        axios.get('sale/orders/view/'+this.$route.params.id).then((response)=>{
            this.id = response.data.id
            this.total = response.data.total
            this.discount = response.data.discount
            this.salesperson = response.data.salesperson
            this.created_by = response.data.created_by
            this.pricelist = response.data.pricelist
            this.freight = response.data.freight
            this.tax = response.data.tax
            this.gst_included = String(parseInt(response.data.gst_included))
            this.ref = response.data.ref
            this.items = response.data.items
            this.customer = response.data.customer
            this.status = response.data.status
        })
    },
    methods:{
        textCopy(text){
            const el = document.createElement('textarea');
            el.value = text;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        },
        complete(){
            var fD = new FormData()
            fD.append('id',this.$route.params.id)
            axios.post('sale/orders/complete',fD).then((response)=>{
                if(response.status == 200){
                    this.$router.push('/sale/orders')
                }
            })
        },
        edit(){
           this.$router.push('/sale/orders/edit/'+this.id)
        }
    }
}
</script>
