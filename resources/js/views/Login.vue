<template>
<div>
    <form method="post" action="#">
        <label for="email">Email:</label>
        <input type="email" id="email" />
        <label for="password">Password:</label>
        <input type="password" id="password" />
        <button type="submit">Submit</button>
    </form>
</div>
</template>

<script>
export default {
    name: "Login"
}
</script>

<style scoped>

</style>
