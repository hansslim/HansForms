export let createFormStore = {
    data: {
        items: [],
    },
    getItems() {
        return this.data.items;
    },
    addItem(item) {
        this.data.items = [...this.data.items, item];
        this.refreshItemsOrder();
    },
    refreshItemsOrder() {
        let i = 0;
        this.data.items = this.data.items.map((x)=>{
            let obj = x;
            obj.order = i++;
            return obj;
        });
    },
    sortItemsByOrder() {
        this.data.items = this.data.items.sort((a, b) => {
            if (a.order < b.order) {
                return -1;
            }
            if (a.order > b.order) {
                return 1;
            }
            return 0;
        });
    },
    changeItem(item) {
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.data.items = [...this.data.items, item];
        this.sortItemsByOrder();
    },
    deleteItem(item) {
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.refreshItemsOrder();
    },
    clearStore() {
        this.data.items = []
    },
    setItems(items) {
        this.data.items = items
    }
}

export let createFormChoicesStore = {
    data: {
        items: [],
    },
    getItems() {
        return this.data.items;
    },
    addItem(item) {
        this.data.items = [...this.data.items, item];
        this.refreshItemsOrder();
    },
    refreshItemsOrder() {
        let i = 0;
        this.data.items = this.data.items.map((x)=>{
            let obj = x;
            obj.order = i++;
            return obj;
        });
    },
    sortItemsByOrder() {
        this.data.items = this.data.items.sort((a, b) => {
            if (a.order < b.order) {
                return -1;
            }
            if (a.order > b.order) {
                return 1;
            }
            return 0;
        });
    },
    changeItem(item) {
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.data.items = [...this.data.items, item];
        this.sortItemsByOrder();
    },
    deleteItem(item) {
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.refreshItemsOrder();
    },
    clearStore() {
        this.data.items = []
    },
    setItems(items) {
        this.data.items = items
    }
}


