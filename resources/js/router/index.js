import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home.vue";
import About from "../views/About.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";

import store from "../store";
import Profile from "../views/Profile";
import Form from "../views/Form";
import NotFound from "../views/NotFound";
import CreateForm from "../views/CreateForm";
import FormPreview from "../views/FormPreview";
import FormResults from "../views/FormResults";
import UpdateForm from "../views/UpdateForm";
import ChangePassword from "../views/ChangePassword";

Vue.use(VueRouter);

const routes = [
    {
        path: "/",
        name: "Home",
        component: Home
    },
    {
        path: "/about",
        name: "About",
        component: About
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: {requiresGuest: true}
    },
    {
        path: "/register",
        name: "Register",
        component: Register,
        meta: {requiresGuest: true}
    },
    {
        path: "/profile",
        name: "Profile",
        component: Profile,
        meta: {requiresAuth: true}
    },
    {
        path: "/change_password",
        name: "ChangePassword",
        component: ChangePassword,
        meta: {requiresAuth: true}
    },
    {
        path: "/form/:slug",
        name: "Form",
        component: Form,
    },
    {
        path: "/private_form/:token",
        name: "PrivateForm",
        component: Form,
    },
    {
        path: "/form/preview/:slug",
        name: "FormPreview",
        component: FormPreview,
        meta: {requiresAuth: true}
    },
    {
        path: "/form/results/:slug",
        name: "FormResults",
        component: FormResults,
        meta: {requiresAuth: true}
    },
    {
        path: "/form/public_results/:slug",
        name: "FormPublicResults",
        component: FormResults,
    },
    {
        path: "/create_form",
        name: "CreateForm",
        component: CreateForm,
        meta: {requiresAuth: true}
    },
    {
        path: "/update_form/:slug",
        name: "UpdateForm",
        component: UpdateForm,
        meta: {requiresAuth: true}
    },
    {
        path: "*",
        name: "NotFound",
        component: NotFound,
    }
];

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresGuest)) {
        if (store.getters['authenticated']) {
            next('/');
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters['authenticated']) {
            next();
        } else next('/login');
    } else {
        next();
    }
})

export default router;
