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
                text: 'Concepts',
                items: [
                    { text: 'Events', link: '/events' },
                    { text: 'Workflows', link: '/workflows' },
                    { text: 'Processes', link: '/processes' },
                    { text: 'Swaps', link: '/swaps' },
                    { text: 'Forms', link: '/forms'}
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
