<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
            	<v-card class="pa-4">
            		<v-row row wrap>
            			<v-col cols=12 class="pr-2 mb-2">
                            <v-row :justify="justify">
                                <v-col v-for="item in main_menu_items" :key="item.index">
                                    <v-row>
                                        <v-col cols=12 class="text-center">
                                          <v-btn color="primary" fab dark :large="$vuetify.breakpoint.mdAndUp" :small="$vuetify.breakpoint.xsOnly" :medium="$vuetify.breakpoint.smAndUp" v-bind:to="item.route" @click="main_menu_dialog = false">
                                            <v-icon>
                                              {{item.icon}}
                                            </v-icon>
                                          </v-btn>
                                        </v-col cols=12>
                                        <v-col class="font-weight-bold text-uppercase text-center">
                                          {{item.name}}
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                            <v-row row wrap>
                                <v-col cols=6 mb-2>
                    				<v-card color="" class="pa-2">
                    					<v-card-title>
                    						Products
                    					</v-card-title>
                    					<v-card-text>
                							<Doughnut v-if="productDonut" :data="productDonutData" :options="productDonutOptions"/>
                						</v-card-text>
                					</v-card>
                                </v-col>
                                <v-col cols=6>
                                    <v-card color="" class="pa-2">
                                        <v-card-title>
                                            Customers
                                        </v-card-title>
                                        <v-card-text>
                                            <Doughnut v-if="customerDonut" :data="customerDonutData" :options="customerDonutOptions"/>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
        				</v-col>
        				<v-col cols=12 class="pr-2 mb-2">
        					
        				</v-col>
        				<v-col cols=12 >
            				
        				</v-col>
        			</v-row>
            	</v-card>
            </v-container>
        </v-content>    
    </div>
</template>
<script>
import Doughnut from '../../../js/components/donut.js'
export default{
	data(){
        return{
        	tally_count:0,
        	pending_count:0,
        	products_count:0,
        	quote:'',
            main_menu_items:[],
            breadcrumbs:[
                {
                    text:'Home',
                    disabled:true,
                    to:'/'
                },
            ],
            value:[0,1,10,20,5,15,23,10],
            values: [],
            productDonut:false,
            productDonutData:{
                'labels':['Active', 'Pending', 'To Tally' ],
                datasets: [
                    {
                      backgroundColor: [
                        '#41B883',
                        '#E46651',
                        '#00D8FF',
                      ],
                      data: []
                    }
                ]
            },
            productDonutOptions:{responsive: true, maintainAspectRatio: false},
            customerDonut:false,
            customerDonutData:{
                'labels':['Active', 'Pending', 'To Tally' ],
                datasets: [
                    {
                      backgroundColor: [
                        '#41B883',
                        '#E46651',
                        '#00D8FF',
                      ],
                      data: []
                    }
                ]
            },
            customerDonutOptions:{responsive: true, maintainAspectRatio: false},
        }
    },
    components:{
        'Doughnut': Doughnut,
    },
    mounted(){
        axios.get('/config/menu').then((response)=>{
            if(response.data){
              this.main_menu_items = response.data
            }
        })
    	axios.get('dashboard').then((response)=>{
            this.productDonutData.datasets[0].data.push(response.data.products_count, response.data.pending_count, response.data.tally_count)
    		//this.tally_count = response.data.tally_count
    		//this.pending_count = response.data.pending_count
    		//this.products_count = response.data.products_count
            
            this.customerDonutData.datasets[0].data.push(response.data.customers_count,0,0)
    		this.customers_count = response.data.customers_count
            this.productDonut = true
            this.customerDonut = true
    	})
        this.$emit('top-menu-url','')
    },
    methods: {
        dataFormat: function(a, b) {
            return this.names[a]+': '+b;
        }
    }
}
</script>
