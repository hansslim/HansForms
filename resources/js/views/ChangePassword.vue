<template>
    <div class="container">
        <div v-if="!this.loading">
            <h1>Change password</h1>
            <hr>
        <FormulateForm @submit="handleChangePassword">
            <FormulateInput
                class="mb-2"
                type="password"
                v-model="passwords.old_password"
                label="Old Password"
                :validation="[['required']]"
                input-class="form-control"
                placeholder="Old Password"
            />
            <FormulateInput
                class="mb-2"
                type="password"
                v-model="passwords.new_password"
                label="New Password"
                validation="^required|min:8,length"
                input-class="form-control"
                placeholder="New Password"
            />
            <FormulateInput
                class="mb-2"
                type="password"
                v-model="passwords.new_password_confirmation"
                label="New Password Confirmation"
                validation="^required|min:8,length"
                input-class="form-control"
                placeholder="Repeat New Password"
            />
            <hr>
            <FormulateInput type="submit" label="Change password" />
        </FormulateForm>
        </div>
        <loading v-else/>
    </div>
</template>

<script>
import User from "../apis/User";
export default {
    data() {
        return {
            passwords: {
                old_password: "",
                new_password: "",
                new_password_confirmation: "",
            },
            loading: true,
        };
    },
    mounted() {
        this.loading = false;
    },
    methods: {
        async handleChangePassword() {
            this.loading = true;
            try {
                this.$toasted.clear()
                if (this.passwords.new_password_confirmation !== this.passwords.new_password){
                    throw new Error("New password and it's confirmation don't match!")
                }
                if (this.passwords.old_password === this.passwords.new_password){
                    throw new Error("Old and new passwords can't be same!")
                }
                await User.changePassword(this.passwords).then((res) => {
                    if (res.status === 200) {
                        this.$toasted.success('Password was changed successfully.');
                        this.$router.push('/profile')
                    }
                    else throw new Error(res.data)
                });
            } catch (e) {
                this.$toasted.error(e)
            }

            this.loading = false;
        },
    },
};
</script>

<style></style>
