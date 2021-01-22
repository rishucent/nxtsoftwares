(function ($) {
    "use strict";
    $(window).on('az-frontend-before-init', function (event, data) {
        function nested_isotope($grid) {
            var $parent = $grid.parent('.az-isotope-items');
            var back = false;
            $parent.on('layoutComplete', function () {
                if (!back) {
                    $grid.one('layoutComplete', function () {
                        $parent.isotope('layout');
                        back = true;
                    }).isotope('layout');
                }
                back = false;
            });
        }
        function variations_init($item) {
            $item.find('.azh-variations').each(function () {
                var $variations = $(this);
                var $item = $variations.closest('.az-item');
                var $wrapper = $item.children('[data-inverted-styles]');

                $variations.find('.azh-inverted-styles').on('change', function () {
                    if ($wrapper.find('[data-inverted-styles]').length) {
                        if ($(this).prop('checked')) {
                            $wrapper.find('[data-inverted-styles]').attr('data-inverted-styles', 'true');
                        } else {
                            $wrapper.find('[data-inverted-styles]').attr('data-inverted-styles', 'false');
                        }
                    }
                    if ($(this).prop('checked')) {
                        $wrapper.attr('data-inverted-styles', 'true');
                    } else {
                        $wrapper.attr('data-inverted-styles', 'false');
                    }
                });
                if ($wrapper.find('[data-inverted-styles]').length) {
                    if ($wrapper.find('[data-inverted-styles]').attr('data-inverted-styles') == 'true') {
                        $variations.find('.azh-inverted-styles').prop('checked', true);
                    }
                }

                $variations.find('.azh-alternative-styles').on('change', function () {
                    if ($(this).prop('checked')) {
                        $wrapper.find('[data-alternative-styles]').attr('data-alternative-styles', 'true');
                    } else {
                        $wrapper.find('[data-alternative-styles]').attr('data-alternative-styles', 'false');
                    }
                });
                if ($wrapper.find('[data-alternative-styles]').length) {
                    if ($wrapper.find('[data-alternative-styles]').attr('data-alternative-styles') == 'true') {
                        $variations.find('.azh-alternative-styles').prop('checked', true);
                    }
                } else {
                    $variations.find('.azh-alternative-styles').closest('.azh-variation').remove();
                }

                $variations.find('.azh-shadow-border').on('change', function () {
                    if ($(this).prop('checked')) {
                        $wrapper.find('[data-shadow-border]').attr('data-shadow-border', 'true');
                    } else {
                        $wrapper.find('[data-shadow-border]').attr('data-shadow-border', 'false');
                    }
                });
                if ($wrapper.find('[data-shadow-border]').length) {
                    if ($wrapper.find('[data-shadow-border]').attr('data-shadow-border') == 'true') {
                        $variations.find('.azh-shadow-border').prop('checked', true);
                    }
                } else {
                    $variations.find('.azh-shadow-border').closest('.azh-variation').remove();
                }

            });
        }
        var $wrapper = data.wrapper;
        $wrapper.find('.azen-mynx-elements .az-isotope-filters').each(function () {
            var $filters = $(this);
            var $grid = $filters.closest('.az-isotope').find('.az-isotope-items');
            $filters.find('[data-filter]').on('click', function () {
                setTimeout(function () {
                    $grid.one('layoutComplete', function () {
                        $grid.find('> .az-item:not(:hidden)').each(function () {
                            var $item = $(this);
                            var $noscript = $item.find('noscript');
                            if ($noscript.length) {
                                var html = $noscript.html();
                                $noscript.replaceWith(html);
                                var $element = $item.find('> [data-inverted-styles]');
                                azh.frontend_init($element);
                                variations_init($item);
                                $element.imagesLoaded(function () {
                                    $element.find('.az-isotope-items').each(function () {
                                        $(this).one('layoutComplete', function () {
                                            $grid.isotope('layout');
                                        }).isotope('layout');
                                    });
                                    $grid.isotope('layout');
                                    $('html, body').stop().animate({
                                        'scrollTop': $grid.offset().top
                                    }, 1000);
                                });
                            }
                        });
                    });
                });
            });
        });
    });
})(jQuery);