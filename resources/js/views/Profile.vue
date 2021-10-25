<template>
    <div>
      <h1>Your profile</h1>
        <p>
            {{this.user}}
        </p>
    </div>
</template>

<script>
import User from "../apis/User";

export default {
    name: "Profile",
    data() {
        return {
            user: {
                name: '',
                email: '',
            }
        }
    },
    mounted() {
        this.getUser();
    },
    methods: {
        async getUser() {
            const response = await User.loggedUser();

            if (response.data === '') {
                this.$store.dispatch('logout');
                await User.logout();
            }
            else {
                this.user = response.data;

                /*this.user.name = response.data.name;
                this.user.email = response.data.email;*/
            }
        }
    }
}
</script>

<style scoped>

</style>
