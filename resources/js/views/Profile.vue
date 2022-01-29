<template>
    <div>
        <div class="container" v-if="!this.loading">
            <h1>Your profile</h1>
            <hr>
            <div class="text-left container">
                <h4>Name: {{ this.user.name }}</h4>
                <hr />
                <h4>Email: {{ this.user.email }}</h4>
                <hr />
                <h5>Registered in {{ this.user.created_at }}</h5>
                <hr />
                <div>
                    <FormulateInput
                        type="button"
                        label="Change password"
                        @click="handleChangePassword"
                    />
                    <FormulateInput
                        type="button"
                        label="Delete account"
                        @click="handleDeleteAccount"
                    />
                </div>
            </div>
        </div>
        <loading v-else/>
    </div>
</template>

<script>
import User from "../apis/User";
import Loading from '../components/Loading.vue';

export default {
  components: { Loading },
    name: "Profile",
    data() {
        return {
            user: {
                name: "",
                email: "",
                created_at: "",
            },
            loading: true,
        };
    },
    async mounted() {
        this.loading = true;
        await User.loggedUser().then((res) => {
            if (res.status === 200) {
                this.user = res.data;
                this.user.created_at = this.user.created_at.split("T")[0];
            } else {
                this.$store.dispatch("logout");
            }
        });
        this.loading = false
    },
    methods: {
        handleChangePassword() {
            this.$router.push('/change_password');
        },
        handleDeleteAccount() {},
    },
};
</script>

<style scoped></style>
