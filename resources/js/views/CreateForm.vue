<template>
    <div>
        <div v-if="!loading">
            <FormulateForm @submit="submitCreateForm">
                <div style="padding-bottom: 5vh">
                    <FormulateInput
                        type="text"
                        v-model="form.header"
                        label="Form header"
                        :validation="[['required']]"
                    />
                    <FormulateInput
                        v-model="form.description"
                        label="Form description"
                        type="textarea"
                    />
                    <hr>
                    <FormulateInput
                        v-model="form.start_time"
                        label="Form publication start time"
                        type="datetime-local"
                        :validation="[['required']]"
                    />
                    <FormulateInput
                        v-model="form.end_time"
                        label="Form publication end time"
                        type="datetime-local"
                        :validation="[['required']]"
                    />
                    <hr>
                    <FormulateInput
                        v-model="form.has_private_token"
                        label="Form with private access (WIP)"
                        type="checkbox"
                    />
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
            <p>loading</p>
        </div>

    </div>
</template>

<script>
import ItemModal from "../components/Modals/CreateForm/ItemModal";
import FormElementControl from "../components/FormElementControl";
import {createFormChoicesStore, createFormStore} from "../components/Modals/CreateForm/stores";
import Form from "../apis/Form";

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
                items: []
            },
            loading: true

        }
    },
    methods: {
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
                }
                else throw new Error("Invalid date data (max or min)");

                //question amount check
                if (this.form && this.form.items.length >= 1) {
                    await Form.postCreateForm(this.form).then(() => {
                        alert("Form creation was successful.")
                        this.$router.push("/");
                        createFormStore.clearStore();
                        this.choices = [];
                        this.loading = false;
                    })
                } else throw new Error("Form creation error");

            } catch (error) {
                console.log(error);
                alert(`Form creation wasn't successful.`)
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
