<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" light tabs>
                <v-toolbar-title>Order ID: {{order_id}}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn depressed color="primary" tile @click.stop="complete">Complete</v-btn>
            </v-toolbar>
            <v-card>
                <v-row class="mx-0">
                    <v-col cols=6>
                        <v-text-field readonly label="Customer" append-icon="file_copy" @click:append="textCopy(customer)" :value="customer"></v-text-field>
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
                                            <th class="text-center">Product</th>
                                            <th class="text-center" >Quantity</th>
                                            <th class="text-center" >Rate</th>
                                            <th class="text-center" >Price</th>
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
                                            <td class="text-right">{{item.price}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                            <td class="text-right">Discount</td>
                                            <td class="text-right">{{discount}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
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
            discount : 0,
            total : 0,
            customer:'',
            order_id:'',
            items:[],
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
    mounted(){
        axios.get('sale/orders/view/'+this.$route.params.id).then((response)=>{
            this.total = response.data.total
            this.discount = response.data.discount
            this.order_id = response.data.order_id
            this.items = response.data.items
            this.customer = response.data.customer
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
        }
    }
}
</script>
