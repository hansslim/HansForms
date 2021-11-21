<template>
    <div>
        <FormulateInput type="group" :name="this.$props['obj'].id">
            <div class="d-flex">
                <FormulateInput
                    name="text"
                    type="text"
                    placeholder="Write a choice."
                    v-model="choiceText"
                    @input="handleItemChanged"
                />
                <FormulateInput
                    name="hidden_label"
                    type="number"
                    placeholder="Write a hidden label."
                    v-model="hiddenLabel"
                    @input="handleItemChanged"
                />
                <FormulateInput type="button" @click="deleteThis" label="Delete"/>
            </div>
        </FormulateInput>
    </div>
</template>

<script>
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
        deleteThis() {
            this.$emit('delete', this.$props['obj']);
        },
        handleItemChanged() {
            this.$emit('itemChange', {
                text: this.choiceText,
                hidden_label: this.hiddenLabel,
                id: this.$props['obj'].id
            });
        }
    },
    mounted() {
        this.choiceText = this.$props['obj'].text
        this.hiddenLabel = this.$props['obj'].hidden_label
    }
}
</script>

<style scoped>

</style>
