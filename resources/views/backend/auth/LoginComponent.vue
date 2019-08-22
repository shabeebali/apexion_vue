<template>
	<div class="teal" style="height: 100%; background-image: url('../svg/apexion_logo.svg');background-size: cover; background-position-y: center;">
		<v-layout row wrap class="pt-5">
			<v-flex xs12 md4 px-2>
			</v-flex>
			<v-flex xs12 md4 px-2>
				<v-card>
					<v-card-title class="align-center">
							<h1 class="darken-4--text teal--text">Login</h1>
						</v-card-title>
					<v-card-text>						
						<v-layout row wrap>
                            <v-flex xs12 px-2>
                                <v-text-field label="Email" v-model="formdata.email.value" :error-messages="formdata.email.error"></v-text-field>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 px-2>
                                <v-text-field label="Password" type="password" v-model="formdata.password.value" :error-messages="formdata.password.error"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    <v-card-actions>
                    	<v-btn color="primary" style="width:100%" @click.stop="login">Login</v-btn>
                    </v-card-actions>
                </v-card>
			</v-flex>
			<v-flex xs12 md4 px-2>
			</v-flex>
		</v-layout>
	</div>
</template>
<script>
	export default{
		data(){
			return{
				formdata:{
					email:{
						value:'',error:''
					},
					password:{
						value:'',error:''
					}
				}
			}
		},
		mounted(){
			var localToken = localStorage.token
			if(localToken !== undefined){
				this.$router.push('/')
			}
		},
		methods:{
			login(){
				var fD = new FormData()
				fD.append('email',this.formdata.email.value)
				fD.append('password',this.formdata.password.value)
				axios.post('login',fD).then((response)=>{
					if(response.status == 200){
						localStorage.setItem('access_token', response.data.access_token);
						localStorage.setItem('refresh_token', response.data.refresh_token);
						window.axios.defaults.headers.common['Authorization'] = 'Bearer '+ response.data.access_token
						this.$router.push('/')
					}
					else{
						Object.keys(response.data.errors).forEach((key)=>{
                            this.formdata[key]['error'] = response.data.errors[key]
                        })
					}
				})
			}
		}
	}
</script>