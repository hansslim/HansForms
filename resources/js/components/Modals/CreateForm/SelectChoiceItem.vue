<template>
    <div >
        <ul>
            <li @click="updateChoice">{{this.getChoiceTextWithLabel()}}</li>
        </ul>
    </div>
</template>

<script>
import SelectChoiceModal from "./SelectChoiceModal";

export default {
    name: "SelectChoiceItem",
    props: ["obj"],
    methods: {
        getChoiceTextWithLabel() {
            if (this.$props['obj'].hidden_label) {
                return `${this.$props['obj'].hidden_label} | ${this.$props['obj'].text}`;
            }
            else {
                return `${this.$props['obj'].text}`
            }
        },
        updateChoice() {
            this.$modal.show(
                SelectChoiceModal,
                { obj: this.$props['obj'], purpose: "update", hasHiddenLabel: this.$props['obj'].hidden_label },
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': () => {
                        this.$emit('choiceChanged')
                    }}
            )
        },
    },
}
</script>

<style scoped>

</style>
