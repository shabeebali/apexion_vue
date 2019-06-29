<template>
	<div>
		<v-content>
            <v-container fluid>
            	<v-card class="pa-2">
            		<v-subheader class="headline">Add User Role</v-subheader>
            		<v-layout row wrap>
            			<v-flex xs12 px-4>
            				<v-text-field label="Name" v-model="formdata.name" :error-messages="formdata.error"/>
            			</v-flex>
            		</v-layout>
            		<v-card elevation-0>
            			<v-layout row wrap>
		            		<v-subheader class="subheading">Permissions</v-subheader>
		            		<v-spacer></v-spacer>
		            		<v-btn @click.stop="reset">Reset</v-btn>
		            		<v-btn @click.stop="selectAll">Select All</v-btn>
		            	</v-layout>
	            		<v-divider></v-divider>
	            		<v-layout row wrap px-4>
	            			<v-flex xs3 v-for="p in permissions" :key="p.id">
	            				<v-switch :label="p.name" v-model="p.value"/>
	            			</v-flex>
	            		</v-layout>
	            	</v-card>
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
<script >
	export default{
		data(){
			return{
				permissions:[],
				formdata:{
					'name':'',
					'error':''
				}
			}
		},
		mounted(){
			axios.get('/settings/permissions').then((response)=>{
				this.permissions = response.data.permissions
			})
		},
		methods:{
			save(){
				var fD = new FormData()
				fD.append('name',this.formdata.name)
				var permission_ids = new Array()
				Object.keys(this.permissions).forEach((key)=>{
					if(this.permissions[key].value === true){
						permission_ids.push(this.permissions[key].id)
					}
				})
				fD.append('permission_ids',permission_ids)
				axios.post('/settings/users/roles/add',fD).then((response)=>{
					if(response.status == 200 && response.data.message == 'failed'){
						this.formdata.name = response.data.request.name
						this.formdata.error = response.data.errors.name[0]
					}
					if(response.status == 200 && response.data.message == 'success'){
						this.$router.push('/settings/users/roles/list')
					}
				})
			},
			selectAll(){
				Object.keys(this.permissions).forEach((key)=>{
					this.permissions[key].value = true
				})
				console.log(this.permissions)
			},
			reset(){
				Object.keys(this.permissions).forEach((key)=>{
					this.permissions[key].value = false
				})
			},
		}
	}
</script>