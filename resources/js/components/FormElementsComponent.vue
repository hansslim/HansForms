<template>
    <div>
        <p v-if="this.pages.length > 1">Page {{ this.currentPage + 1 }}/{{ this.pages.length }}</p>
        <div class="mt-2 mb-2">
            <input
                v-if="this.currentPage > 0"
                type="button"
                @click="handlePagesGoBack"
                class="btn btn-outline-primary"
                value="Previous page"
            />
            <input
                v-if="this.currentPage + 1 !== this.pages.length"
                type="button"
                @click="handlePagesGoNext"
                class="btn btn-outline-primary"
                value="Next page"
            />
        </div>
        <div
            v-for="page in this.pages"
            :key="page.id"
            class="d-none"
            :id="'page-' + page.id"
        >
            <form-element
                v-for="item in page.elements"
                :obj="item"
                :key="item.order"
            />
        </div>
        <div class="mt-2 mb-2">
            <hr />
            <input
                v-if="this.currentPage > 0"
                type="button"
                @click="handlePagesGoBack"
                class="btn btn-outline-primary"
                value="Previous page"
            />
            <input
                v-if="this.currentPage + 1 !== this.pages.length"
                type="button"
                @click="handlePagesGoNext"
                class="btn btn-outline-primary"
                value="Next page"
            />
        </div>
        <FormulateInput
            v-if="this.currentPage + 1 === this.pages.length"
            type="submit"
            name="Submit this form!"
            wrapper-class="text-center"
            input-class="form-control w-50 m-auto btn btn-primary"
        />
    </div>
</template>

<script>
import FormElement from "../components/FormElement.vue";

export default {
    name: "FormElementsComponent",
    components: {
        "form-element": FormElement,
    },
    props: ["elements"],
    data() {
        return {
            pages: [],
            currentPage: 0,
            pageReferences: [],
        };
    },
    mounted() {
        let pages = [];
        let i = 0;

        //first page
        pages.push({
            id: i,
            elements: [],
        });

        this.$props["elements"].forEach((x) => {
            if (x.input_element) {
                pages[i].elements.push(x);
            } else {
                i++;
                pages.push({
                    id: i,
                    elements: [],
                });
            }
        });

        this.pages = pages;
        this.$nextTick(() => {
            this.pages.forEach((x) => {
                this.pageReferences.push(
                    document.querySelector("#page-" + x.id)
                );
            });

            this.pageReferences[0].classList.toggle("d-none");
            this.currentPage = 0;
        });
    },
    methods: {
        handlePagesGoNext() {
            if (this.currentPage + 1 < this.pageReferences.length) {
                this.pageReferences[this.currentPage].classList.toggle(
                    "d-none"
                );
                this.currentPage++;
                this.pageReferences[this.currentPage].classList.toggle(
                    "d-none"
                );
            }
        },
        handlePagesGoBack() {
            if (this.currentPage - 1 >= 0) {
                this.pageReferences[this.currentPage].classList.toggle(
                    "d-none"
                );
                this.currentPage--;
                this.pageReferences[this.currentPage].classList.toggle(
                    "d-none"
                );
            }
        },
    },
};
</script>

<style scoped></style>
