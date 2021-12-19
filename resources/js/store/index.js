import Vue from "vue";
import Vuex from "vuex";
import VuexPersistence from 'vuex-persist';
import MyStore from './MyStoreClass'

//store for auth
Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        authenticated: false,
        user: null
    },
    getters: {
        authenticated(state) {
            return state.authenticated;
        },
        user(state) {
            return state.user;
        }
    },
    mutations: {
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value;
        },
        SET_USER(state, value) {
            state.user = value;
        }
    },
    actions: {
        login({commit}, user) {
            commit('SET_AUTHENTICATED', true);
            commit('SET_USER', user);
        },
        logout({commit}) {
            commit('SET_AUTHENTICATED', false);
            commit('SET_USER', null);
        },
        updateUser({commit}, user) {
            commit('SET_USER', user);
        }
    },
    plugins: [new VuexPersistence().plugin]
});
export default store;

//store for create form page
export const createFormStore = new MyStore();
export const createFormChoicesStore = new MyStore();

//store for duplicate form modal
export const duplicateFormStore = {
    name: "",
    description: "",
    start_time: "",
    end_time: "",

    setData(obj) {
        if (obj) {
            this.name = obj.name
            this.description = obj.description
            this.start_time = obj.start_time
            this.end_time = obj.end_time

        }
    },
    getData() {
        return {
            name: this.name,
            description: this.description,
            start_time: this.start_time,
            end_time: this.end_time,

        }
    },
    validateData() {
        if (!this.name) return "Missing form name";
        const min = new Date(this.start_time);
        const max = new Date(this.end_time);

        if (!(min instanceof Date && !isNaN(min.valueOf()))) return "Invalid minimal date";
        if (!(max instanceof Date && !isNaN(max.valueOf()))) return "Invalid maximal date";

        if (min.getTime() && max.getTime()) {
            if (parseInt(max.getTime()) <= (parseInt(min.getTime()))) return "Maximal date is lower than minimal date";
        }
        else return "Invalid date values";
        return false;
    },
    clearData() {
        this.name= ""
        this.description= ""
        this.start_time= ""
        this.end_time= ""
    },
    isStoreEmpty() {
        if (this.name === "" &&
            this.description === "" &&
            this.start_time === "" &&
            this.end_time === ""
        ) return true;
        else return false;
    }
}
