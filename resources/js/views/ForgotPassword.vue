<template>
    <div>
        <div class="container" v-if="!this.loading">
        <h1>Reset password...</h1>
        <hr>
        <FormulateForm @submit="handleResetPassword">
            <h3>Provide us your email to send you reset link...</h3>
            <FormulateInput
                type="email"
                label="Account email address"
                placeholder="Email address"
                name="email"
                validation="required|email"
                v-model="email"
            />
            <FormulateInput type="submit" label="Send" />
        </FormulateForm>
    </div>
    <loading v-else/>
    </div>
</template>

<script>
import User from '../apis/User';
export default {
    data() {
        return {
            email: "",
            loading: true
        }
    },
    mounted() {this.loading = false},
    methods: {
        async handleResetPassword() {
            this.loading = true
            await User.forgotPassword({email: this.email}).then((res) =>{
                if (res.status === 200) {
                    this.$toasted.success("Password reset email has been sent. Check your email.")
                    this.$router.push("/");
                }
                else {
                    this.$toasted.error("Something went wrong. Check your email or try it later.")
                }
                this.loading = false;
            })
        },
    },
};
</script>

<style></style>
