<template>
    <div>
        <div v-if="!loading" class="container">
            <FormulateForm @submit="submitCreateForm">
                <div style="padding-bottom: 5vh">
                    <div class=" d-flex justify-content-center align-items-center flex-wrap">
                        <FormulateInput
                            class="m-2"
                            type="text"
                            v-model="form.header"
                            label="Form header"
                            :validation="[['required']]"
                            input-class="form-control"
                        />
                        <FormulateInput
                            class="m-2"
                            v-model="form.description"
                            label="Form description"
                            type="text"
                            input-class="form-control"
                        />
                        <FormulateInput
                            class="m-2"
                            v-model="form.start_time"
                            label="Form publication start time"
                            type="datetime-local"
                            :validation="[['required']]"
                            input-class="form-control"
                        />
                        <FormulateInput
                            class="m-2"
                            v-model="form.end_time"
                            label="Form publication end time"
                            type="datetime-local"
                            :validation="[['required']]"
                            input-class="form-control"
                        />
                        <FormulateInput
                            class="m-2"
                            v-model="form.has_private_token"
                            label="Form with private access "
                            type="checkbox"
                        />
                    </div>

                    <div v-if="form.has_private_token">
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
                            (Web validation limited! Please, separate emails by enter. Invalid data will be ignored.)
                            <FormulateInput
                                type="textarea"
                                name="emails"
                                validation="required"
                                v-model="form.private_emails"
                            />
                        </div>
                    </div>
                    <hr>
                    <form-element v-for="item in this.form.items" :key="item.id" :obj="item"
                                  @itemChanged="handleItemsChanged"/>
                </div>
                <div class="d-flex justify-content-center fixed-bottom bg-info" style="z-index: 0">
                    <div class="m-2">
                        <FormulateInput
                            type="button"
                            @click="showAddItemModal"
                            label="Add new question..."
                        />
                    </div>
                    <div class="m-2">
                        <FormulateInput
                            type="submit"
                            label="Create this form"
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

export default {
    name: "CreateForm",
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
                has_private_token: false,
                private_emails: [],
                items: []
            },
            loading: true,
            privateEmailsTextareaInputMode: false
        }
    },
    methods: {
        handleInputModeChange() {
            this.form.private_emails = [];
            this.privateEmailsTextareaInputMode = !this.privateEmailsTextareaInputMode;
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
        async submitCreateForm() {
            try {
                this.loading = true;

                //date type and interval validation
                const min = new Date(this.form.start_time);
                const max = new Date(this.form.end_time);

                if (!(min instanceof Date && !isNaN(min.valueOf()))) throw new Error("Invalid date data (min)");
                if (!(max instanceof Date && !isNaN(max.valueOf()))) throw new Error("Invalid date data (max)");

                if (min.getTime() && max.getTime()) {
                    if (parseInt(max.getTime()) <= (parseInt(min.getTime()))) throw new Error("Invalid date data");
                } else throw new Error("Invalid date data (max or min)");

                //question amount check
                if (this.form && this.form.items.length >= 1) {
                    await Form.postCreateForm(this.form).then((res) => {
                        if (res.status === 200) {
                            this.$toasted.success(`Form creation was successful.`);
                            this.$router.push("/");
                            createFormStore.clearStore();
                            //this.choices = [];
                            this.loading = false;
                        } else throw new Error(res.data.toString())
                    })
                } else throw new Error("Form creation error (no questions)");

            } catch (error) {
                this.$toasted.error(`Form creation wasn't successful. (${error})`);
                this.loading = false;
            }

        }
    },
    mounted() {
        this.loading = false;
    }
}


</script>

<style scoped>

</style>
