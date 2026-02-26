import "./bootstrap";

import {createApp, h} from "vue";
import {createInertiaApp} from "@inertiajs/vue3";
import {ZiggyVue} from "ziggy-js";
import {Ziggy} from './ziggy.js';
import {MAIN_THEME_DARK_MODE_SELECTOR, MainLayout} from "@danaflex/layout";
import {DanaflexUI} from "@danaflex/ui";
import {MainTheme} from "@danaflex/ui/themes";
import "../css/app.css";
import "@danaflex/ui/icons/icons.css";
import "@danaflex/layout/MainLayout.css";
import {ConfirmationService, DialogService, ToastService} from "@danaflex/ui/services";
import {Tooltip} from "@danaflex/ui/directives";
import {ru} from "primelocale/ru.json"

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", {eager: true});
        const page = pages[`./Pages/${name}.vue`];
        page.default.layout = page.default.layout || MainLayout;
        return page;
    },
    setup({el, App, props, plugin}) {
        const app = createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(DanaflexUI, {
                locale: ru,
                theme: {
                    preset: MainTheme,
                    options: {
                        darkModeSelector: `.${MAIN_THEME_DARK_MODE_SELECTOR}`,
                        cssLayer: {
                            name: "danaflex-ui",
                            order: "tailwind-base, danaflex-ui, tailwind-utilities"
                        }
                    }
                }
            })
            .use(ToastService)
            .use(DialogService)
            .use(ConfirmationService)
        app.directive("tooltip", Tooltip)
        app.mount(el);
    }
});
