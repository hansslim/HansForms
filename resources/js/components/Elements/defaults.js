export const FormElementDefaultComputedProps = {
    propsLabel() {
        return this.$props["label"];
    },
    propsIsMandatory() {
        return this.$props["isMandatory"];
    },
}

export const FormElementDefaultMethods = {
    propsId(type) {
        const fullId = (id) => {
            return `${type}-${id}`;
        }
        return fullId(this.$props["obj"].id);
    }
}

export const FormElementDefaultProps = ["label", "obj", "isMandatory"];
