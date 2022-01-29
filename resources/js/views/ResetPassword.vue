<template>
    <div>
        <div class="container" v-if="!this.loading && !this.errored">
            <h1>Reset password...</h1>
            <hr />
            <FormulateForm @submit="handleResetPassword">
                <h3>Set up your new password...</h3>
                <h5>Requested action is related to {{this.passwordReset.email}}.</h5>
                <FormulateInput
                    class="mb-2"
                    type="password"
                    v-model="passwordReset.password"
                    label="New Password"
                    validation="^required|min:8,length"
                    input-class="form-control"
                    placeholder="New Password"
                />
                <FormulateInput
                    class="mb-2"
                    type="password"
                    v-model="passwordReset.password_confirmation"
                    label="New Password Confirmation"
                    validation="^required|min:8,length"
                    input-class="form-control"
                    placeholder="Repeat New Password"
                />
                <FormulateInput type="submit" label="Change" />
            </FormulateForm>
        </div>
        <loading v-if="this.loading" />
        <div v-if="this.errored"><h1>Bad Request</h1></div>
    </div>
</template>

<script>
import User from "../apis/User";
import Loading from "../components/Loading.vue";
export default {
    components: { Loading },
    data() {
        return {
            passwordReset: {
                token: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            loading: true,
            errored: false,
        };
    },
    methods: {
        async handleResetPassword() {
            this.loading = true;
            if (this.passwordReset.password === this.passwordReset.password_confirmation) {
                await User.resetPassword(this.passwordReset).then((res) => {
                     if (res.status === 200) {
                        this.$toasted.success("Password has been reseted.")
                        this.$router.push("/login");
                    }
                    else {
                        this.$toasted.error("Password reset failed. This token is maybe expired.")
                    }
                    
                });
            }
            else  this.$toasted.error("Passwords aren't same.")
            this.loading = false;
        },
    },
    mounted() {
        const routeParams = this.$route.query;
        if (routeParams.hasOwnProperty("token") && routeParams.hasOwnProperty("email")) {
            this.passwordReset.token = routeParams.token;
            this.passwordReset.email = routeParams.email;
        }
        else {
            this.errored = true;
        }
        this.loading = false;
    },
};
</script>

<style lang="scss" scoped></style>
