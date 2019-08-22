<template>
	<div>
		<v-content>
			<div>
	            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
	        </div>
            <v-container fluid class="pt-0">
				<v-card>
					<v-subheader class="headline">Category Import</v-subheader>
					<v-card-text>
						
						<v-row wrap>
							<v-col class="col-3">
								<v-file-input label="Click Here to select file.." persistent-hint hint="Please upload Excel file (.xslx) only" @change="update"></v-file-input>
							</v-col>
							<v-col class="col-3">
								<v-select label="Category Type" :items="types" v-model="type_id" :error-messages="type_error"></v-select>
							</v-col>
							<v-col class="col-2">
								<v-select label="Method" :items="methods" v-model="method" :error-messages="method_error"></v-select>
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
				types:[],
				type_id:0,
				type_error:'',
				method:'create',
				method_error:'',
				methods:[
					{
						'text':'Create',
						'value':'create'
					},
					{
						'text':'Update',
						'value':'update'
					}
				],
				breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Categories',
                        disabled:false,
                        to:'/inventory/categories',
                        exact:true,
                    },
                    {
                        text:'Import',
                        disabled:true,
                        to:'/inventory/categories/import'
                    },  
                ]
			}
		},
		mounted(){
			axios.get('products/categories/types').then((response)=>{
				this.types = response.data.data
			})
		},
		watch:{
			type_id:{
				handler(){
					this.type_error = ''
				}
			}
		},
		methods:{
			update(file){
				this.file = file
			},
			upload(){
				if(this.file == '' || this.file === undefined){
					alert('Please Select a file first')
				}
				else if(this.type_id == 0 || this.type_id === undefined){
					this.type_error = 'This field is required'
				}
				else{
					this.type_error = ''
					var fD = new FormData()
					fD.append('file',this.file)
					fD.append('type_id',this.type_id)
					fD.append('method',this.method)
					axios.post('/products/categories/import',fD,{
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