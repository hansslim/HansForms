[
<template>
    <div class="container p-2">
        <h1>Accessibility</h1>
        <div v-if="!this.loading">
            <h3>
                Current state:
                {{
                this.$props["has_private_token"] === true
                ? "Private"
                : "Public"
                }}
            </h3>
            <div>
                <FormulateForm
                    v-model="formValues"
                    @submit="handleSubmit"
                    name="modalForm"
                >
                    <div
                        v-if="
                            this.$props['has_private_token'] &&
                            formValues.has_private_token
                        "
                    >
                        <div v-if="this.$props['emails'].length > 0">
                            <hr/>
                            <h4>Invited emails</h4>
                            <p class="m-0">Mark to invalidate</p>
                            <FormulateInput
                                type="group"
                                name="emailsToInvalidate"
                                style="max-height: 40vh"
                                class="overflow-auto"
                            >
                                <FormulateInput
                                    v-for="item in this.$props['emails']"
                                    :key="item.id"
                                    class="form-check form-switch"
                                    :label="item.email"
                                    :name="`${item.id}`"
                                    type="checkbox"
                                    value="false"
                                    element-class="form-check-input"
                                    label-class="form-check-label"
                                />
                            </FormulateInput>
                        </div>
                    </div>
                    <hr/>
                    <FormulateInput
                        class="form-check form-switch"
                        label="Private form (access only with invitation on email)"
                        name="has_private_token"
                        type="checkbox"
                        :checked="this.$props['has_private_token']"
                        :value="this.$props['has_private_token']"
                        element-class="form-check-input"
                        label-class="form-check-label"
                    />
                    <div
                        v-if="
                            this.$props['has_private_token'] &&
                            !formValues.has_private_token
                        "
                        class="font-italic"
                    >
                        All tokens will be invalidated and removed! The form
                        will become public.
                    </div>
                    <div
                        v-if="
                            !this.$props['has_private_token'] &&
                            formValues.has_private_token
                        "
                        class="font-italic"
                    >
                        After this, the form will be accessible only through
                        invitation! Write down invited emails below.
                    </div>
                    <div v-if="formValues.has_private_token">
                        <h3 class="mt-2">Invite emails...</h3>
                        <FormulateInput
                            type="textarea"
                            name="newInvitedEmails"
                            placeholder="Insert emails..."
                        />
                    </div>
                    <FormulateErrors/>
                    <FormulateInput
                        label="Change accessibility"
                        type="submit"
                    />
                </FormulateForm>
            </div>
        </div>
        <div v-else>
            <loading/>
        </div>
    </div>
</template>

<script>
import Form from "../../../apis/Form";
import Loading from "../../Loading.vue";

export default {
    components: {Loading},
    name: "FormAccessibilityModal",
    props: ["has_private_token", "emails", "slug"],
    data() {
        return {
            loading: true,
            newHasPrivateToken: false,
            formValues: {newInvitedEmails: "", emailsToInvalidate: ""},
        };
    },
    mounted() {
        this.loading = false;
    },
    methods: {
        async handleSubmit() {
            this.loading = true;
            this.trivialFormulateErrorHandler();
            const formValuesBackup = {...this.formValues};
            let emailValidation = false;
            let request = {...this.formValues};
            if (this.formValues.has_private_token) {
                const [emailsToInvalidateDestructed] = [
                    ...this.formValues.emailsToInvalidate,
                ];
                request = {
                    ...this.formValues,
                    emailsToInvalidate: emailsToInvalidateDestructed,
                };
                emailValidation = this.validateNewEmails(
                    request.newInvitedEmails
                );
            }
            console.log(this.formValues);

            if (!emailValidation) {
                await Form.putUpdateAccessForm(request, this.$props["slug"])
                    .then((res) => {
                        if (res.status === 200) {
                            this.$toasted.success(
                                `Changing accessibility was successful.`
                            );
                            this.loading = false;
                            setTimeout(() => {
                                //this.$modal.hide(this.$parent.name)
                                //window.location.reload()
                                this.loading = false;
                            }, 1000);
                        } else {
                            throw new Error(res.statusText);
                        }
                    })
                    .catch((error) => {
                        this.$toasted.error(
                            `Changing accessibility failed (${error})`
                        );
                        this.loading = false;
                        //to recover input data in modal on error
                        this.formValues = formValuesBackup;
                    });
            } else {
                this.trivialFormulateErrorHandler(emailValidation);
                this.loading = false;
            }
        },
        validateNewEmails(emails) {
            if (emails.length === 0) return false;
            let hasInvalidEmail = false;
            const emailRegex = new RegExp("(.+)@(.+)\\.(.+)", "i");
            emails.split("\n").forEach((x) => {
                if (!emailRegex.test(x)) {
                    hasInvalidEmail = true;
                }
            });
            if (hasInvalidEmail) return "Invalid email values";
            else return false;
        },
        trivialFormulateErrorHandler(error = null) {
            if (error) {
                this.$formulate.handle(
                    {
                        formErrors: [error.toString()],
                    },
                    "modalForm"
                );
            } else {
                this.$formulate.handle(
                    {
                        formErrors: [],
                    },
                    "modalForm"
                );
            }
        },
    },
};
</script>

<style scoped></style>
]
