import { defineConfig } from 'vitepress'
import { withMermaid} from "vitepress-plugin-mermaid";
// https://vitepress.dev/reference/site-config
export default withMermaid({
    title: "Workflowable",
    description: "A Workflowable workflow engine",
    base: "/",
    mermaid: {
        // refer https://mermaid.js.org/config/setup/modules/mermaidAPI.html#mermaidapi-configuration-defaults for options
    },
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [

        ],

        sidebar: [
            {
                text: 'Workflow Definition',
                items: [
                    { text: 'Workflow Events', link: '/workflow-events' },
                    { text: 'Workflows', link: '/workflows' },
                    { text: 'Workflow Activities', link: '/workflow-activities' },
                    { text: 'Workflow Transitions', link: '/workflow-activities' },
                ]
            },
            {
                text: 'Workflow Processing',
                items: [
                    { text: 'Workflow Processes', link: '/workflow-processes' },
                    { text: 'Life Cycle', link: '/workflow-process-lifecycle' },
                ]
            },
            /*{
                text: 'Setup',
                items: [
                    { text: 'Triggering Workflow Events', link: '/commands' },
                    { text: 'Dispatching Workflow Runs', link: '/data-dependencies' },
                    { text: 'Infrastructure', link: '/race-conditions' },
                ]
            },*/
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
