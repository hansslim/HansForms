<template>
    <div>
        <div v-if="!loading" class="container-fluid flex-wrap">
            <div v-if="errored" class="container"><h1>{{ this.errorText }}</h1></div>
            <FormulateForm v-else @submit="submitUpdateForm">
                <div style="padding-bottom: 7vh" class="container-fluid row m-auto">
                    <div class="col-md shadow-sm bg-white p-2 overflow-auto"
                         style="max-height: 70vh; overflow-x: hidden !important;"
                    >
                        <h1 class="mt-1">Update form...</h1>
                        <hr>
                        <div class="m-2 row">
                            <FormulateInput
                                class="col"
                                input-class="btn btn-primary w-100"
                                type="button"
                                @click="showAddItemModal"
                                label="Add new question..."
                            />
                            <FormulateInput
                                class="col"
                                input-class="btn btn-primary w-100"
                                type="button"
                                @click="addNewPage"
                                label="Add new page"
                            />
                        </div>
                        <FormulateInput
                            class="col m-0"
                            type="text"
                            v-model="form.header"
                            label="Form header"
                            validation="required|max:127,length"
                            input-class="form-control"
                        />
                        <FormulateInput
                            class="col m-0"
                            v-model="form.description"
                            label="Form description"
                            validation="max:511,length"
                            type="textarea"
                            input-class="form-control"
                        />
                        <div class="row mt-1">
                            <FormulateInput
                                class="col m-0"
                                v-model="form.start_time"
                                label="Form publication start time"
                                type="datetime-local"
                                :validation="[['required']]"
                                input-class="form-control"
                            />
                            <FormulateInput
                                class="col m-0"
                                v-model="form.end_time"
                                label="Form publication end time"
                                type="datetime-local"
                                :validation="[['required']]"
                                input-class="form-control"
                            />
                            <!--                            <FormulateInput
                                                            class="col m-0 align-self-center"
                                                            v-model="form.has_private_token"
                                                            label="Form with private access "
                                                            type="checkbox"
                                                        />-->
                        </div>
                        <!--                        <div v-if="form.has_private_token">
                                                    <hr>
                                                    <div class="h6">Form can be accessed and answered only via unique link with access
                                                        token that will be sent on every email from below.
                                                    </div>
                                                    <div class="d-inline-flex">
                                                        <div class="font-weight-bold pr-1">Invited emails</div>
                                                        <div class="font-weight-normal pr-1" v-if="this.privateEmailsTextareaInputMode">
                                                            (Raw text input mode)
                                                        </div>
                                                        <div class="font-weight-normal pr-1" v-else>
                                                            (Basic input mode)
                                                        </div>
                                                    </div>
                                                    <a class="d-block mb-2" href="#" @click="handleInputModeChange">Change mode</a>
                                                    &lt;!&ndash; only to add - existing cannot be removed&ndash;&gt;
                                                    <div v-if="!this.privateEmailsTextareaInputMode">
                                                        <div style="max-height: 100px; overflow-y:auto">
                                                            <FormulateInput
                                                                type="group"
                                                                name="emails"
                                                                :repeatable="true"
                                                                add-label="Add Email"
                                                                validation="required"
                                                                remove-position="after"
                                                                remove-label="Remove"
                                                                v-model="form.private_emails"
                                                                minimum="1"
                                                            >
                                                                <div class="d-inline-flex">
                                                                    <FormulateInput
                                                                        type="email"
                                                                        name="email"
                                                                        validation="required|email"
                                                                    />
                                                                </div>
                                                            </FormulateInput>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        (Web validation limited! Please, separate emails by enter. Invalid data will be
                                                        ignored.)
                                                        <FormulateInput
                                                            type="textarea"
                                                            name="emails"
                                                            validation="required"
                                                            v-model="form.private_emails"
                                                        />
                                                    </div>
                                                </div>-->
                    </div>
                    <div class="col-md mt-2 overflow-auto" style="max-height: 70vh">
                        <div
                            v-if="!this.form.header && !this.form.description && this.form.items.length === 0"
                            class="text-muted"
                        >
                            There will be the form shown.
                        </div>
                        <h1>{{ this.form.header }}</h1>
                        <h4>{{ this.form.description }}</h4>
                        <hr v-if="this.form.header || this.form.description">
                        <form-element
                            class="row justify-content-center"
                            v-for="item in this.form.items"
                            :key="item.id"
                            :obj="item"
                            @itemChanged="handleItemsChanged"
                            @removeNewPage="handleRemoveNewPage"
                        />
                    </div>
                </div>
                <div class="d-flex justify-content-center fixed-bottom bg-info" style="z-index: 0">
                    <div class="m-1">
                        <FormulateInput
                            type="submit"
                            label="Update this form"
                            outer-class="m-0"
                        />
                    </div>
                </div>
            </FormulateForm>
        </div>
        <div v-else>
            <loading/>
        </div>

    </div>
