<template>
    <div class="mb-2">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <router-link class="navbar-brand" to="/">
                <span class="h3 font-size-bold">
                    <!-- todo: remove beta -->
                    HansForms <small class="text-muted" style="color: white !important; opacity: 70%;">Beta</small>
                </span>
            </router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">
                            {{ accountText }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <template v-if="authenticated">
                                <router-link class="dropdown-item" to="/profile">Profile</router-link>
                                <router-link class="dropdown-item" to="/create_form">Create new...</router-link>
                                <a class="dropdown-item" href="#" @click.prevent="logout">Logout</a>
                            </template>
                            <template v-else>
                                <router-link class="dropdown-item" to="/login">Login</router-link>
                                <router-link class="dropdown-item" to="/register">Register</router-link>
                            </template>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>

<script>
import User from "../apis/User";

export default {
    name: "Header",
    computed: {
        accountText() {
            if (this.$store.getters['user'] !== null) {
                return this.$store.getters['user'].name;
            } else return "Account";
        },
        authenticated() {
            return this.$store.getters['authenticated'];
        }
    },
    methods: {
        async logout() {
            try {
                await User.logout();
                this.$store.dispatch('logout');
                this.$router.push('/login');
            } catch (error) {
                console.log(error);
            }
        }
    }
}

</script>

<style scoped>

</style>
