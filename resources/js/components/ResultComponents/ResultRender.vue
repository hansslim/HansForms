<template>
    <div>
        <list-view v-if="this.selectedView==='list'" :parsedData="this.$props['parsedData']"></list-view>
        <div v-if="this.selectedView==='pie' || this.selectedView==='bar'">
            <FormulateInput
                class="btn"
                @click="handleChangeView"
                label="Change view"
                type="button"
            />
            <pie-view
                style="height: 300px"
                v-if="this.selectedView==='pie'"
                :parsedData="this.$props['parsedData']"
            />
            <bar-view
                style="height: 300px"
                v-if="this.selectedView==='bar'"
                :parsedData="this.$props['parsedData']"
            />
        </div>
    </div>
</template>

<script>
import List from "./Views/List";
import Pie from "./Views/Pie";
import Bar from "./Views/Bar";

export default {
    name: "ResultRender",
    components: {
        'list-view': List,
        'pie-view': Pie,
        'bar-view': Bar
    },
    props: ['viewType', 'parsedData'],
    data() {
        return {
            selectedView: null
        }
    },
    created() {
        this.$nextTick(()=>{
            this.selectedView = this.viewType;
        })
    },
    methods: {
        handleChangeView() {
            this.$nextTick(()=>{
                if (this.selectedView === 'bar') this.selectedView = "pie";
                else if (this.selectedView === 'pie') this.selectedView = "bar";
            })
        }
    }
}
</script>

<style scoped>

</style>
