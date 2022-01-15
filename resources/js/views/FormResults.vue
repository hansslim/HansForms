<template>
    <div>
        <div v-if="this.loading"><loading/></div>
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
                v-if="!this.loading && !this.errored && this.$store.getters['authenticated'] && !this.arePublicResults"
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
            <FormulateInput
                v-if="!this.loading && !this.errored && this.$store.getters['authenticated'] && !this.arePublicResults"
                class="btn"
                @click="handlePublication"
                label="Publication..."
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
import FormResultsPublicationModal from "../components/Modals/FormResults/FormResultsPublicationModal";
import ItemModal from "../components/Modals/CreateForm/ItemModal";

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
            detailedSummaryView: false,
            arePublicResults: true,
        }
    },
    async mounted() {
        if (this.$route.path.includes('/results/')) {
            this.arePublicResults = false
        }
        if (this.arePublicResults) {
            Form.getPublicFormResults(this.getSlug()).then((resFormRes) => {
                console.log(resFormRes)
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
                        this.errorText = "No content.";
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
        }
        else if (!this.arePublicResults && this.$store.getters['authenticated']) {
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
        }
        else {
            this.errored = true;
            this.loading = false;
            this.errorText = "Bad request."
        }
    },
    methods: {
        getSlug() {
            return this.$route.params['slug'] ?? '';
        },
        getCompletionsIds() {
            return Object.keys(this.formResults.results_table[2])
        },
        handleGoBack() {
            this.$router.go(-1);
        },
        async handleDownload() {
            this.loading = true;
            await Form.getFormResultsDownload(this.getSlug()).then(response => {
                if (response.status === 200) {
                    this.$toasted.success("File started downloading successfully.")
                    const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', this.getSlug() + '.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                }
                else throw new Error("Can't download file.")
            }).catch(()=>{
                this.$toasted.error("Can't download file. Try it later.")
            })
            this.loading = false;
        },
        handleChangeView() {
            this.detailedSummaryView = !this.detailedSummaryView;
        },
        handlePublication() {
            this.$modal.show(
                FormResultsPublicationModal,
                {
                    questions: this.formResults.form_elements.map((x)=>{
                        return {
                            id: `${x.input_element.id}`,
                            header: x.input_element.header,
                            public: x.input_element.has_public_results
                        }}),
                    publicResults: this.formResults.has_public_results,
                    slug: this.getSlug()
                },
                {height: 'auto', width: '60%', scrollable: true},
                {/*'before-close': event => this.handleItemsChanged()*/}
            )
        },
    },

}
</script>

<style scoped>

</style>
