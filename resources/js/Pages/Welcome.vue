<!-- resources/js/Pages/Welcome.vue -->
<template>
    <div class="p-6 bg-gray-100 min-h-screen">
        <h1 class="text-3xl font-bold mb-6">Data from API</h1>
        <div class="jira-section">
            <h2 class="text-2xl font-semibold mb-4">Jira</h2>
            <JiraTable :data="data" />
        </div>
    </div>
</template>

<script>
import JiraTable from './JiraTable.vue';

export default {
    name: 'Welcome',
    components: {
        JiraTable
    },
    mounted() {
        console.log(this.data)
    },
    props: {
        data: Object
    }
}
</script>

<style scoped>
.jira-section {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    background-color: #fff;
    padding: 1.5rem;
}
</style>
