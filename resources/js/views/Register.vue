<template>
    <div class="container">
        <div v-if="!loading">
            <h1>Sign Up</h1>
            <hr>
            <FormulateForm @submit="handleRegister">
                <FormulateInput
                    class="mb-2"
                    type="text"
                    v-model="user.name"
                    label="Username"
                    :validation="[['required']]"
                    input-class="form-control"
                    placeholder="Enter Username"
                />
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
                    class="mb-2"
                    type="password"
                    v-model="user.password_confirmation"
                    label="Password Confirmation"
                    :validation="[['required']]"
                    input-class="form-control"
                    placeholder="Repeat Password"
                />
                <FormulateInput
                    type="submit"
                    label="Register"
                />
            </FormulateForm>
        </div>
        <div v-else>loading</div>
    </div>
</template>

<script>
import User from "../apis/User";

export default {
    name: "Register",
    data() {
        return {
            user: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            errored: {},
            loading: true
        };
    },
    mounted() {
        this.loading = false;
    },
    methods: {
        async handleRegister() {
            this.loading = true
            await User.register(this.user).then((res) => {
                if (res.status === 200) {
                    this.$router.push('/login');
                } else throw new Error();
            }).catch((e) => {
                console.log(e);
                alert("Invalid data provided")
            }).finally(() => {
                this.loading = false;
            });
        }
    }
}
</script>

<style scoped>
</style>
