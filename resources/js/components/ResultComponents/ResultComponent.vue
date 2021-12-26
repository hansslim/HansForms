<template>
    <div>
        <h4>{{ this.header }}</h4>
        <result-render :viewType="this.viewType" :parsedData="this.parsedData"/>
        <hr>
    </div>
</template>

<script>

import ResultRender from "./ResultRender";
import randomColor from "randomcolor";

export default {
    name: "ResultComponent",
    components: {
        'result-render': ResultRender
    },
    props: ["obj", "completionsIds"],
    data() {
        return {
            elementType: null,
            elementAnswers: null,
            parsedData: {
                /*labels: [],
                datasets: [{
                    label: "",
                    data: [],
                    backgroundColor: [],
                    borderColor: [],
                    borderWidth: 1
                }]*/
            },
            header: null,
            isMandatory: false,
            viewType: 'list' //list, pie, bar
        }
    },
    mounted() {
        const element = this.$props['obj'];
        if (element.new_page === null) {
            if (element.input_element !== null) {
                //find type and save raw data
                if (element.input_element.header) this.header = element.input_element.header;
                if (element.input_element.is_mandatory) this.isMandatory = element.input_element.is_mandatory;
                if (element.input_element.boolean_input !== null) {
                    this.elementType = "boolean";
                    this.elementAnswers = element.input_element.boolean_input.boolean_input_answers;
                    this.viewType = 'pie';
                } else if (element.input_element.date_input !== null) {
                    this.elementType = "date";
                    this.elementAnswers = element.input_element.date_input.date_input_answers;
                    this.viewType = 'list';
                } else if (element.input_element.number_input !== null) {
                    this.elementType = "number";
                    this.elementAnswers = element.input_element.number_input.number_input_answers;
                    this.viewType = 'list';
                } else if (element.input_element.text_input !== null) {
                    this.elementType = "text";
                    this.elementAnswers = element.input_element.text_input.text_input_answers;
                    this.viewType = 'list';
                } else if (element.input_element.select_input !== null) {
                    this.elementType = "select";
                    this.elementAnswers = element.input_element.select_input.select_input_choices;
                    this.viewType = 'bar';
                } else throw new Error("Invalid type of input element");

                if (this.viewType === 'list') {
                    this.parsedData = this.elementAnswers.map((x) => {
                        return {'id': x.form_completion_id, 'value': x.value}
                    });
                } else if (this.viewType === 'pie' || this.viewType === 'bar') {
                    let set = {
                        labels: [],
                        datasets: [{
                            data: [],
                            label: "",
                            backgroundColor: [],
                            borderColor: [],
                            borderWidth: 1
                        }]
                    };

                    let pairs = {}
                    if (this.elementType === 'boolean') {
                        this.elementAnswers.forEach((x) => {
                            if (pairs[x.value]) pairs[x.value]++;
                            else pairs[x.value] = 1;
                        })
                        for (let key in pairs) {
                            set.labels.push(key);
                            set.datasets[0].data.push(pairs[key]);

                            if (key === 'true') set.datasets[0].backgroundColor.push('limegreen');
                            else if (key === 'false') set.datasets[0].backgroundColor.push('red');
                            else set.datasets[0].backgroundColor.push('deepskyblue');
                        }
                    } else if (this.elementType === 'select') {
                        let completions = this.$props['completionsIds'];
                        this.elementAnswers.forEach((x) => {
                            pairs[x.text] = x.select_input_answers.length;
                            x.select_input_answers.forEach((y)=>{
                                completions = completions.filter((z) => {
                                    return parseInt(z) !== y.form_completion_id;
                                })
                            })
                        })

                        for (let key in pairs) {
                            set.labels.push(key);
                            set.datasets[0].data.push(pairs[key]);
                        }

                        if (!element.input_element.is_mandatory) {
                            set.labels.push('null');
                            set.datasets[0].data.push(completions.length);
                        }

                        set.datasets[0].backgroundColor = randomColor({
                            luminosity: 'light',
                            hue: 'random',
                            alpha: 1,
                            count: set.labels.length,
                        });
                    }
                    this.parsedData = set;
                }
            }
        } else this.elementType = "new_page";


    },
    methods: {
        handleChangeRender() {

        },
        parseDataForChart() {

        },
        parseDataForList() {

        }
    }
}
</script>

<style scoped>

</style>
