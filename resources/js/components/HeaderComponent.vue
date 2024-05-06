<template>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">Weather App&nbsp;<font-awesome-icon :icon="['fas', 'cloud-showers-water']" /></a>


            </div>
        </nav>
    </header>

    <div class=" bg-dark-lighter py-1">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <input v-model="searchTerm" type="text" class="form-control" placeholder="City...">
                        <button @click="search" class="btn bg-white" type="button"><font-awesome-icon :icon="['fas', 'magnifying-glass']" /></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'HeaderComponent',
    data() {
        return {
            searchTerm: ''
        }
    },
    methods: {
        search() {
            // Emit search event to parent (HeaderComponent.vue)
            this.$emit('search', this.searchTerm);
        }
    }
}
</script>
