<template>
    <div class="container">
        <form @submit.prevent="login">
            <h1>Log In</h1>
            <hr>
            <div class="container">
                <label for="username"><b>Email</b></label>
                <input v-model="user.email" type="text" placeholder="Enter Email" name="username" id="username"
                       required>

                <label for="password"><b>Password</b></label>
                <input v-model="user.password" type="password" placeholder="Enter Password" name="password"
                       id="password" required>

                <button type="submit">Login</button>

            </div>
        </form>
    </div>
</template>

<script>

import User from '../apis/User';
import store from "../store";
export default {
    name: "Login",
    data() {
        return {
            user: {
                email: '',
                password: ''
            },
            error: {},
            loading: false
        }
    },
    methods: {
        async login() {
            try {
                const response = await User.login(this.user);
                if (response.status === 200) {
                    this.$store.dispatch('login', response.data);
                    this.$router.push('/');
                }
                else throw new Error();
            } catch (error) {
               console.log(error);
               alert("Bad credentials")
            }
        },
    }
}
</script>

<style scoped>
/* Full-width inputs */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Set a style for all buttons */
button {
    background-color: #04AA6D;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

/* Add a hover effect for buttons */
button:hover {
    opacity: 0.8;
}

/* Extra style for the cancel button (red) */
.cancelbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #f44336;
}

/* Center the avatar image inside this container */
.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
}

/* Avatar image */
img.avatar {
    width: 40%;
    border-radius: 50%;
}

/* Add padding to containers */
.container {
    padding: 16px;
}

/* The "Forgot password" text */
span.psw {
    float: right;
    padding-top: 16px;
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
    span.psw {
        display: block;
        float: none;
    }

    .cancelbtn {
        width: 100%;
    }
}
</style>
