<template>
    <div id="app">
        <app-header></app-header>
        <router-view/>
    </div>
</template>

<script>
import Header from "./components/Header";

const App = {
    components: {
        'app-header': Header
    }
}
export default App;
</script>

<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
}

#nav {
    padding: 30px;
}

#nav a {
    font-weight: bold;
    color: #2c3e50;
}

#nav a.router-link-exact-active {
    color: #42b983;
}
</style>