</template>

<script>
import ItemModal from "../components/Modals/CreateForm/ItemModal";
import FormElementControl from "../components/FormElementControl";
import Form from "../apis/Form";
import {createFormChoicesStore, createFormStore} from "../store";
import {v4 as uuidv4} from "uuid";

export default {
    name: "UpdateForm",
    components: {
        'form-element': FormElementControl,
    },
    data() {
        return {
            form: {
                header: "",
                description: "",
                start_time: "",
                end_time: "",
                items: []
            },
            privateEmailsTextareaInputMode: false,
            loading: true,
            errored: false,
            errorText: "Bad request"
        }
    },
    methods: {
        handleInputModeChange() {
            this.form.private_emails = [];
            this.privateEmailsTextareaInputMode = !this.privateEmailsTextareaInputMode;
        },
        addNewPage() {
            createFormStore.addItem({
                type: "new_page",
                id: uuidv4(),
                order: -1
            })
            this.handleItemsChanged()
        },
        handleRemoveNewPage(item) {
            createFormStore.deleteItem(item)
            this.handleItemsChanged()
        },
        showAddItemModal() {
            this.$modal.hideAll();
            createFormChoicesStore.clearStore();
            this.$modal.show(
                ItemModal,
                {purpose: "add"},
                {height: 'auto', width: '60%', scrollable: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged() {
            this.form.items = createFormStore.getItems()
        },
        async submitUpdateForm() {
            try {
                this.loading = true;

                //date type and interval validation
                const min = new Date(this.form.start_time);
                const max = new Date(this.form.end_time);
                console.log(min)

                if (!(min instanceof Date && !isNaN(min.valueOf()))) throw new Error("Invalid date data (min)");
                if (!(max instanceof Date && !isNaN(max.valueOf()))) throw new Error("Invalid date data (max)");

                if (min.getTime() && max.getTime()) {
                    if (parseInt(max.getTime()) <= (parseInt(min.getTime()))) throw new Error("Invalid date data");
                } else throw new Error("Invalid date data (max or min)");

                const formElementsError = this.validateFormElements();
                if (formElementsError.errored) {
                    throw new Error(formElementsError.message)
                }

                //question amount check
                if (this.form && this.form.items.length >= 1) {
                    //at least one question must be mandatory
                    let atLeastOneQuestionMandatory = false;
                    this.form.items.forEach((x)=>{
                        if (x.is_mandatory) {
                            atLeastOneQuestionMandatory = true;
                            return;
                        }
                    })
                    if (!atLeastOneQuestionMandatory) throw new Error('At least one question must be mandatory')
                    
                    await Form.putUpdateForm(this.form, this.$route.params['slug']).then((res) => {
                        if (res.status === 200) {
                            this.$toasted.success(`Form update was successful.`);
                            this.$router.push("/");
                            createFormStore.clearStore();
                            //this.choices = [];
                            this.loading = false;
                        } else throw new Error(res.data.toString())
                    })
                } else throw new Error("Form update error (no questions)");

            } catch (error) {
                this.$toasted.error(`Form update wasn't successful. (${error})`);
                this.loading = false;
            }

        },
        validateFormElements() {
            let response = {
                errored: false,
                message: null
            }

            const elements = this.form.items.map((x) => {
                if (x.type === 'new_page') return 0;
                else return 1;
            })

            if (elements[0] === 0) {
                response.errored = true;
                response.message = "New page element is not allowed on the start of the form"
                return response;
            }
            if (elements[elements.length - 1] === 0) {
                response.errored = true;
                response.message = "New page element is not allowed on the end of the form"
                return response;
            }

            let newPageBefore = false;
            elements.forEach((x) => {
                if (x === 0) {
                    if (newPageBefore) {
                        response.errored = true;
                        response.message = "New page elements are next to each other somewhere in the form"
                        return response;
                    } else newPageBefore = true;
                } else if (x === 1) {
                    newPageBefore = false;
                }
            })

            return response;
        },
        async getRequestedForm() {
            let errorCode = -1;
            await Form.getSpecificFormWithAuth(this.$route.params['slug']).then((res) => {
                if (res.status === 200) {
                    if (res.data) {
                        if (res.data.was_already_published) {
                            errorCode = -2;
                            throw new Error();
                        } else {
                            const data = res.data;
                            this.form.header = data.name
                            this.form.description = data.description
                            this.form.start_time = data.start_time.replace(" ", "T")
                            this.form.end_time = data.end_time.replace(" ", "T")

                            data.form_elements.forEach((item) => {
                                let element = {
                                    type: "",
                                    id: -1,
                                    order: 0,
                                }

                                element.order = item.order;

                                if (item.input_element) {
                                    element.is_mandatory = item.input_element.is_mandatory;
                                    element.header = item.input_element.header;
                                    if (item.input_element.boolean_input) {
                                        element = {...element, ...item.input_element.boolean_input}
                                        element.type = "boolean";
                                    } else if (item.input_element.number_input) {
                                        element = {...element, ...item.input_element.number_input}
                                        element.type = "number";
                                    } else if (item.input_element.text_input) {
                                        element = {...element, ...item.input_element.text_input}
                                        element.type = "text";
                                    } else if (item.input_element.date_input) {
                                        element = {...element, ...item.input_element.date_input}
                                        element.type = "date";
                                    } else if (item.input_element.select_input) {
                                        let choices = item.input_element.select_input.select_input_choices;
                                        if (item.input_element.select_input.has_hidden_label) {
                                            choices = choices.map((x)=>{
                                                x.hidden_label = `${x.hidden_label}`;
                                                return x;
                                            })
                                        }

                                        element = {
                                            ...element, ...item.input_element.select_input,
                                            choices: choices
                                        }
                                        element.type = "select";
                                    }

                                } else if (item.new_page) {
                                    element.type = "new_page";
                                    element.id = item.new_page.id
                                }

                                Object.keys(element).forEach((k) => element[k] == null && delete element[k]);
                                
                                //to handle same ids from DB (taking ids from specific input types that can be same)
                                element.id = uuidv4();

                                createFormStore.addItem(element);
                            })

                            this.form.items = createFormStore.getItems();
                        }
                    }
                } else {
                    errorCode = res.status
                    throw new Error()
                }
            }).catch((error) => {
                this.errored = true;
                console.log(error)
                switch (errorCode) {
                    case -2:
                        this.errorText = "Form has been published. You cannot change this form.";
                        break;
                    case 400:
                        this.errorText = "Bad request. Check your link to the form.";
                        break;
                    case 404:
                        this.errorText = "Requested form was not found.";
                        break;
                    case 401:
                        this.errorText = "Unauthorized.";
                        break;
                    default:
                        this.errorText = `Unhandled error - ${error}`;
                        break; //dev only
                }
            }).finally(() => {
                this.loading = false;
            })
        }
    },
    async mounted() {
        this.loading = true;
        createFormStore.clearStore();
        await this.getRequestedForm();
    }
}


</script>

<style scoped>

</style>
