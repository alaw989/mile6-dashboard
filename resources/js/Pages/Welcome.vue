<!-- resources/js/Pages/Welcome.vue -->
<template>
    <div>
        <h1>Data from API</h1>
        <pre>{{ data }}</pre>
    </div>
</template>

<script>
export default {
    name: 'Welcome',
    props: {
        data: Object
    },
    mounted() {
        console.log(this.data);
    }
}
</script>

<style scoped>
/* Add your styles here */
</style>
