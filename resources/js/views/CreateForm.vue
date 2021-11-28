<template>
    <div>
        <div v-if="!loading">
                <FormulateForm @submit="submitCreateForm">
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
                    <FormulateInput
                        v-model="form.start_time"
                        label="Form start time (WIP)"
                        type="date"
                    />
                    <FormulateInput
                        v-model="form.end_time"
                        label="Form end time (WIP)"
                        type="date"
                    />
                    <FormulateInput
                        v-model="form.has_private_token"
                        label="Form with private access (WIP)"
                        type="checkbox"
                    />
                    <hr>
                    <form-element v-for="item in this.form.items" :key="item.id" :obj="item" @itemChanged="handleItemsChanged"/>
                    <div class="d-flex justify-content-center fixed-bottom bg-info">
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
                {height: 'auto', width: '60%', adaptive: true, scrollable: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged() {
            this.form.items = createFormStore.getItems()
        },
        async submitCreateForm() {
            try {
                this.loading = true;
                await Form.postCreateForm(this.form).then(() => {
                    alert("Form creation was successful.")
                    this.$router.push("/");
                    //createFormStore.clearStore();
                    this.choices = [];
                    this.loading = false;
                })
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
