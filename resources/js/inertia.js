import React from "react";
import {createInertiaApp} from '@inertiajs/react'
import {createRoot} from 'react-dom/client'
import MainLayout from "./layouts/MainLayout";

createInertiaApp({
    resolve: name => {
        const page = require(`./Pages/${name}`);
        page.default.layout = page.default.layout || (page => <MainLayout children={page} />)
        return page
    },
    setup({el, App, props}) {
        createRoot(el).render(<App {...props}/>)
    },
    progress: {
        color: '#bc2126',
    },
});
