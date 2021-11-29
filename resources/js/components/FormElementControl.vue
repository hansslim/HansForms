<template>
    <div v-if="this.$props['obj']" @click="showUpdateModal">
        <p>{{this.$props['obj'].header}}</p>
        <div v-if="this.$props['obj'].type==='text'">
            <input type="text" disabled placeholder="Text input"/>
        </div>
        <div v-if="this.$props['obj'].type==='number'">
            <input type="number" disabled placeholder="Number input"/>
        </div>
        <div v-if="this.$props['obj'].type==='date'">
            <input type="text" disabled placeholder="Date input"/>
        </div>
        <div v-if="this.$props['obj'].type==='boolean'">
            <div v-if="this.$props['obj'].is_mandatory">
                <input type="radio" checked disabled>Yes
                <input type="radio" disabled>No
            </div>
            <div v-else>
                <input type="radio" checked disabled>Yes
                <input type="radio" disabled>No
                <input type="radio" disabled>I don't want to answer.
            </div>
        </div>
        <div v-if="this.$props['obj'].type==='select'">
            <div v-if="this.$props['obj'].has_hidden_label">
                <div v-if="this.$props['obj'].is_multiselect">
                    <div v-for="choice in this.$props['obj'].choices"><p><input type="checkbox" disabled> {{choice.text}} ({{choice.hidden_label}})</p></div>
                </div>
                <div v-else>
                    <div v-for="choice in this.$props['obj'].choices"><p><input type="radio" disabled> {{choice.text}} ({{choice.hidden_label}})</p></div>
                </div>
            </div>
            <div v-else>
                <div v-if="this.$props['obj'].is_multiselect">
                    <div v-for="choice in this.$props['obj'].choices"><p><input type="checkbox" disabled> {{choice.text}}</p></div>
                </div>
                <div v-else>
                    <div v-for="choice in this.$props['obj'].choices"><p><input type="radio" disabled> {{choice.text}}</p></div>
                </div>
            </div>
        </div>
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
                {height: 'auto', width: '60%', scrollable: true},
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
