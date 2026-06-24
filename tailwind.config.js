import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: "class",
    theme: {
        extend: {
            "colors": {
                "secondary-container": "#fdc425",
                "tertiary-fixed-dim": "#c4c7c9",
                "surface": "#f8f9ff",
                "on-tertiary-fixed-variant": "#444749",
                "surface-dim": "#cbdbf5",
                "inverse-primary": "#bec2ff",
                "on-primary-fixed-variant": "#3b4084",
                "on-surface": "#0b1c30",
                "primary-container": "#2f3478",
                "surface-bright": "#f8f9ff",
                "surface-container-lowest": "#ffffff",
                "on-tertiary": "#ffffff",
                "secondary-fixed-dim": "#f7be1d",
                "surface-tint": "#53589e",
                "secondary-fixed": "#ffdf9a",
                "on-primary": "#ffffff",
                "inverse-on-surface": "#eaf1ff",
                "surface-container": "#e5eeff",
                "on-secondary-container": "#6d5200",
                "surface-container-high": "#dce9ff",
                "on-secondary": "#ffffff",
                "primary-fixed": "#e0e0ff",
                "secondary": "#785a00",
                "on-primary-container": "#9aa0eb",
                "on-error": "#ffffff",
                "on-secondary-fixed": "#251a00",
                "on-error-container": "#93000a",
                "error": "#ba1a1a",
                "inverse-surface": "#213145",
                "on-background": "#0b1c30",
                "surface-container-low": "#eff4ff",
                "on-primary-fixed": "#0b0f58",
                "tertiary": "#232628",
                "surface-variant": "#d3e4fe",
                "error-container": "#ffdad6",
                "primary": "#181c61",
                "tertiary-container": "#393c3e",
                "on-secondary-fixed-variant": "#5a4300",
                "primary-fixed-dim": "#bec2ff",
                "outline-variant": "#c7c5d2",
                "background": "#f8f9ff",
                "on-surface-variant": "#464650",
                "tertiary-fixed": "#e0e3e5",
                "surface-container-highest": "#d3e4fe",
                "outline": "#777681",
                "on-tertiary-container": "#a3a6a8",
                "on-tertiary-fixed": "#191c1e"
            },
            "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            "spacing": {
                "sidebar-width": "260px",
                "container-padding": "24px",
                "gutter": "20px",
                "card-gap": "24px",
                "base": "8px"
            },
            "fontFamily": {
                "headline-md": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "body-md": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "body-lg": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "data-display": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "label-md": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "headline-lg": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans],
                "headline-xl": ["Hanken Grotesk", ...defaultTheme.fontFamily.sans]
            },
            "fontSize": {
                "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "data-display": ["28px", {"lineHeight": "32px", "fontWeight": "700"}],
                "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                "headline-lg": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "headline-xl": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/container-queries'),
    ],
};
