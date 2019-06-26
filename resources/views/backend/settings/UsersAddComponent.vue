<template>
	<div>
		<v-content>
            <v-container fluid>
            	<v-card class="pa-2">
            		<v-subheader class="headline">Add User</v-subheader>
            		<v-layout row wrap>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="Name" v-model="formdata.name.value" :error-messages="formdata.name.error"/>
            			</v-flex>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="E-mail" v-model="formdata.email.value" :error-messages="formdata.email.error"/>
            			</v-flex>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="Password" v-model="formdata.password.value" :error-messages="formdata.password.error" :type="'password'"/>
            			</v-flex>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="Confirm Password" v-model="formdata.confirm_password.value" :error-messages="formdata.confirm_password.error" :type="'password'"/>
            			</v-flex>
            			<v-flex xs12 md6 px-4>
            				<v-select :items="formdata.roles.items" item-text="name" item-value="id" label="Role" :error-messages="formdata.roles.error" multiple v-model="formdata.roles.value" />
            			</v-flex>
            		</v-layout>
	            	<v-card-actions class="text-xs-right">
	            		<v-layout align-center justify-end>
	            			<v-flex xs12>
	            				<v-btn @click.stop="save" color="primary">Save</v-btn>
	            			</v-flex>
	            		</v-layout>
	            	</v-card-actions>
            	</v-card>
            </v-container>
        </v-content>
	</div>
</template>
<script>
	export default{
		data(){
			return{
				formdata:{
					name:{
						'error':'',
						'value':'',
					},
					email:{
						'error':'',
						'value':'',
					},
					roles:{
						'error':'',
						'value':[],
						'items':[],
					},
					password:{
						'error':'',
						'value':'',
					},
					confirm_password:{
						'error':'',
						'value':'',
					}
				}
			}
		},
		mounted(){
			axios.get('settings/users/add').then((response)=>{
				this.formdata.roles.items = response.data.roles
			})
		},
		methods:{
			save(){
				var fD = new FormData()
				Object.keys(this.formdata).forEach((key)=>{
					fD.append(key,this.formdata[key].value)
				})
				axios.post('settings/users/add',fD).then((response)=>{
					if(response.data.message == 'failed'){
						Object.keys(this.formdata).forEach((key)=>{
							this.formdata[key].error = ''
						})
						Object.keys(response.data.errors).forEach((key)=>{
							this.formdata[key].error = response.data.errors[key][0]
						})
					}
					if(response.data.message == 'success'){
						this.$router.push('/settings/users')
					}
				})
			}
		}
	}
</script>