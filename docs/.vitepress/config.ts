import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "Workflowable",
    description: "A Workflowable workflow engine",
    base: "https://workflowable.github.io/workflowable/",
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [

        ],

        sidebar: [
            {
                text: 'Concepts',
                items: [
                    { text: 'Workflow Events', link: 'concepts#workflow-events' },
                    { text: 'Workflows', link: 'concepts#workflows' },
                    { text: 'Workflow Priorities', link: 'concepts#workflow-priorities' },
                    { text: 'Workflow Steps', link: 'concepts#workflow-steps' },
                    { text: 'Workflow Transitions', link: 'concepts#workflow-transitions' },
                    { text: 'Workflow Runs', link: 'concepts#workflow-runs' },
                ]
            },
            {
                text: 'Setup',
                items: [
                    { text: 'Triggering Workflow Events', link: 'commands' },
                    { text: 'Dispatching Workflow Runs', link: 'data-dependencies' },
                    { text: 'Infrastructure', link: 'race-conditions.md' },
                ]
            },
            {
                text: 'Advanced',
                items: [
                    { text: 'Commands', link: 'commands' },
                    { text: 'Data Dependencies', link: 'data-dependencies' },
                    { text: 'Race Conditions', link: 'race-conditions.md' },
                ]
            }
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/workflowable/workflowable' }
        ]
    }
})
