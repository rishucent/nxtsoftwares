(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    window.azh = $.extend({}, window.azh);
    azh.parse_query_string = function (a) {
        if (a == "")
            return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p = a[i].split('=');
            if (p.length != 2)
                continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    };
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    var customize = ('azh' in $.QueryString && $.QueryString['azh'] == 'customize');
    function refresh_maps($wrapper) {
        $wrapper.find('.az-image-map').each(function () {
            var $map = $(this);
            if ($map.find('[data-element].azt-exists .az-polygone[data-id]').length) {
                $map.find('[data-element].azt-exists .az-polygone[data-id].az-active').removeClass('az-active');
                $map.find('[data-element].az-exists .az-polygone[data-id] svg polygon').first().trigger('mouseenter').closest('.az-svg').addClass('az-active').removeClass('az-hover');
            }
        });
    }
    $window.on('az-frontend-before-init', function (event, data) {
        function reset_template($template) {
            $template.find('.azt-filled').each(function () {
                var $this = $(this);
                if ($this.is('.azt-exists')) {
                    $this.removeClass('az-exists');
                }
                if ($this.is('.azt-table')) {
                    var $body = $this.find('tbody');
                    while ($body.children().length > 1) {
                        $body.children().last().remove();
                    }
                    $body.find('[data-azt-key][data-azt-value]').each(function () {
                        $(this).attr('data-azt-value', '');
                    });
                }
                $this.contents().filter(function () {
                    return this.nodeType === 3;
                }).each(function () {
                    if ($this.data('azt-original-text')) {
                        this.textContent = $this.data('azt-original-text');
                    }
                });
                $this.removeClass('azt-filled');
            });
        }
        function field_sum(rows, field) {
            var sum = 0;
            $(rows).each(function () {
                sum = sum + parseFloat(this[field]);
            });
            return sum;
        }
        function field_max(rows, field) {
            var max = parseFloat(rows[0][field]);
            $(rows).each(function () {
                if (max < parseFloat(this[field])) {
                    max = parseFloat(this[field]);
                }
            });
            return max;
        }
        function field_min(rows, field) {
            var min = parseFloat(rows[0][field]);
            $(rows).each(function () {
                if (min > parseFloat(this[field])) {
                    min = parseFloat(this[field]);
                }
            });
            return min;
        }
        function field_value(rows, field) {
            var value = false;
            if (rows.length) {
                value = rows[0][field];
                $(rows).each(function () {
                    if (value != this[field]) {
                        value = false;
                        return false;
                    }
                });
            }
            return value;
        }
        function replace_text($wrapper, from, to) {
            $wrapper.contents().filter(function () {
                return this.nodeType === 3;
            }).each(function () {
                var $this = $(this);
                if (!$wrapper.data('azt-original-text')) {
                    $wrapper.data('azt-original-text', this.textContent);
                }
                this.textContent = this.textContent.replace(from, to);
            });
        }
        function fill_template($template, current_fields) {
            $template.find('.azt-table:not(.azt-filled)').each(function () {
                var $table = $(this);
                $table.addClass('azt-filled');
                var $body = $table.find('tbody');
                if ($body.children().length) {
                    var $row = $body.children().first();
                    $row.detach();
                    if (azt.current_rows.length > 0) {
                        $(azt.current_rows).each(function () {
                            var current_row = this;
                            var $new_row = $row.clone(true);
                            azt.current_row = current_row;
                            fill_template($new_row, current_row);
                            azt.current_row = false;
                            $body.append($new_row);
                        });
                    } else {
                        $body.append($row);
                    }
                }
                azt.click_urls = false;
            });
            if (azt.current_row) {
                $template.find('[data-azt-key]:not([data-azt-key=""])[data-azt-value=""]').each(function () {
                    $(this).attr('data-azt-value', azt.current_row[$(this).attr('data-azt-key')]);
                });
            }
            for (var field in azt.fields) {
                var unique_value = field_value(azt.current_rows, field);
                $template.find('.azt-' + field + ':not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if (azt.current_row) {
                        $this.addClass(azt.current_row[field]);
                    } else {
                        if (unique_value) {
                            $this.addClass(unique_value);
                        }
                    }
                    $this.addClass('azt-filled');
                });
                $template.find('[' + field + ']:not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if (azt.current_row) {
                        $this.attr(field, azt.current_row[field]);
                        $this.closest('[data-element]').show();
                    } else {
                        if (unique_value) {
                            $this.attr(field, unique_value);
                            $this.closest('[data-element]').show();
                        } else {
                            $this.closest('[data-element]').hide();
                        }
                    }
                    $this.addClass('azt-filled');
                });
                $template.find('[name="' + field + '"][type="hidden"]:not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if (azt.current_row) {
                        $this.val(azt.current_row[field]);
                    } else {
                        if (unique_value) {
                            $this.val(unique_value);
                        }
                    }
                    $this.addClass('azt-filled');
                });
                $template.find(':contains("azt-' + field + '"):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if ($this.children().length === 0) {
                        if (azt.current_row) {
                            replace_text($this, 'azt-' + field, azt.current_row[field]);
                            $this.closest('[data-element]').show();
                        } else {
                            if (unique_value) {
                                replace_text($this, 'azt-' + field, unique_value);
                                $this.closest('[data-element]').show();
                            } else {
                                $this.closest('[data-element]').hide();
                            }
                        }
                        $this.addClass('azt-filled');
                    }
                });
                $template.find('[data-azt-background-image-key]:not([data-azt-background-image-key=""]):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if (azt.current_row) {
                        $this.css('background-image', 'url("' + azt.current_row[$this.attr('data-azt-background-image-key')] + '")');
                        $this.closest('[data-element]').show();
                    } else {
                        var value = field_value(azt.current_rows, $this.attr('data-azt-background-image-key'));
                        if (value) {
                            $this.css('background-image', 'url("' + value + '")');
                            $this.closest('[data-element]').show();
                        } else {
                            $this.closest('[data-element]').hide();
                        }
                    }
                    $this.addClass('azt-filled');
                });
                $template.find('[data-azt-src-key]:not([data-azt-src-key=""]):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if (azt.current_row) {
                        $this.attr('src', azt.current_row[$this.attr('data-azt-src-key')]);
                        $this.closest('[data-element]').show();
                    } else {
                        var value = field_value(azt.current_rows, $this.attr('data-azt-src-key'));
                        if (value) {
                            $this.attr('src', value);
                            $this.closest('[data-element]').show();
                        } else {
                            $this.closest('[data-element]').hide();
                        }
                    }
                    $this.addClass('azt-filled');
                });
                $template.find('[data-azt-url-key]:not([data-azt-url-key=""]):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    if ($this.is('[href]')) {
                        if (azt.current_row) {
                            $this.attr('href', azt.current_row[$this.attr('data-azt-url-key')]);
                            $this.closest('[data-element]').show();
                        } else {
                            var value = field_value(azt.current_rows, $this.attr('data-azt-url-key'));
                            if (value) {
                                $this.attr('href', value);
                                $this.closest('[data-element]').show();
                            } else {
                                $this.closest('[data-element]').hide();
                            }
                        }
                    }
                    if ($this.is('[data-click-url]')) {
                        if (azt.current_row) {
                            $this.attr('data-click-url', azt.current_row[$this.attr('data-azt-url-key')]);
                            $this.closest('[data-element]').show();
                        } else {
                            var value = field_value(azt.current_rows, $this.attr('data-azt-url-key'));
                            if (value) {
                                $this.attr('data-click-url', value);
                                $this.closest('[data-element]').show();
                            } else {
                                $this.closest('[data-element]').hide();
                            }
                        }
                    }
                    $this.addClass('azt-filled');
                });
            }
            if (azt.current_rows.length > 0) {
                for (var field in current_fields) {
                    if (typeof current_fields[field] === 'string') {
                        $template.find('.azt-' + field + ':not(.azt-filled)').each(function () {
                            var $this = $(this);
                            $this.addClass(current_fields[field]);
                            $this.addClass('azt-filled');
                        });
                        $template.find('[' + field + ']:not(.azt-filled)').each(function () {
                            var $this = $(this);
                            $this.attr(field, current_fields[field]);
                            $this.addClass('azt-filled');
                        });
                        $template.find(':contains("azt-' + field + '"):not(.azt-filled)').each(function () {
                            var $this = $(this);
                            replace_text($this, 'azt-' + field, current_fields[field]);
                            $this.addClass('azt-filled');
                        });
                    }
                }
            }
            $template.find('.azt-exists:not(.azt-filled)').addBack().filter('.azt-exists:not(.azt-filled)').each(function () {
                var $this = $(this);
                if (azt.current_rows.length > 0) {
                    $this.addClass('az-exists');
                }
                $this.addClass('azt-filled');
            });
            $template.find(':contains("azt-count"):not(.azt-filled)').each(function () {
                var $this = $(this);
                replace_text($this, 'azt-count', azt.current_rows.length);
                $this.addClass('azt-filled');
            });
            for (var field in azt.fields) {
                $template.find(':contains("azt-sum-' + field + '"):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    replace_text($this, 'azt-sum-' + field, field_sum(azt.current_rows, field));
                    $this.addClass('azt-filled');
                });
                $template.find(':contains("azt-max-' + field + '"):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    replace_text($this, 'azt-max-' + field, field_max(azt.current_rows, field));
                    $this.addClass('azt-filled');
                });
                $template.find(':contains("azt-min-' + field + '"):not(.azt-filled)').each(function () {
                    var $this = $(this);
                    replace_text($this, 'azt-min-' + field, field_min(azt.current_rows, field));
                    $this.addClass('azt-filled');
                });
            }
        }
        function select_rows(current_fields) {
            azt.current_row = false;
            azt.current_rows = [];
            var current_rows = azt.table;
            for (var field in current_fields) {
                var rows = [];
                $(current_rows).each(function () {
                    if (typeof current_fields[field] === 'object') {
                        if ('from' in current_fields[field] && 'to' in current_fields[field]) {
                            if (parseFloat(this[field]) >= parseFloat(current_fields[field]['from']) && parseFloat(this[field]) <= parseFloat(current_fields[field]['to'])) {
                                rows.push(this);
                            }
                        } else {
                            if ('from' in current_fields[field]) {
                                if (parseFloat(this[field]) >= parseFloat(current_fields[field]['from'])) {
                                    rows.push(this);
                                }
                            }
                            if ('to' in current_fields[field]) {
                                if (parseFloat(this[field]) <= parseFloat(current_fields[field]['to'])) {
                                    rows.push(this);
                                }
                            }
                        }
                    } else {
                        if (current_fields[field] === '' || this[field] == current_fields[field]) {
                            rows.push(this);
                        }
                    }
                });
                current_rows = rows;
            }
            if (current_rows.length == 1) {
                azt.current_row = current_rows[0];
            }
            azt.current_rows = current_rows;
        }
        function get_controls_fields($wrapper) {
            var controls_fields = {};
            for (var field in azt.fields) {
                $wrapper.find('input[type="checkbox"][name="' + field + '"]:checked').each(function () {
                    controls_fields[field] = $(this).val();
                });
                $wrapper.find('input[type="radio"][name="' + field + '"]:checked').each(function () {
                    controls_fields[field] = $(this).val();
                });
                $wrapper.find('select[name="' + field + '"]').each(function () {
                    controls_fields[field] = $(this).val();
                });
                $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                    if (!controls_fields[field]) {
                        controls_fields[field] = {};
                    }
                    controls_fields[field] = get_range_slider_values($(this));
                });
                $wrapper.find('.air-datepicker[name="' + field + '-from"]').each(function () {
                    if (!controls_fields[field]) {
                        controls_fields[field] = {};
                    }
                    controls_fields[field].from = $(this).data('unixtime') ? $(this).data('unixtime') : 0;
                });
                $wrapper.find('.air-datepicker[name="' + field + '-to"]').each(function () {
                    if (!controls_fields[field]) {
                        controls_fields[field] = {};
                    }
                    controls_fields[field].to = $(this).data('unixtime') ? $(this).data('unixtime') : 0;
                });
            }
            return controls_fields;
        }
        function get_range_slider_values($range_slider) {
            var from = parseFloat($range_slider.val().split(';')[0]);
            var to = parseFloat($range_slider.val().split(';')[1]);
            if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'k') {
                from = parseFloat($range_slider.val().split(';')[0]) * 1000;
                to = parseFloat($range_slider.val().split(';')[1]) * 1000;
            }
            if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'm') {
                from = parseFloat($range_slider.val().split(';')[0]) * 1000000;
                to = parseFloat($range_slider.val().split(';')[1]) * 1000000;
            }
            return {'from': from, 'to': to};
        }
        function set_range_slider_values($range_slider, values) {
            values.from = Math.floor(values.from);
            values.to = Math.ceil(values.to);
            if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'k') {
                values.from = Math.floor(values.from / 1000);
                values.to = Math.ceil(values.to / 1000);
            }
            if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'm') {
                values.from = Math.floor(values.from / 1000000);
                values.to = Math.ceil(values.to / 1000000);
            }
            $range_slider.data("ionRangeSlider").update(values);
        }
        function init_controls($wrapper, url_fields) {
            for (var field in azt.fields) {
                if (field in url_fields) {
                    $wrapper.find('input[type="checkbox"][name="' + field + '"][value="' + url_fields[field] + '"]').each(function () {
                        $(this).prop('checked', true);
                    });
                    $wrapper.find('input[type="radio"][name="' + field + '"][value="' + url_fields[field] + '"]').each(function () {
                        $(this).prop('checked', true);
                    });
                    $wrapper.find('select[name="' + field + '"]').each(function () {
                        $(this).val(url_fields[field]);
                    });
                    $wrapper.find('input[type="hidden"][name="' + field + '"]').each(function () {
                        $(this).val(url_fields[field]);
                    });
                } else {
                    var value = field_value(azt.table, field);
                    if (value) {
                        $wrapper.find('input[type="checkbox"][name="' + field + '"][value="' + value + '"]').each(function () {
                            $(this).prop('checked', true);
                        });
                        $wrapper.find('input[type="radio"][name="' + field + '"][value="' + value + '"]').each(function () {
                            $(this).prop('checked', true);
                        });
                        $wrapper.find('select[name="' + field + '"]').each(function () {
                            $(this).val(value);
                        });
                        $wrapper.find('input[type="hidden"][name="' + field + '"]').each(function () {
                            $(this).val(value);
                        });
                    }
                }
                $wrapper.find('.air-datepicker[name="' + field + '-from"]').each(function () {
                    var datepicker = $(this).data('datepicker');
                    if (datepicker) {
                        if (url_fields[field] && 'from' in url_fields[field]) {
                            var value = url_fields[field].from;
                        } else {
                            var value = field_min(azt.table, field);
                        }
                        $(this).data('unixtime', value);
                        datepicker.selectDate(new Date(value * 1000));
                    }
                });
                $wrapper.find('.air-datepicker[name="' + field + '-to"]').each(function () {
                    var datepicker = $(this).data('datepicker');
                    if (datepicker) {
                        if (url_fields[field] && 'to' in url_fields[field]) {
                            var value = url_fields[field].to;
                        } else {
                            var value = field_max(azt.table, field);
                        }
                        $(this).data('unixtime', value);
                        datepicker.selectDate(new Date(value * 1000));
                    }
                });
                $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                    var $this = $(this);
                    if ($this.data("ionRangeSlider")) {
                        $this.data("ionRangeSlider").reset();
                        if (url_fields[field]) {
                            var values = false;
                            if (typeof url_fields[field] === 'object') {
                                values = {
                                    from: url_fields[field].from,
                                    to: url_fields[field].to
                                };
                            } else {
                                values = {from: url_fields[field], to: url_fields[field]};
                            }
                            set_range_slider_values($this, values);
                        } else {
                            var values = {
                                from: parseFloat(field_min(azt.table, field)),
                                to: parseFloat(field_max(azt.table, field))
                            };
                            set_range_slider_values($this, values);
                        }
                    }
                });
            }
        }
        function refresh_controls($wrapper, $initiator) {
            for (var field in azt.fields) {
                $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').not($initiator).each(function () {
                    var $this = $(this);
                    if ($this.data("ionRangeSlider")) {
                        if (azt.current_rows.length) {
                            var values = get_range_slider_values($this);
                            if (values.from <= field_min(azt.current_rows, field)) {
                                values.from = field_min(azt.current_rows, field);
                            }
                            if (values.to >= field_max(azt.current_rows, field)) {
                                values.to = field_max(azt.current_rows, field);
                            }
                            set_range_slider_values($this, values);
                        } else {
                            $this.data("ionRangeSlider").update({
                                from: $this.data('min'),
                                to: $this.data('max')
                            });
                        }
                    }
                });
            }
        }
        function disable_controls($wrapper) {
            $wrapper.find('.az-clear-filters').off('click');
            for (var field in azt.fields) {
                $wrapper.find('input[type="checkbox"][name="' + field + '"]').each(function () {
                    $(this).closest('[data-element]').css('pointer-events', 'none');
                });
                $wrapper.find('input[type="radio"][name="' + field + '"]').each(function () {
                    $(this).closest('[data-element]').css('pointer-events', 'none');
                });
                $wrapper.find('select[name="' + field + '"]').each(function () {
                    $(this).closest('[data-element]').css('pointer-events', 'none');
                });
                $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                    $(this).closest('[data-element]').css('pointer-events', 'none');
                });
            }
        }
        function is_empty_request(fields1, fields2) {
            var intersect = $(Object.keys(fields1)).filter(Object.keys(fields2));
            var empty = false;
            $(intersect).each(function () {
                if (typeof fields2[this] === 'object') {
                    if ('from' in fields2[this] && 'to' in fields2[this]) {
                        if (parseFloat(fields1[this]) < parseFloat(fields2[this]['from']) || parseFloat(fields1[this]) > parseFloat(fields2[this]['to'])) {
                            empty = true;
                            return false;
                        }
                    } else {
                        if ('from' in fields2[this]) {
                            if (parseFloat(fields1[this]) < parseFloat(fields2[this]['from'])) {
                                empty = true;
                                return false;
                            }
                        }
                        if ('to' in fields2[this]) {
                            if (parseFloat(fields1[this]) > parseFloat(fields2[this]['to'])) {
                                empty = true;
                                return false;
                            }
                        }
                    }
                } else {
                    if (fields1[this] != fields2[this]) {
                        empty = true;
                        return false;
                    }
                }
            });
            return empty;
        }
        function refresh_template($wrapper, url_fields) {
            var controls_fields = get_controls_fields($wrapper);
            reset_template($wrapper);
            if (!('templates' in azt)) {
                azt.templates = $wrapper.find('[data-element][data-azt-key][data-azt-value]').sort(function (a, b) {
                    return $(b).parents().length - $(a).parents().length;
                });
            }
            azt.templates.each(function () {
                var $template = $(this);
                var current_fields = {};
                if (azt.fields[$template.attr('data-azt-key')]) {
                    current_fields[$template.attr('data-azt-key')] = $template.attr('data-azt-value');
                }
                $template.parents('[data-element][data-azt-key][data-azt-value]').each(function () {
                    var $this = $(this);
                    if (azt.fields[$this.attr('data-azt-key')]) {
                        current_fields[$this.attr('data-azt-key')] = $this.attr('data-azt-value');
                    }
                });
                if (is_empty_request(current_fields, controls_fields)) {
                    azt.current_row = false;
                    azt.current_rows = [];
                } else {
                    var merged_fields = $.extend({}, url_fields);
                    merged_fields = $.extend(merged_fields, controls_fields);
                    current_fields = $.extend(merged_fields, current_fields);
                    select_rows(current_fields);
                }
                fill_template($template, current_fields);
                if ($template.attr('data-id')) {
                    fill_template($body.find($template.attr('data-id')), current_fields);
                }
                $template.find('[data-id]').each(function () {
                    fill_template($body.find($(this).attr('data-id')), current_fields);
                });
            });
            var current_fields = $.extend({}, url_fields);
            current_fields = $.extend(current_fields, controls_fields);
            select_rows(current_fields);
            fill_template($wrapper, current_fields);
            refresh_maps($wrapper);
        }
        function get_url_fields() {
            var url_fields = {};
            for (var field in azt.fields) {
                if (field in $.QueryString) {
                    url_fields[field] = $.QueryString[field];
                }
                var field_from = field + '-from';
                var field_to = field + '-to';
                if (field_from in $.QueryString || field_to in $.QueryString) {
                    url_fields[field] = {};
                    if (field_from in $.QueryString) {
                        url_fields[field].from = $.QueryString[field_from];
                    }
                    if (field_to in $.QueryString) {
                        url_fields[field].to = $.QueryString[field_to];
                    }
                }
            }
            return url_fields;
        }
        function refresh_click_urls($wrapper) {
            var controls_fields = get_controls_fields($wrapper);
            var url_fields = get_url_fields();
            if (!('click_urls' in azt) || !azt.click_urls) {
                azt.click_urls = $wrapper.find('a[href][data-azt-key]:not([data-azt-key=""])[data-azt-value]:not([data-azt-value=""]), [data-click-url][data-azt-key]:not([data-azt-key=""])[data-azt-value]:not([data-azt-value=""])');
            }
            azt.click_urls.each(function () {
                var $this = $(this);
                var query = $.extend({}, url_fields);
                query = $.extend(query, controls_fields);
                query[$this.attr('data-azt-key')] = $this.attr('data-azt-value');
                var fields = [];
                for (var key in query) {
                    if (typeof query[key] === 'object') {
                        if ('from' in query[key]) {
                            fields.push(key + '-from=' + query[key].from);
                        }
                        if ('to' in query[key]) {
                            fields.push(key + '-to=' + query[key].to);
                        }
                    } else {
                        fields.push(key + '=' + query[key]);
                    }
                }
                if ($this.attr('href')) {
                    $this.attr('href', $this.attr('href').split('?')[0] + '?' + fields.join('&'));
                }
                if ($this.attr('data-click-url')) {
                    $this.attr('data-click-url', $this.attr('data-click-url').split('?')[0] + '?' + fields.join('&'));
                }
            });
            if (!('back_buttons' in azt)) {
                azt.back_buttons = $wrapper.find('.az-url-back-button');
            }
            azt.back_buttons.each(function () {
                var $this = $(this);
                var fields = [];
                $this.find('> [data-azt-key]').each(function () {
                    if ($(this).attr('data-azt-key') in $.QueryString || $(this).attr('data-azt-key') === '') {
                        fields.push($(this).attr('data-azt-key'));
                    }
                    $(this).hide();
                });
                var current_field = fields.pop();
                var prev_field = fields.pop();
                var query = $.extend({}, url_fields);
                query = $.extend(query, controls_fields);
                $this.find('> a[data-azt-key="' + prev_field + '"]').each(function () {
                    var $this = $(this);
                    var search = [];
                    for (var field in query) {
                        if (field != current_field) {
                            if (typeof query[field] === 'object') {
                                if ('from' in query[field]) {
                                    search.push(field + '-from=' + query[field].from);
                                }
                                if ('to' in query[field]) {
                                    search.push(field + '-to=' + query[field].to);
                                }
                            } else {
                                search.push(field + '=' + query[field]);
                            }
                        }
                    }
                    if (search.length) {
                        $this.attr('href', $this.attr('href').split('?')[0] + '?' + search.join('&'));
                    } else {
                        $this.attr('href', $this.attr('href').split('?')[0]);
                    }

                }).show();
            });
            if (!('breadcurmbs' in azt)) {
                azt.breadcurmbs = $wrapper.find('.az-url-breadcurmbs');
            }
            azt.breadcurmbs.each(function () {
                var $this = $(this);
                var fields = [];
                $this.find('> [data-azt-key]').each(function () {
                    if ($(this).attr('data-azt-key') in $.QueryString || $(this).attr('data-azt-key') === '') {
                        fields.push($(this).attr('data-azt-key'));
                    }
                    $(this).hide();
                });
                var query = $.extend({}, url_fields);
                query = $.extend(query, controls_fields);
                for (var current_field in $.QueryString) {
                    (function (current_field) {
                        $this.find('> a[data-azt-key="' + current_field + '"]').each(function () {
                            var $this = $(this);
                            var search = [];
                            for (var field in query) {
                                if (fields.indexOf(field) <= fields.indexOf(current_field)) {
                                    if (typeof query[field] === 'object') {
                                        if ('from' in query[field]) {
                                            search.push(field + '-from=' + query[field].from);
                                        }
                                        if ('to' in query[field]) {
                                            search.push(field + '-to=' + query[field].to);
                                        }
                                    } else {
                                        search.push(field + '=' + query[field]);
                                    }
                                }
                            }
                            if (search.length) {
                                $this.attr('href', $this.attr('href').split('?')[0] + '?' + search.join('&'));
                            } else {
                                $this.attr('href', $this.attr('href').split('?')[0]);
                            }
                        }).show();
                    })(current_field);
                }
            });
            if (!('field_up_downs' in azt)) {
                azt.field_up_downs = $wrapper.find('.az-url-field-up-down');
            }
            azt.field_up_downs.each(function (event) {
                var $wrapper = $(this);
                if ($wrapper.attr('data-azt-key') in $.QueryString) {
                    var query = $.extend({}, url_fields);
                    query = $.extend(query, controls_fields);
                    $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                    $wrapper.find('> [data-azt-value]').each(function () {
                        var $this = $(this);
                        var search = [];
                        for (var field in query) {
                            var value = query[field];
                            if (field == $wrapper.attr('data-azt-key')) {
                                if (value == $this.attr('data-azt-value')) {
                                    $this.addClass('az-active');
                                    $this.prev().addClass('az-up');
                                    $this.next().addClass('az-down');
                                } else {
                                    value = $this.attr('data-azt-value');
                                }
                            }
                            search.push(field + '=' + value);
                        }
                        var $a = $this.find('> a[href]');
                        $a.attr('href', $a.attr('href').split('?')[0] + '?' + search.join('&'));
                    });
                }
            });
        }
        var $wrapper = data.wrapper;
        $wrapper.find('.ion-range-slider').each(function () {
            var $this = $(this);
            $this.ionRangeSlider();
            $this.prev().addClass('az-empty-inner-html');
            $this.on('azh-change', function () {
                var $this = $(this);
                if ($this.data("ionRangeSlider")) {
                    $this.data("ionRangeSlider").destroy();
                    $this.removeData(['ionRangeSlider', 'skin', 'type', 'min', 'max', 'from', 'to', 'step', 'prefix', 'postfix', 'grid']);
                }
                $this.ionRangeSlider();
                $this.prev().addClass('az-empty-inner-html');
            });
            $this.on('azh-clone', function () {
                var $this = $(this);
                $this.prev().remove();
                $this.removeData(['ionRangeSlider', 'skin', 'type', 'min', 'max', 'from', 'to', 'step', 'prefix', 'postfix', 'grid']);
                $this.ionRangeSlider();
                $this.addClass('irs-hidden-input').prev().addClass('az-empty-inner-html');
            });
        });
        $.fn.air_datepicker.language['en'] = {
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            today: 'Today',
            clear: 'Clear',
            dateFormat: 'mm/dd/yyyy',
            timeFormat: 'hh:ii aa',
            firstDay: 0
        };
        $wrapper.find('.air-datepicker').each(function () {
            var $this = $(this);
            $this.air_datepicker({
                language: 'en',
                onSelect: function (fd, d, picker) {
                    if (d) {
                        var unixtime = d.getTime() / 1000;
                        $this.data('unixtime', unixtime);
                        //$this.trigger('change');
                        refresh_template($wrapper, get_url_fields());
                        refresh_click_urls($wrapper);
                    }
                }
            });
        });
        if (customize) {
            $wrapper.find('.az-url-back-button').each(function () {
                var $this = $(this);
                $this.find('> [data-azt-key]').hide();
                $this.find('> [data-azt-key]').first().show();
                $this.find('> [data-azt-key]').on('azh-active', function (event) {
                    $this.find('> [data-azt-key]').hide();
                    $(this).show();
                });
            });
            $wrapper.find('.az-url-breadcurmbs').each(function () {
                var $this = $(this);
                $this.find('> [data-azt-key]').show();
            });
            $wrapper.find('.az-url-field-up-down').each(function (event) {
                var $wrapper = $(this);
                $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                $wrapper.find('> [data-azt-value]').first().addClass('az-active').next().addClass('az-down');
                $wrapper.find('> [data-azt-value]').on('azh-active', function (event) {
                    var $this = $(this);
                    $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                    $this.addClass('az-active');
                    $this.prev().addClass('az-up');
                    $this.next().addClass('az-down');
                });
            });
        } else {
            if ('azt' in window && 'table' in azt) {
                var url_fields = get_url_fields();
                init_controls($wrapper, url_fields);
                select_rows(url_fields);
                if (azt.current_row) {
                    disable_controls($wrapper);
                }
                refresh_template($wrapper, url_fields);

                var input_throttle = _.throttle(function ($input) {
                    refresh_template($wrapper, url_fields);
                    refresh_click_urls($wrapper);
                }, 500);
                for (var field in azt.fields) {
                    $wrapper.find('input[type="checkbox"][name="' + field + '"]').on('change', function () {
                        refresh_template($wrapper, url_fields);
                        refresh_click_urls($wrapper);
                    });
                    $wrapper.find('input[type="radio"][name="' + field + '"]').on('change', function () {
                        refresh_template($wrapper, url_fields);
                        refresh_click_urls($wrapper);
                    });
                    $wrapper.find('select[name="' + field + '"]').on('change', function () {
                        refresh_template($wrapper, url_fields);
                        refresh_click_urls($wrapper);
                    });
                    $wrapper.find('input.ion-range-slider[name="' + field + '"]').on('change', function () {
                        //input_throttle($(this));
                        $window.off('mouseup.azh').one('mouseup.azh', function () {
                            refresh_template($wrapper, url_fields);
                            refresh_click_urls($wrapper);
                        });
                    });
                    $wrapper.find('.air-datepicker[name="' + field + '"]').on('change', function () {
                        refresh_template($wrapper, url_fields);
                        refresh_click_urls($wrapper);
                    });
                }
            }
            refresh_click_urls($wrapper);
            $wrapper.find('.az-clear-filters').on('click', function (event) {
                $wrapper.find('form').each(function () {
                    this.reset();
                });
                var reset_fields = {}; //url_fields
                init_controls($wrapper, reset_fields);
                select_rows(reset_fields);
                refresh_template($wrapper, reset_fields);
                refresh_click_urls($wrapper);
                return false;
            });
        }
    });
    $window.on('az-frontend-after-init', function (event, data) {
        refresh_maps(data.wrapper);
    });
})(window.jQuery);