<template>
    <div>
        <div v-if="this.loading">{{ "loading" }}</div>
        <div v-if="!this.loading">
            <h1>Results</h1>
            <h2 v-if="!this.loading && !this.errored">{{this.formResults.name}}</h2>
            <p v-if="!this.loading && !this.errored">{{this.formResults.description}}</p>
        </div>
        <div v-if="this.errored">{{ errorText }}</div>

        <div v-if="!this.loading">
            <FormulateInput
                class="btn"
                @click="handleGoBack"
                label="Go back"
                type="button"
            />
            <FormulateInput
                v-if="!this.loading && !this.errored"
                class="btn"
                @click="handleDownload"
                label="Download"
                type="button"
            />
            <FormulateInput
                v-if="!this.loading && !this.errored"
                class="btn"
                @click="handleChangeView"
                label="Change results view"
                type="button"
            />
        </div>
        <div v-if="!this.loading && !this.errored">
            <hr>
            <result-component
                v-if="!detailedSummaryView"
                v-for="item in this.formResults.form_elements"
                :key="item.order"
                :obj="item"
                :completionsIds="getCompletionsIds()"
            />
            <results-table
                v-if="detailedSummaryView"
                :obj="this.formResults.results_table"
            />
        </div>
    </div>
</template>

<script>
import Form from "../apis/Form";
import ResultComponent from "../components/ResultComponents/ResultComponent";
import DetailedResultsTable from "../components/ResultComponents/DetailedResultsTable";

export default {
    name: "FormResults",
    components: {'result-component': ResultComponent, 'results-table': DetailedResultsTable},
    data() {
        return {
            formResults: {},
            loading: true,
            errored: false,
            errorCode: -1,
            errorText: "Bad request.",
            detailedSummaryView: false
        }
    },
    async mounted() {
        Form.getFormResults(this.getSlug()).then((resFormRes) => {
            if (resFormRes.status === 200) {
                this.formResults = resFormRes.data;
                this.loading = false;
            }
            else {
                this.errorCode = resFormRes.status
                throw new Error(resFormRes.data);
            }
        }).catch(() => {
            this.errored = true;
            this.loading = false;
            switch (this.errorCode) {
                case 204:
                    this.errorText = "Form hasn't been answered yet.";
                    break;
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
        getCompletionsIds() {
            //console.log(Object.keys(this.formResults.results_table[2]))
            return Object.keys(this.formResults.results_table[2])
        },
        handleGoBack() {
            this.$router.go(-1);
        },
        async handleDownload() {
            await Form.getFormResultsDownload(this.getSlug()).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', this.getSlug() + '.xlsx');
                document.body.appendChild(link);
                link.click();
                link.remove();
            }).catch(console.error)
        },
        handleChangeView() {
            this.detailedSummaryView = !this.detailedSummaryView;
        }
    },

}
</script>

<style scoped>

</style>
