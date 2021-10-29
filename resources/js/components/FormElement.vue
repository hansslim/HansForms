<template>
    <div v-if="inputType==='text'">
        {{ "text" }}
        <FormulateInput
            :name="id"
            type="text"
            :label="header"
            validation=""
        />
    </div>
    <div v-else-if="inputType==='number'">
        {{ "number" }}
        <FormulateInput
            :name="id"
            type="number"
            :label="header"
            validation=""
        />
    </div>
    <div v-else>
        <hr>
        {{ "new page" }}
        <hr>
    </div>


</template>

<script>
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
    computed: {
        header() {
            return this.$props["obj"].input_elements.header;
        },
        id() {
            const fullId = (id) => {
                return `${this.inputType}-${id}`;
            }
            switch (this.inputType) {
                case 'boolean': {
                    return fullId(this.$props["obj"].input_elements.boolean_input.id);
                }
                case 'date': {
                    return fullId(this.$props["obj"].input_elements.date_input.id);
                }
                case 'number': {
                    return fullId(this.$props["obj"].input_elements.number_input.id);
                }
                case 'text': {
                    return fullId(this.$props["obj"].input_elements.text_input.id);
                }
                case 'new_page': {
                    return fullId(this.$props["obj"].new_pages.id);
                }
                default: {
                    throw new Error("Invalid type of input element")
                }
            }
        }
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
            } else return "new_page";
        }
    },
}
</script>

<style scoped>

</style>
