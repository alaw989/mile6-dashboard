<!-- resources/js/Components/JiraTable.vue -->
<template>
    <div v-for="(issuesBySprint, projectName) in groupedData" :key="projectName" class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">{{ projectName }}</h2>
        <div v-for="(issues, sprintName) in issuesBySprint" :key="sprintName" class="mb-4">
            <h3 class="text-xl font-semibold mb-2">{{ sprintName }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md">
                    <thead>
                    <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Key</th>
                        <th class="py-3 px-6 text-left">Summary</th>
                        <th class="py-3 px-6 text-left">Status</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="issue in issues" :key="issue.key" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ issue.key }}</td>
                        <td class="py-3 px-6 text-left">
                            <a target="_blank" :href="'https://mile6.atlassian.net/browse/' + issue.key" class="text-blue-500 hover:underline">{{ issue.summary }}</a>
                        </td>
                        <td class="py-3 px-6 text-left">{{ issue.status.statusCategory.name }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'JiraTable',
    props: {
        data: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            groupedData: {}
        };
    },
    mounted() {
        this.groupIssuesByProjectAndSprint();
    },
    methods: {
        groupIssuesByProjectAndSprint() {
            this.groupedData = this.data.tasks.reduce((acc, issue) => {
                const projectName = issue.project.name;
                const sprintName = issue.sprint.name;

                if (!acc[projectName]) {
                    acc[projectName] = {};
                }

                if (!acc[projectName][sprintName]) {
                    acc[projectName][sprintName] = [];
                }

                acc[projectName][sprintName].push(issue);
                return acc;
            }, {});

        }
    }
}
</script>

<style scoped>
/* Add your styles here */
</style>
