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
                text: 'Workflows',
                items: [
                    { text: 'Workflow Events', link: '/workflows/workflow-events' },
                    { text: 'Workflows', link: '/workflows/workflows' },
                    { text: 'Workflow Activities', link: '/workflows/workflow-activities' },
                    { text: 'Workflow Conditions', link: '/workflows/workflow-conditions' },
                    { text: 'Workflow Transitions', link: '/workflows/workflow-transitions' },
                    { text: 'Workflow Swaps', link: '/workflows/workflow-swaps' },
                    { text: 'Data Dependencies', link: '/workflows/data-dependencies' },
                ]
            },
            {
                text: 'Workflow Processes',
                items: [
                    { text: 'Workflow Processes', link: '/workflow-processing/workflow-processes' },
                    { text: 'Life Cycle', link: '/workflow-processing/workflow-process-lifecycle' },
                    { text: 'Race Conditions', link: '/workflow-processing/race-conditions.md' },
                ]
            },
            {
                text: 'Misc',
                items: [
                    { text: 'Events', link: 'misc/events' },
                    { text: 'Commands', link: 'misc/commands' },
                ]
            }
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/workflowable/workflowable' }
        ]
    }
})
