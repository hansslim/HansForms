<template>
    <div @click="showUpdateModal">
        <hr>
        {{this.$props['obj']}}
        <hr>
    </div>
</template>

<script>
import AddItemModal from "./Modals/CreateForm/AddItemModal";

export default {
    name: "FormElementControl",
    props: ['obj'],
    methods: {
        showUpdateModal() {
            this.$modal.show(
                AddItemModal,
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
