<template>
    <k-panel-inside>
        <k-header>Veranstalter</k-header>
        <k-text>
            Veranstaltungen: {{ total }}
        </k-text>
        <k-text>
            Seite: {{ page }}
        </k-text>
        <k-text>
            Elemente: {{ orgs.length }}
        </k-text>
        <div v-if="orgs">
            <k-items items="1" class="elements" layout="cardlets">

                <k-item v-for="(value, index) in orgs" :text="value.name" :key="index" :info="value.description"
                    layout="carlets"></k-item>
            </k-items>
            <k-pagination :page="page" :total="total" :limit="30" :details="true" @paginate="paginate" />
        </div>

        <div v-else>No items available</div>

    </k-panel-inside>
</template>

<script>

export default {
    props: {
        controller: Object
    },
    // Put your section logic here
    created() {
        console.log(this.controller);
    },
    computed: {
        orgs() {
            return this.controller.orgs
        },
        total() {
            return this.controller.total
        },
        has_prev() {
            return this.controller.has_prev
        },
        has_next() {
            return this.controller.has_next
        },
        page() {
            return this.controller.page
        },
        pages() {
            return this.controller.pages
        },
    },


    methods: {
        paginate(pagination) {
            this.$reload({
                query: {
                    page: pagination.page
                }
            });
        }
    }
}
</script>

<style>
/** Put your CSS here **/
</style>
