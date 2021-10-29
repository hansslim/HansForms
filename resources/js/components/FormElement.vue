<template>
    <div>
        <hr>
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
        <div v-else-if="inputType==='boolean'">
            {{ "boolean" }}
            <FormulateInput
                :name="id"
                type="radio"
                :label="header"
                validation=""
                :options="{true: 'Yes', false: 'No'}"
            />
        </div>
        <div v-else-if="inputType==='date'">
            {{ "date" }}
            <FormulateInput
                :name="id"
                type="date"
                :label="header"
                validation=""
            />
        </div>
        <div v-else-if="inputType==='select'">
            {{ "select" }}
            <FormulateInput
                :name="id"
                type="radio"
                :label="header"
                validation=""
                :options="selectChoices"
            />
        </div>
        <div v-else-if="inputType==='new_page'" style="font-weight: bold">
                {{ "new page" }}
        </div>
        <div v-else>

            {{ "unknown element" }}

        </div>
        <hr>
    </div>

</template>

<script>
export default {
    name: "FormElement",
    props: ['obj'],
    data() {
        return {
            inputType: '',
            element: {}
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
                case 'select': {
                    return fullId(this.$props["obj"].input_elements.select_input.id);
                }
                case 'new_page': {
                    return fullId(this.$props["obj"].new_pages.id);
                }
                default: {
                    throw new Error("Invalid type of input element")
                }
            }
        },
        selectChoices() {
            if (this.inputType === 'select') {

                const options = this.$props["obj"].input_elements.select_input.select_input_choices.sort(( a, b ) => {
                    if ( a.order < b.order ){
                        return -1;
                    }
                    if ( a.order > b.order ){
                        return 1;
                    }
                    return 0;
                });
                let selectChoices = [];
                options.forEach((option) => {
                    selectChoices.push({value: `choice-${option.id}`, label: `${option.text}`})
                })
                return selectChoices;
            }
            else return null
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
                    else if (element.input_elements.select_input !== null) return "select";
                    else throw new Error("Invalid type of input element");
                }
            } else return "new_page";
        }
    },
}
</script>

<style scoped>

</style>
