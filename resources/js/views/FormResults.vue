<template>
    <div>
        <div v-if="!this.loading && !this.errored">
<!--            <result-component v-for="item in this.formResults.form_elements" :key="item.order" :obj="item"></result-component>-->
             {{ formResults }}
        </div>
        <div v-if="this.loading">{{ "loading" }}</div>
        <div v-if="this.errored">{{ errorText }}</div>
    </div>
</template>

<script>
import Form from "../apis/Form";
import ResultComponent from "../components/ResultComponents/ResultComponent";

export default {
    name: "FormResults",
    components: {'result-component': ResultComponent},
    data() {
        return {
            formResults: {},
            loading: true,
            errored: false,
            errorCode: -1,
            errorText: "Bad request."
        }
    },
    async mounted() {
        Form.getFormResults(this.getSlug()).then((resFormRes) => {
            console.log(resFormRes.status)
            if (resFormRes.status === 200) {
                this.formResults = resFormRes.data;
                this.loading = false;
            } else {
                this.errorCode = resFormRes.status
                throw new Error(resFormRes.data);
            }
        }).catch(() => {
            this.errored = true;
            this.loading = false;
            switch (this.errorCode) {
                case 401:
                    this.errorText = "Unauthorized.";
                    break;
                case 404:
                    this.errorText = "Not found.";
                    break;
                default:
                    this.errorText = "Unhandled error.";
                    break;
            }
        })
    },
    methods: {
        getSlug() {
            return this.$route.params['slug'] ?? '';
        },
    }
}
</script>

<style scoped>

</style>
