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
                    { text: 'Workflow Events', link: '/workflow-definition/workflow-events' },
                    { text: 'Workflows', link: '/workflow-definition/workflows' },
                    { text: 'Workflow Activities', link: '/workflow-definition/workflow-activities' },
                    { text: 'Workflow Conditions', link: '/workflow-definition/workflow-conditions' },
                    { text: 'Workflow Transitions', link: '/workflow-definition/workflow-activities' },
                    { text: 'Data Dependencies', link: '/workflow-definition/data-dependencies' },
                ]
            },
            {
                text: 'Workflow Processing',
                items: [
                    { text: 'Workflow Processes', link: '/workflow-processing/workflow-processes' },
                    { text: 'Life Cycle', link: '/workflow-processing/workflow-process-lifecycle' },
                    { text: 'Race Conditions', link: '/workflow-processing/race-conditions.md' },
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
                ]
            }
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/workflowable/workflowable' }
        ]
    }
})
