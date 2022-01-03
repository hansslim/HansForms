<template>
    <div class="container">
        <div v-if="!loading">
            <h1>Log In</h1>
            <hr>
            <FormulateForm @submit="handleLogin">
                <FormulateInput
                    class="mb-2"
                    type="email"
                    v-model="user.email"
                    label="Email"
                    :validation="[['required']]"
                    input-class="form-control"
                    placeholder="Enter Email"
                />
                <FormulateInput
                    class="mb-2"
                    type="password"
                    v-model="user.password"
                    label="Password"
                    :validation="[['required']]"
                    input-class="form-control"
                    placeholder="Enter Password"
                />
                <FormulateInput
                    type="submit"
                    label="Login"
                />
            </FormulateForm>
        </div>
        <div v-else>loading</div>
    </div>
</template>

<script>
import User from '../apis/User';

export default {
    name: "Login",
    data() {
        return {
            user: {
                email: '',
                password: ''
            },
            errored: {},
            loading: true
        }
    },
    mounted() {
        this.loading = false;
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            await User.login(this.user).then((res)=>{
                if (res.status === 200) {
                    this.$store.dispatch('login', res.data);
                    this.$router.push('/');
                }
                else throw new Error();
            }).catch((error)=>{
                console.log(error);
                alert("Bad credentials")
            }).finally(()=>{
                this.loading = false;
            });
        },
    }
}
</script>

<style scoped>
</style>
