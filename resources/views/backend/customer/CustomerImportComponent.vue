<template>
	<div>
		<v-content>
			<div>
	            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
	        </div>
            <v-container fluid class="pt-0">
				<v-card>
					<v-subheader class="headline">Customer Import</v-subheader>
					<v-card-text>
						
						<v-row wrap>
							<v-col class="col-3">
								<v-file-input label="Click Here to select file.." persistent-hint hint="Please upload Excel file (.xslx) only" @change="update"></v-file-input>
							</v-col>
							<v-col class="col-2">
								<v-select label="Method" :items="items" v-model="method"></v-select>
							</v-col>
							<v-col class="col-2" >
								<v-row>
									<v-col class="col-12 align-center">
										<v-btn color="blue" dark @click="upload">
											Upload
											<v-icon class="">
												cloud_upload
											</v-icon>
										</v-btn>
									</v-col>
								</v-row>
							</v-col>
							<v-col class="col-12">
								<v-alert v-model="alert" :type="alert_type" dismissible>
									<span v-html="alert_messages"></span>
								</v-alert>
							</v-col>
						</v-row>
					</v-card-text>	
				</v-card>
			</v-container>
		</v-content>
	</div>
</template>
<script>
	export default{
		data(){
			return{
				file:'',
				alert:false,
				alert_messages:'',
				alert_type:'success',
				breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Customers',
                        disabled:false,
                        to:'/customers',
                        exact:true,
                    },
                    {
                        text:'Import',
                        disabled:true,
                        to:'/customers/import'
                    },  
                ],
                items:[
                	{
                		'text':'Create',
                		'value':'create',
                	},
                	{
                		'text':'Update',
                		'value':'update',
                	},
                ],
                error:'',
                method:'create',
			}
		},
		mounted(){
		},
		methods:{
			update(file){
				this.file = file
			},
			upload(){
				if(this.file == '' || this.file === undefined){
					alert('Please Select a file first')
				}
				else{
					var fD = new FormData()
					fD.append('file',this.file)
					fD.append('method',this.method)
					axios.post('/customers/import',fD,{
						headers: {
					        'Content-Type': 'multipart/form-data'
					    }
					}).then((response)=>{
						if(response.status == 200 && response.data.status == 'failed'){
							var str = '';
							Object.keys(response.data.messages).forEach((key)=>{
								str += '<p>Error in Line:'+(parseInt(key)+1)+'<br><ul>'
								Object.keys(response.data.messages[key]).forEach((item)=>{
									str+='<li>'+response.data.messages[key][item].message+'</li>'
								})
								str+='</ul></p>'
							})
							this.alert_messages = str;
							this.alert_type = 'error'
							this.alert = true
						}
						if(response.status == 200 && response.data.status == 'file_failed'){
							this.alert_messages = response.data.message
							this.alert_type = 'error'
							this.alert = true
						}
						if(response.status == 200 && response.data.status == 'success'){
							this.alert_messages = response.data.message
							this.alert_type = 'success'
							this.alert = true
						}
					})
				}
			},
		},
	}
</script>