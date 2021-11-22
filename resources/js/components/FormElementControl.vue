<template>
    <div @click="showUpdateModal">
        <hr>
        {{this.$props['obj']}}
        <hr>
    </div>
</template>

<script>
import ItemModal from "./Modals/CreateForm/ItemModal";

export default {
    name: "FormElementControl",
    props: ['obj'],
    methods: {
        showUpdateModal() {
            this.$modal.show(
                ItemModal,
                { obj: this.$props['obj'], purpose: "update" },
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': () => {
                        this.$emit('itemChanged')
                }}
            )
        },

    }
}
</script>

<style scoped>

</style>
