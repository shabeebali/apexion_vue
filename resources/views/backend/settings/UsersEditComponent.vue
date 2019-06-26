<template>
	<div>
		<v-content>
            <v-container fluid>
            	<v-card class="pa-2">
            		<v-layout row>
            			<v-subheader class="headline">Add User</v-subheader>
            			<v-spacer></v-spacer>
            			<v-btn color="primary" @click="passwordDialog = true">Change Password</v-btn>
            		</v-layout>
            		<v-layout row wrap>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="Name" v-model="formdata.name.value" :error-messages="formdata.name.error"/>
            			</v-flex>
            			<v-flex xs12 md6 px-4>
            				<v-text-field label="E-mail" v-model="formdata.email.value" :error-messages="formdata.email.error"/>
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
        <v-dialog v-model="passwordDialog" lazy persistent>
            <v-card v-if="passwordSuccess" class="pa-4">
                <v-layout row wrap>
                    <v-flex xs12 >
                        <div class="text-xs-center">
                            <v-icon x-large color="green lighten-1">check_circle</v-icon>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="text-xs-center">
                            <div>Saved</div>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card>
            <v-card v-else>
                <v-card-title>
                    <span class="headline">Change Password for user : {{formdata.name.value}}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 md6 px-4>
            					<v-text-field label="New Password *" v-model="passwordData.password.value" :error-messages="passwordData.password.error" :type="'password'"/>
	            			</v-flex>
	            			<v-flex xs12 md6 px-4>
	            				<v-text-field label="Confirm Password *" v-model="passwordData.confirm_password.value" :error-messages="passwordData.confirm_password.error" :type="'password'"/>
	            			</v-flex>
                        </v-layout>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="resetDialog(); passwordDialog =  false">Close</v-btn>
                    <v-btn color="blue darken-1" flat @click="savePassword">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
	</div>
</template>
<script>
	export default{
		data(){
			return{
				passwordDialog:false,
				passwordSuccess:false,
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
				},
				passwordData:{
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
			axios.get('settings/users/edit/'+this.$route.params.id).then((response)=>{
				this.formdata = response.data.formdata
			})
		},
		methods:{
			save(){
				var fD = new FormData()
				Object.keys(this.formdata).forEach((key)=>{
					fD.append(key,this.formdata[key].value)
				})
				axios.post('settings/users/edit/'+this.$route.params.id,fD).then((response)=>{
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
			},
			savePassword(){
				var fD = new FormData()
				Object.keys(this.passwordData).forEach((key)=>{
					fD.append(key,this.passwordData[key].value)
				})
				axios.post('settings/users/edit/change_password/'+this.$route.params.id,fD).then((response)=>{
					if(response.data.message == 'failed'){
						this.resetDialog()
						Object.keys(response.data.errors).forEach((key)=>{
							this.passwordData[key].error = response.data.errors[key][0]
						})
					}
					if(response.data.message == 'success'){
						this.passwordSuccess=true
                        setTimeout(()=>{
                            this.passwordDialog = false
                            this.passwordSuccess=false
                        },600);
                        this.resetDialog()
					}
				})
			},
			resetDialog(){
				Object.keys(this.passwordData).forEach((key)=>{
					this.passwordData[key].error = ''
				})
			}
		}
	}
</script>