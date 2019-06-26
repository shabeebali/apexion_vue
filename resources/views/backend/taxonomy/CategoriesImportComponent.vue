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
						
						<v-layout row wrap>
							<v-flex xs12>
								<p class="pl-4">Please upload Excel file (.xslx) only</p>
							</v-flex>
							<v-flex xs12>
								<upload-btn
									@file-update="update"
								  	title="Click Here to select file.."
								>
								  <template slot="icon">
								    <v-icon>add</v-icon>
								  </template>
								</upload-btn>
							</v-flex>
							<v-flex xs12 lg3 md4 sm4 px-4>
								<v-select label="Category Type" :items="types" v-model="type_id" :error-messages="type_error"></v-select>
							</v-flex>
							<v-flex xs12 pa-3>
								<v-btn color="blue" dark @click="upload">
									Upload
									<v-icon class="pl-1">
										cloud_upload
									</v-icon>
								</v-btn>
							</v-flex>
							<v-flex xs12>
								<v-alert v-model="alert" :type="alert_type" dismissible>
									<span v-html="alert_messages"></span>
								</v-alert>
							</v-flex>
						</v-layout>
					</v-card-text>	
				</v-card>
			</v-container>
		</v-content>
	</div>
</template>
<script>
	import UploadButton from 'vuetify-upload-button'
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
				breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Products',
                        disabled:false,
                        to:'/products/list'
                    },
                    {
                        text:'Categories',
                        disabled:false,
                        to:'/products/categories/list'
                    },
                    {
                        text:'Import',
                        disabled:true,
                        to:'/products/categories/import'
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
						if(response.status == 200 && response.data.status == 'success'){
							this.alert_messages = response.data.message
							this.alert_type = 'success'
							this.alert = true
						}
					})
				}
			},
		},
		components: {
		  'upload-btn': UploadButton
		}
	}
</script>