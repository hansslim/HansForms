<template>
    <div id="main">
        <header>
            <!-- <app-header /> -->
        </header>
        <main>
            <div>
<!--                <router-view />-->
                
            </div>
        </main>
    </div>
</template>

<script>
import Header from "./components/Header";
import Login from "./views/Login";

const App = {
    components: {
        'app-header': Header,
        'login': Login
    }
}
export default App;

</script>

<style scoped>

</style>
