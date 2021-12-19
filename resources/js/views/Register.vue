<template>
    <div class="container">
        <form @submit.prevent="register">
            <h1>Sign Up</h1>
            <hr>

            <label for="username"><b>Username</b></label>
            <input v-model="user.name" type="text" placeholder="Enter Username" name="email" id="username" required>

            <label for="email"><b>Email</b></label>
            <input v-model="user.email" type="text" placeholder="Enter Email" name="email" id="email" required>

            <label for="password"><b>Password</b></label>
            <input v-model="user.password" type="password" placeholder="Enter Password" name="password" id="password"
                   required>

            <label for="password-repeat"><b>Repeat Password</b></label>
            <input v-model="user.password_confirmation" type="password" placeholder="Repeat Password"
                   name="password-repeat" id="password-repeat" required>


            <button type="submit" class="signupbtn">Sign Up</button>
        </form>
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
            error: {},
            loading: false
        };
    },
    methods: {
        async register() {
            try {
                const response = await User.register(this.user);
                if (response.status === 200) {
                    this.$router.push('/login');
                }
                else throw new Error();
            } catch (e) {
                console.log(e);
                alert("Invalid data provided")
            }
        }
    }
}
</script>

<style scoped>
* {
    box-sizing: border-box
}

/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 15px;
    margin: 5px 0 22px 0;
    display: inline-block;

}

input[type=text]:focus, input[type=password]:focus {

    outline: none;
}

hr {
    border: 1px solid #f1f1f1;
    margin-bottom: 25px;
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
    opacity: 0.9;
}

button:hover {
    opacity: 1;
}

/* Extra styles for the cancel button */
.cancelbtn {
    padding: 14px 20px;
    background-color: #f44336;
}

/* Float cancel and signup buttons and add an equal width */
.cancelbtn, .signupbtn {
    float: left;
    width: 50%;
}

/* Add padding to container elements */
.container {
    padding: 16px;
}

/* Clear floats */
.clearfix::after {
    content: "";
    clear: both;
    display: flex;
}

/* Change styles for cancel button and signup button on extra small screens */
@media screen and (max-width: 300px) {
    .cancelbtn, .signupbtn {
        width: 100%;
    }
}
</style>
