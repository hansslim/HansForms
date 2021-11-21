<template>
    <div >
        <ul>
            <li @click="updateChoice">{{this.getChoiceTextWithLabel()}}</li>
        </ul>
    </div>
</template>

<script>

import AddItemModal from "./AddItemModal";
import SelectChoiceModal from "./SelectChoiceModal";

export default {
    name: "SelectChoiceItem",
    props: ["obj"],
    data() {
        return {
            choiceText: "",
            hiddenLabel: "",
        }
    },
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
                { obj: this.$props['obj'], purpose: "update" },
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': () => {
                        this.$emit('choiceChanged')
                    }}
            )
        },
    },
    mounted() {
        this.choiceText = this.$props['obj'].text
        this.hiddenLabel = this.$props['obj'].hidden_label
    }
}
</script>

<style scoped>

</style>
