<template>
    <div v-if="inputType==='text'">
        {{this.$props["obj"].input_elements.header}} <br> {{"text"}} <br>
    </div>
    <div v-else-if="inputType==='number'">
        {{this.$props["obj"].input_elements.header}} <br>{{"number"}} <br>
    </div>
    <div v-else>{{"new page?"}}</div>
</template>

<script>
import VueSwitch from 'v-switch-case';
import Vue from "vue";
Vue.use(VueSwitch);

export default {
    name: "FormElement",
    props: ['obj'],
    data() {
      return {
          inputType: ''
      }
    },
    mounted() {
        this.element = this.$props["obj"];
        this.inputType = this.getType(this.$props["obj"]);
        console.log(this.element);
    },
    methods: {
        getType(element) {
            /*console.log(element);*/
            if (element.new_pages === null) {
                if (element.input_elements !== null) {
                    if (element.input_elements.boolean_input !== null) return "boolean";
                    else if (element.input_elements.date_input !== null) return "date";
                    else if (element.input_elements.number_input !== null) return "number";
                    else if (element.input_elements.text_input !== null) return "text";
                    else throw new Error("Invalid type of input element");
                }
            }
            else return "new_page";
        }
    },
}
</script>

<style scoped>

</style>
