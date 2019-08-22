<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
            	<v-card class="pa-4">
            		<v-row row wrap>
            			<v-col sm=6 cols=12 class="pr-2 mb-2">
                            <v-row row wrap>
                                <v-col cols=12 mb-2>
                    				<v-card color="blue-grey darken-4 white--text" class="pa-2">
                    					<v-card-title>
                    						Products
                    					</v-card-title>
                    					<v-card-text>
                							<div class="mb-2" v-if="pending_count > 0">
                								<v-chip dark color="blue-grey darken-3" >
                									<v-avatar>
                										<v-icon>hourglass_empty</v-icon>
                									</v-avatar>
                									<router-link to="inventory/products/pending" class="white--text">
                										Products Pending Approval:
                									</router-link>
                								</v-chip>
            									<v-chip color="amber">
            										{{pending_count}}
            									</v-chip>
            								</div>
            								<div class="mb-2" v-if="tally_count > 0">
            									<v-chip dark color="blue-grey darken-3">
            										<v-avatar>
            											<v-icon>T</v-icon>
            										</v-avatar>
            										<router-link to="inventory/products/tally" class="white--text">
            											To be synced with Tally:
            										</router-link>
            									</v-chip>
            									<v-chip color="amber">
            										{{tally_count}}
            									</v-chip>
            								</div>
            								<div class="ma-0">
            									<v-chip dark color="blue-grey darken-3" >
            										<v-avatar>
            											<v-icon>list_alt</v-icon>
            										</v-avatar>
            										<router-link to="inventory/products" class="white--text">
            											Total Active Products
            										</router-link>
            									</v-chip>
            									<v-chip color="amber">
            										{{products_count}}
            									</v-chip>
            								</div>
                						</v-card-text>
                					</v-card>
                                </v-col>
                                <v-col cols=12>
                                    <v-card color="blue-grey darken-4 white--text" class="pa-2">
                                        <v-card-title>
                                            Customers
                                        </v-card-title>
                                        <v-card-text>
                                            <div v-if="pending_count > 0">
                                                <div class="ma-0">
                                                    <v-chip dark color="blue-grey darken-3" >
                                                        <v-avatar>
                                                            <v-icon>people</v-icon>
                                                        </v-avatar>
                                                        Total Customers :
                                                    </v-chip>
                                                    <v-chip color="amber">
                                                        {{customers_count}}
                                                    </v-chip>
                                                </div>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
        				</v-col>
        				<v-col sm=6 cols=12 class="pr-2 mb-2">
        					<v-row row wrap>
        						<v-col cols=12 mb-2>
                                    <v-card color="blue-grey darken-3 white--text" class="pa-2">
                                        <v-card-title>
                                            Notifications
                                        </v-card-title>
                                        <v-card-text>
                                            No Notifications
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-btn>View All</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-col>
		        				<v-col cols=12>
		        					<v-card color="blue-grey darken-3 white--text" class="pa-2">
		            					<v-card-title>
		            						Messages
		            					</v-card-title>
		            					<v-card-text>
		            						No Messages Today
		        						</v-card-text>
		        						<v-card-actions>
		        							<v-btn>View All</v-btn>
		        						</v-card-actions>
		        					</v-card>
		        				</v-col>
		        			</v-row>
        				</v-col>
        				<v-col cols=12 >
            				<v-card class="mx-auto text-xs-center"
							    color="primary"
							    dark
							>
            					<v-card-title>
            						Sale
            					</v-card-title>
            					<v-card-text>
            						<v-sheet color="rgba(0, 0, 0, .12)">
	            						<v-sparkline
								        :value="value"
										color="rgba(255, 255, 255, .7)"
										height="100"
										padding="24"
										stroke-linecap="round"
										sm=ooth
										>
								        </v-sparkline>
								    </v-sheet>
        						</v-card-text>
        					</v-card>
        				</v-col>
        			</v-row>
            	</v-card>
            </v-container>
        </v-content>    
    </div>
</template>
<script>
export default{
	 data(){
        return{
        	tally_count:0,
        	pending_count:0,
        	products_count:0,
        	quote:'',
            breadcrumbs:[
                {
                    text:'Home',
                    disabled:true,
                    to:'/'
                },
            ],
            value:[0,1,10,20,5,15,23,10]
        }
    },
    mounted(){
    	axios.get('dashboard').then((response)=>{
    		this.tally_count = response.data.tally_count
    		this.pending_count = response.data.pending_count
    		this.products_count = response.data.products_count
    		this.customers_count = response.data.customers_count
    	})
        this.$emit('top-menu-url','')
    }
}
</script>
