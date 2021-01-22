(function ($) {
    "use strict";
    window.azh = $.extend({}, window.azh);
    if (!('controls_options' in azh)) {
        azh.controls_options = [];
    }
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "dropdown-attribute",
            "menu": "utility",
            "target_utility": ".az-isotope-items",
            "options": "data-variants",
            "select2": true,
            "group": "Grid items",
            "selector": ".az-isotope-items > .az-item [data-variant]",
            "attribute": "data-variant",
            "control_class": "azh-dropdown",
            "control_type": "variant",
            "control_text": "Variant"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "target_utility": ".az-isotope-items",
            "group": "Grid items",
            "selector": ".az-isotope-items > .az-item [data-inverted-styles]",
            "attribute": "data-inverted-styles",
            "control_class": "azh-toggle",
            "control_type": "data-inverted-styles",
            "control_text": "Inverted styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "target_utility": ".az-isotope-items",
            "control_class": "azh-toggle",
            "group": "Grid items",
            "selector": ".az-isotope-items > .az-item [data-alternative-styles]",
            "attribute": "data-alternative-styles",
            "control_type": "data-alternative-styles",
            "control_text": "Alternative styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "target_utility": ".az-isotope-items",
            "control_class": "azh-toggle",
            "group": "Grid items",
            "selector": ".az-isotope-items > .az-item [data-shadow-border]",
            "attribute": "data-shadow-border",
            "control_type": "data-shadow-border",
            "control_text": "Shadow / Border"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "target_utility": ".az-isotope-items",
            "control_class": "azh-toggle",
            "group": "Grid items",
            "selector": ".az-isotope-items > .az-item [data-border-radius]",
            "attribute": "data-border-radius",
            "control_type": "data-border-radius",
            "control_text": "Border radius"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "group": "Slides",
            "selector": ".az-swiper > .az-wrapper > .az-slide [data-inverted-styles]",
            "attribute": "data-inverted-styles",
            "control_type": "data-inverted-styles",
            "control_text": "Inverted styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "group": "Slides",
            "selector": ".az-swiper > .az-wrapper > .az-slide [data-alternative-styles]",
            "attribute": "data-alternative-styles",
            "control_type": "data-alternative-styles",
            "control_text": "Alternative styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "group": "Slides",
            "selector": ".az-swiper > .az-wrapper > .az-slide [data-shadow-border]",
            "attribute": "data-shadow-border",
            "control_type": "data-shadow-border",
            "control_text": "Shadow / Border"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "group": "Slides",
            "selector": ".az-swiper > .az-wrapper > .az-slide [data-border-radius]",
            "attribute": "data-border-radius",
            "control_type": "data-border-radius",
            "control_text": "Border radius"
        }        
    ]);
    azh.controls_options = azh.controls_options.concat([        
        {
            "refresh": true,
            "type": "image-attribute",
            "menu": "utility",
            "group": "Panorama image",
            "attribute": "data-image-src",
            "control_class": "azh-image",
            "control_type": "image",
            "control_text": "Image URL",
            "selector": '[data-image-src]'
        },
        {
            "type": "input-attribute",
            "menu": "utility",
            "control_text": "Slide name",
            "control_class": "azh-name",
            "control_type": "name",
            "selector": ".az-carousel > div, .az-carousel > a, .az-slides > div, .az-slides > a",
            "attribute": "data-name"
        },
        {
            "type": "url-attribute",
            "menu": "utility",
            "control_text": "Soundcloud URL",
            "control_class": "azh-src",
            "control_type": "src",
            "selector": ".azen iframe[src*='soundcloud']",
            "attribute": "src"
        },
        {
            "type": "background-image",
            "menu": "context",
            "control_text": "Background image",
            "control_class": "azh-background-image",
            "control_type": "background-image",
            "is_selector": '.azen [style]',
            "property": "background-image"
        },
        {
            "type": "color-style",
            "menu": "context",
            "control_text": "Color",
            "control_class": "azh-color",
            "control_type": "color",
            "is_selector": '.azen [style]',
            "property": "color"
        },
        {
            "type": "color-style",
            "menu": "context",
            "control_text": "Background color",
            "control_class": "azh-background-color",
            "control_type": "background-color",
            "is_selector": '.azen [style]',
            "property": "background-color"
        },
        {
            "refresh": true,
            "type": "color-attribute",
            "menu": "context",
            "control_text": "Foreground color",
            "control_class": "azh-fgcolor",
            "control_type": "fgcolor",
            "attribute": "data-fgcolor"
        },
        {
            "refresh": true,
            "type": "color-attribute",
            "menu": "context",
            "control_text": "Background color",
            "control_class": "azh-bgcolor",
            "control_type": "bgcolor",
            "attribute": "data-bgcolor"
        },
        {
            "refresh": true,
            "type": "input-attribute",
            "input_type": "date",
            "menu": "context",
            "control_text": "Countdown date",
            "control_class": "azh-countdown",
            "control_type": "countdown",
            "selector": ".az-countdown",
            "attribute": "data-time"
        }
    ]);
    if (!('modal_options' in azh)) {
        azh.modal_options = [];
    }
    azh.modal_options = azh.modal_options.concat([
        {
            "section_control": false,
            "refresh": false,
            "button_text": "",
            "button_class": "azh-video",
            "button_type": "azh-edit-link",
            "title": "Edit Video URL",
            "desc": "Specify the URL of the video which will be showed in popup",
            "label": "URL",
            "selector": "a.az-iframe-popup",
            "attribute": "href"
        }
    ]);
    //univer
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-dark]",
            "attribute": "data-dark",
            "control_type": "data-dark",
            "control_text": "Styles for white background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-white]",
            "attribute": "data-white",
            "control_type": "data-white",
            "control_text": "Styles for dark background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-completed]",
            "attribute": "data-completed",
            "control_type": "data-completed",
            "control_text": "Completed"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-enable-or-disable-title]",
            "attribute": "data-enable-or-disable-title",
            "control_type": "data-enable-or-disable-title",
            "control_text": "Show title"
        }
    ]);
    //food
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-white-background]",
            "attribute": "data-white-background",
            "control_type": "data-white-background",
            "control_text": "White background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-accent-font]",
            "attribute": "data-accent-font",
            "control_type": "data-accent-font",
            "control_text": "Use accent font"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-brand-color]",
            "attribute": "data-brand-color",
            "control_type": "data-brand-color",
            "control_text": "Use brand color"
        }
    ]);
    //rafale
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-top]",
            "attribute": "data-over-top",
            "control_type": "data-over-top",
            "control_text": "Overlay to above content"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-bottom]",
            "attribute": "data-over-bottom",
            "control_type": "data-over-bottom",
            "control_text": "Overlay to below content"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-best-seller]",
            "attribute": "data-best-seller",
            "control_type": "data-best-seller",
            "control_text": "Best seller"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-text-color]",
            "attribute": "data-text-color",
            "control_type": "data-text-color",
            "control_text": "Black font color"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-darken-background]",
            "attribute": "data-darken-background",
            "control_type": "data-darken-background",
            "control_text": "Darken background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-background-color-black]",
            "attribute": "data-background-color-black",
            "control_type": "data-background-color-black",
            "control_text": "Black background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-black-background]",
            "attribute": "data-black-background",
            "control_type": "data-black-background",
            "control_text": "Black background"
        }
    ]);
    //creo
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-content-revers]",
            "attribute": "data-content-revers",
            "control_type": "data-content-revers",
            "control_text": "Reverse content"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-content-reverse]",
            "attribute": "data-content-reverse",
            "control_type": "data-content-reverse",
            "control_text": "Reverse content"
        }
    ]);
    //doo
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-content]",
            "attribute": "data-over-content",
            "control_type": "data-over-content",
            "control_text": "Overlay to below content"
        }
    ]);
    //strong
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-reverse]",
            "attribute": "data-reverse",
            "control_type": "data-reverse",
            "control_text": "Reverse content"
        }
    ]);
    //listino
    azh.controls_options = azh.controls_options.concat([
    ]);
    //citrix
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-inverted-styles]",
            "attribute": "data-inverted-styles",
            "control_type": "data-inverted-styles",
            "control_text": "Inverted styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-gray-background]",
            "attribute": "data-gray-background",
            "control_type": "data-gray-background",
            "control_text": "Gray background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-light-branded-background]",
            "attribute": "data-light-branded-background",
            "control_type": "data-light-branded-background",
            "control_text": "Light-branded background"
        }
    ]);
    //citra
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-alternative-styles]",
            "attribute": "data-alternative-styles",
            "control_type": "data-alternative-styles",
            "control_text": "Alternative styles"
        }
    ]);
    //elastic
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "exists-class",
            "menu": "utility",
            "control_text": "Active",
            "control_class": "azh-toggle",
            "control_type": "active",
            "selector": ".azen-elastic-pricing-2",
            "class": "azen-elastic-active"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-shadow-border]",
            "attribute": "data-shadow-border",
            "control_type": "data-shadow-border",
            "control_text": "Shadow / Border"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-border-radius]",
            "attribute": "data-border-radius",
            "control_type": "data-border-radius",
            "control_text": "Border radius"
        },
        {
            "type": "background-image",
            "menu": "context",
            "control_text": "Icon background image",
            "control_class": "azh-background-image",
            "control_type": "background-image",
            "selector": '[class*="azen-elastic"] .azen-icon .az-icon',
            "property": "background-image"
        },
        {
            "type": "color-style",
            "menu": "context",
            "control_text": "Icon background color",
            "control_class": "azh-background-color",
            "control_type": "background-color",
            "selector": '[data-alternative-styles] .azen-icon .az-icon span',
            "property": "background-color"
        }
    ]);
    //mynx
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "background-image",
            "menu": "context",
            "control_text": "Icon background image",
            "control_class": "azh-background-image",
            "control_type": "background-image",
            "selector": '[class*="azen-mynx"] .azen-icon .az-icon',
            "property": "background-image"
        },
        {
            "type": "dropdown-attribute",
            "menu": "utility",
            "options": "data-variants",
            "attribute": "data-variant",
            "select2": true,
            "control_class": "azh-dropdown",
            "control_type": "variant",
            "control_text": "Variant"
        },
        {
            "type": "dropdown-attribute",
            "menu": "utility",
            "options": {
                "large": "Large",
                "medium": "Medium",
                "small": "Small"
            },
            "attribute": "data-size",
            "control_class": "azh-dropdown",
            "control_type": "size",
            "control_text": "Size"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-with-icons]",
            "attribute": "data-with-icons",
            "control_type": "data-with-icons",
            "control_text": "With icons"
        },
        {
            "refresh": true,
            "type": "exists-class",
            "menu": "utility",
            "control_text": "Boxed content",
            "control_class": "azh-toggle",
            "control_type": "container",
            "selector": ".azen-mynx-site-header .azen-header-parts",
            "class": "az-container"
        },        
            {
                "type": "integer-list-style",
                "menu": "utility",
                "selector": ".azen-mynx-site-header .azen-header-parts",
                "responsive": true,
                "properties": {
                    "margin-left": "Offset Left",
                    "margin-right": "Offset Right",
                },
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-margin",
                "control_type": "margin",
                "control_text": "Boxed content offsets"
            }
    ]);
})(window.jQuery);
