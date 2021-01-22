<?php
/*
  ReduxFramework Config File
 */

if (!class_exists('AZEXO_Redux_Framework')) {

    class AZEXO_Redux_Framework {

        public $args = array();
        public $sections = array();
        public $theme;
        public static $redux_framework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            add_action('init', array($this, 'initSettings'), 11); // after woocommerce.php
        }

        public function initSettings() {
            $this->theme = wp_get_theme();
            $this->setArguments();
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }
            add_action('redux/loaded', array($this, 'remove_demo'));
            add_filter('redux/options/' . $this->args['opt_name'] . '/args', array($this, 'change_arguments'));
            AZEXO_Redux_Framework::$redux_framework = new ReduxFramework($this->sections, $this->args);
        }

        function change_arguments($args) {
            $args['dev_mode'] = false;

            return $args;
        }

        function remove_demo() {
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            ob_start();

            $ct = wp_get_theme();
            $this->theme = $ct;
            $item_name = $this->theme->get('Name');
            $tags = $this->theme->Tags;
            $screenshot = $this->theme->get_screenshot();
            $class = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(esc_html__('Customize &#8220;%s&#8221;', 'mynx'), $this->theme->display('Name'));
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
                <?php if ($screenshot) : ?>
                    <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview', 'mynx'); ?>" />
                        </a>
                    <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview', 'mynx'); ?>" />
                <?php endif; ?>

                <h4><?php print esc_html($this->theme->display('Name')); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(esc_html__('By %s', 'mynx'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(esc_html__('Version %s', 'mynx'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . esc_html__('Tags', 'mynx') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php print esc_html($this->theme->display('Description')); ?></p>
                    <?php
                    if ($this->theme->parent()) {
                        printf(' <p class="howto">' . wp_kses(__('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'mynx'), array('a' => array('href' => array()))) . '</p>', esc_html__('http://codex.wordpress.org/Child_Themes', 'mynx'), $this->theme->parent()->display('Name'));
                    }
                    ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $options = get_option(AZEXO_FRAMEWORK);
            $azexo_templates = azexo_get_templates();

            global $azexo_fields;
            if (!isset($azexo_fields)) {
                $azexo_fields = array();
            }
            global $azexo_post_fields;
            $azexo_post_fields = array(
                'post_title' => esc_html__('Post title', 'mynx'),
                'post_summary' => esc_html__('Post summary', 'mynx'),
                'post_content' => esc_html__('Post content', 'mynx'),
                'post_thumbnail' => esc_html__('Post thumbnail', 'mynx'),
                'post_video' => esc_html__('Post video', 'mynx'),
                'post_gallery' => esc_html__('Post gallery', 'mynx'),
                'post_sticky' => esc_html__('Post sticky', 'mynx'),
                'post_date' => esc_html__('Post date', 'mynx'),
                'post_splitted_date' => esc_html__('Post splitted date', 'mynx'),
                'post_author' => esc_html__('Post author', 'mynx'),
                'post_author_avatar' => esc_html__('Post author avatar', 'mynx'),
                'post_category' => esc_html__('Post category', 'mynx'),
                'post_tags' => esc_html__('Post tags', 'mynx'),
                'post_like' => esc_html__('Post like', 'mynx'),
                'post_last_comment' => esc_html__('Post last comment', 'mynx'),
                'post_last_comment_author' => esc_html__('Post last comment author', 'mynx'),
                'post_last_comment_author_avatar' => esc_html__('Post last comment author avatar', 'mynx'),
                'post_last_comment_date' => esc_html__('Post last comment date', 'mynx'),
                'post_comments_count' => esc_html__('Post comments count', 'mynx'),
                'post_read_more' => esc_html__('Post read more link', 'mynx'),
                'post_share' => esc_html__('Post social share', 'mynx'),
                'post_comments' => esc_html__('Post comments', 'mynx'),
                'post_navigation' => esc_html__('Post navigation', 'mynx'),
            );

            $azexo_fields = array_merge($azexo_fields, $azexo_post_fields);


            $field_templates = azexo_get_field_templates();
            $azexo_fields = array_merge($azexo_fields, $field_templates);


            $taxonomy_fields = array();
            $taxonomies = get_taxonomies(array(), 'objects');
            foreach ($taxonomies as $slug => $taxonomy) {
                $taxonomy_fields['taxonomy_' . $slug] = esc_html__('Taxonomy: ', 'mynx') . $taxonomy->label;
            }
            $azexo_fields = array_merge($azexo_fields, $taxonomy_fields);

            $meta_fields = array();
            if (isset($options['meta_fields']) && is_array($options['meta_fields'])) {
                $options['meta_fields'] = array_filter($options['meta_fields']);
                if (!empty($options['meta_fields'])) {
                    $meta_fields = array_combine($options['meta_fields'], $options['meta_fields']);
                }
            }
            $azexo_fields = array_merge($azexo_fields, $meta_fields);


            $azexo_fields = apply_filters('azexo_fields', $azexo_fields);

            $vc_widgets = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'vc_widget',
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            if (is_array($vc_widgets)) {
                foreach ($vc_widgets as $vc_widget) {
                    $azexo_fields[$vc_widget->ID] = $vc_widget->post_title . ' ' . esc_html__('VC Widget', 'mynx');
                }
            }
            $azh_widgets = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'azh_widget',
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            if (is_array($azh_widgets)) {
                foreach ($azh_widgets as $azh_widget) {
                    $azexo_fields[$azh_widget->ID] = $azh_widget->post_title . ' ' . esc_html__('AZH Widget', 'mynx');
                }
            }
            $pages = get_posts(array(
                'numberposts' => -1,
                'post_type' => 'page',
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            $pages_options = array();
            if (is_array($pages)) {
                foreach ($pages as $page) {
                    $pages_options[$page->ID] = $page->post_title;
                }
            }


            $general_settings_fields = array();
            $general_settings_fields[] = array(
                'id' => 'framework_confirmation',
                'type' => 'checkbox',
                'title' => esc_html__('Framework use confirmation', 'mynx'),
                'description' => esc_html__('Almost all settings (including VC elements) in admin-interface of theme used as part of development framework - it allow to make HTML/JS output with high flexibility. It is complex to make CSS theme styles to cover such a big freedom fully. Many possible settings/configurations which not provided with demo content must be used with LESS/CSS sources editing.', 'mynx'),
                'default' => '0'
            );
            $general_settings_fields[] = array(
                'id' => 'logo',
                'type' => 'media',
                'title' => esc_html__('Logo', 'mynx'),
                'subtitle' => esc_html__('Upload any media using the WordPress native uploader', 'mynx'),
                'required' => array('header', 'contains', 'logo')
            );
            if (class_exists('WPLessPlugin')) {
                $general_settings_fields[] = array(
                    'id' => 'brand-color',
                    'type' => 'color',
                    'title' => esc_html__('Brand color', 'mynx'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'accent-1-color',
                    'type' => 'color',
                    'title' => esc_html__('Accent 1 color', 'mynx'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'accent-2-color',
                    'type' => 'color',
                    'title' => esc_html__('Accent 2 color', 'mynx'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'header-color',
                    'type' => 'color',
                    'title' => esc_html__('Header color', 'mynx'),
                    'validate' => 'color',
                    'default' => '#000',
                );
                $general_settings_fields[] = array(
                    'id' => 'paragraph-color',
                    'type' => 'color',
                    'title' => esc_html__('Paragraph color', 'mynx'),
                    'validate' => 'color',
                    'default' => '#000',
                );
            }
            global $azh_google_fonts;
            $general_settings_fields[] = array(
                'id' => 'google_font_families',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => esc_html__('Google font families', 'mynx'),
                'options' => array_combine($azh_google_fonts, $azh_google_fonts),
            );
            if (class_exists('Infinite_Scroll')) {
                $general_settings_fields[] = array(
                    'id' => 'infinite_scroll',
                    'type' => 'checkbox',
                    'title' => esc_html__('Infinite Scroll', 'mynx'),
                    'default' => '0'
                );
            }
            $general_settings_fields[] = array(
                'id' => 'single_post_template',
                'type' => 'select',
                'title' => esc_html__('Single blog template', 'mynx'),
                'options' => $azexo_templates,
                'default' => 'post',
            );
            $general_settings_fields[] = array(
                'id' => 'default_post_template',
                'type' => 'select',
                'title' => esc_html__('Default blog template', 'mynx'),
                'options' => $azexo_templates,
                'default' => 'post',
            );
            $general_settings_fields[] = array(
                'id' => 'show_sidebar',
                'type' => 'select',
                'title' => esc_html__('Show sidebar', 'mynx'),
                'options' => array(
                    'hidden' => esc_html__('Hidden', 'mynx'),
                    'left' => esc_html__('Left side', 'mynx'),
                    'right' => esc_html__('Right side', 'mynx'),
                ),
                'default' => 'right',
            );
            $general_settings_fields[] = array(
                'id' => 'favicon',
                'type' => 'media',
                'title' => esc_html__('Favicon', 'mynx'),
                'subtitle' => esc_html__('Upload any media using the WordPress native uploader', 'mynx'),
            );
            $general_settings_fields[] = array(
                'id' => 'custom-js',
                'type' => 'ace_editor',
                'title' => esc_html__('JS Code', 'mynx'),
                'subtitle' => esc_html__('Paste your JS code here.', 'mynx'),
                'mode' => 'javascript',
                'theme' => 'chrome',
                'default' => "jQuery(document).ready(function(){\n\n});"
            );

            $general_settings_fields[] = array(
                'id' => 'custom_dashboard_pages',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => esc_html__('Custom dashboard pages', 'mynx'),
                'options' => $pages_options,
            );
            $general_settings_fields[] = array(
                'id' => 'disable_credits',
                'type' => 'checkbox',
                'title' => esc_html__('Disable AZEXO credits', 'AZEXO'),
                'default' => '0'
            );            

            $skins = function_exists('azexo_get_skins') ? azexo_get_skins() : array(isset($options['skin']) ? $options['skin'] : get_template());

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('General settings', 'mynx'),
                'fields' => $general_settings_fields
            );

            $this->sections[] = array(
                'type' => 'divide',
            );

            $post_types = get_post_types(array('_builtin' => false, 'publicly_queryable' => true), 'objects');
            if (is_array($post_types) && !empty($post_types)) {
                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => esc_html__('Post types settings', 'mynx'),
                );
                foreach ($post_types as $slug => $post_type) {
                    $fields = array(
                        array(
                            'id' => 'single_' . $slug . '_template',
                            'type' => 'select',
                            'title' => esc_html__('Single template', 'mynx'),
                            'options' => $azexo_templates,
                            'default' => $slug,
                        ),
                        array(
                            'id' => 'default_' . $slug . '_template',
                            'type' => 'select',
                            'title' => esc_html__('Default template', 'mynx'),
                            'options' => $azexo_templates,
                            'default' => $slug,
                        ),
                        array(
                            'id' => $slug . '_show_sidebar',
                            'type' => 'select',
                            'title' => esc_html__('Show sidebar', 'mynx'),
                            'options' => array(
                                'hidden' => esc_html__('Hidden', 'mynx'),
                                'left' => esc_html__('Left side', 'mynx'),
                                'right' => esc_html__('Right side', 'mynx'),
                            ),
                            'default' => 'hidden',
                        ),
                        array(
                            'id' => $slug . '_additional_sidebar',
                            'type' => 'select',
                            'multi' => true,
                            'title' => esc_html__('Show additional sidebar', 'mynx'),
                            'options' => array(
                                'single' => esc_html__('Single page', 'mynx'),
                                'list' => esc_html__('List page', 'mynx'),
                            ),
                            'default' => '',
                        ),
                    );
                    if ($slug != 'product') {
                        $fields = array_merge($fields, array(array(
                                'id' => $slug . '_before_list',
                                'type' => 'select',
                                'multi' => true,
                                'sortable' => true,
                                'title' => esc_html__('Before posts list', 'mynx'),
                                'options' => array(
                                    'result_count' => esc_html__('Result count', 'mynx'),
                                    'ordering' => esc_html__('Ordering', 'mynx'),
                                ),
                                'default' => array(),
                            ),
                            array(
                                'id' => $slug . '_custom_sorting',
                                'type' => 'select',
                                'multi' => true,
                                'sortable' => true,
                                'title' => esc_html__('Custom sorting', 'mynx'),
                                'options' => array(
                                    'menu_order' => esc_html__('Default sorting', 'mynx'),
                                    'date' => esc_html__('Sort by newness', 'mynx'),
                                ),
                                'default' => array('menu_order', 'date'),
                            ),
                            array(
                                'id' => $slug . '_custom_sorting_numeric_meta_keys',
                                'type' => 'multi_text',
                                'title' => esc_html__('Custom sorting numeric meta keys', 'mynx'),
                            ),
                        ));
                    }
                    $this->sections[] = array(
                        'icon' => 'el-icon-cogs',
                        'subsection' => true,
                        'title' => $post_type->label,
                        'fields' => $fields
                    );
                }
            }

            $header_parts = array(
                'logo' => esc_html__('Logo', 'mynx'),
                'search' => esc_html__('Search', 'mynx'),
                'primary_menu' => esc_html__('Primary menu', 'mynx'),
                'secondary_menu' => esc_html__('Secondary menu', 'mynx'),
                'mobile_menu_button' => esc_html__('Mobile menu button', 'mynx'),
                'mobile_menu' => esc_html__('Mobile menu', 'mynx'),
            );
            $files = scandir(get_template_directory() . '/template-parts');
            if (is_array($files)) {
                foreach ($files as $file) {
                    $matches = array();
                    if (preg_match('/header\-([\w\-]+)\.php/', $file, $matches)) {
                        $header_parts[$matches[1]] = $matches[0];
                    }
                }
            }

            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('Templates configuration', 'mynx'),
                'fields' => array(
                    array(
                        'id' => 'skin',
                        'type' => 'select',
                        'title' => esc_html__('Select skin', 'mynx'),
                        'options' => array_combine($skins, $skins),
                    ),
                    array(
                        'id' => 'header_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Header sidebar fullwidth', 'mynx'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'header_parts_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Header parts fullwidth', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'middle_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Middle sidebar fullwidth', 'mynx'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'content_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Content fullwidth', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'footer_sidebar_fullwidth',
                        'type' => 'checkbox',
                        'title' => esc_html__('Footer sidebar fullwidth', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'show_page_title',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show page title in templates', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'show_breadcrumbs',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show breadcrumb in templates', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'header',
                        'type' => 'select',
                        'multi' => true,
                        'sortable' => true,
                        'title' => esc_html__('Header parts', 'mynx'),
                        'options' => $header_parts,
                        'default' => array(),
                    ),
                    array(
                        'id' => 'author_bio',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show author bio in templates', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'post_navigation',
                        'type' => 'select',
                        'title' => esc_html__('Post navigation place', 'mynx'),
                        'options' => array(
                            'hidden' => esc_html__('Hidden', 'mynx'),
                            'before' => esc_html__('Before content', 'mynx'),
                            'after' => esc_html__('After content', 'mynx'),
                        ),
                        'default' => 'hidden',
                    ),
                    array(
                        'id' => 'post_navigation_full',
                        'type' => 'checkbox',
                        'title' => esc_html__('Post navigation full template', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'post_navigation_previous',
                        'type' => 'text',
                        'title' => esc_html__('Post navigation previous text', 'mynx'),
                        'default' => '',
                    ),
                    array(
                        'id' => 'post_navigation_next',
                        'type' => 'text',
                        'title' => esc_html__('Post navigation next text', 'mynx'),
                        'default' => '',
                    ),
                    array(
                        'id' => 'related_posts',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show related posts', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'comments',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show comments in templates', 'mynx'),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'comment_likes',
                        'type' => 'checkbox',
                        'title' => esc_html__('Show likes in comment', 'mynx'),
                        'default' => '0'
                    ),
                    array(
                        'id' => 'default_title',
                        'type' => 'text',
                        'title' => esc_html__('Default page title', 'mynx'),
                        'default' => 'Latest posts',
                    ),
                    array(
                        'id' => 'post_page_title',
                        'type' => 'select',
                        'title' => esc_html__('Post page title', 'mynx'),
                        'options' => $azexo_fields,
                        'default' => '',
                    ),
                    array(
                        'id' => 'strip_excerpt',
                        'type' => 'checkbox',
                        'title' => esc_html__('Strip excerpt', 'mynx'),
                        'default' => '1',
                    ),
                    array(
                        'id' => 'excerpt_length',
                        'type' => 'text',
                        'title' => esc_html__('Excerpt length', 'mynx'),
                        'default' => '15',
                    ),
                    array(
                        'id' => 'comment_excerpt_length',
                        'type' => 'text',
                        'title' => esc_html__('Comment excerpt length', 'mynx'),
                        'default' => '15',
                    ),
                    array(
                        'id' => 'author_avatar_size',
                        'type' => 'text',
                        'title' => esc_html__('Author avatar size', 'mynx'),
                        'default' => '100',
                    ),
                    array(
                        'id' => 'avatar_size',
                        'type' => 'text',
                        'title' => esc_html__('Avatar size', 'mynx'),
                        'default' => '60',
                    ),
                    array(
                        'id' => 'related_posts_carousel_margin',
                        'type' => 'text',
                        'title' => esc_html__('Related posts carousel margin', 'mynx'),
                        'default' => '0',
                    ),
                    array(
                        'id' => 'before_list_place',
                        'type' => 'select',
                        'title' => esc_html__('Before posts place', 'mynx'),
                        'options' => array(
                            'inside_content_area' => esc_html__('Inside content area', 'mynx'),
                            'before_container' => esc_html__('Before container', 'mynx'),
                        ),
                        'default' => 'inside_content_area',
                    ),
                    array(
                        'id' => 'templates',
                        'type' => 'multi_text',
                        'title' => esc_html__('Templates', 'mynx'),
                    ),
                    array(
                        'id' => 'meta_fields',
                        'type' => 'multi_text',
                        'title' => esc_html__('Meta fields', 'mynx'),
                    ),
                )
            );

            foreach ($azexo_templates as $template_slug => $template_name) {


                $places = array(
                    $template_slug . '_thumbnail' => esc_html__('Thumbnail DIV', 'mynx'),
                    $template_slug . '_hover' => esc_html__('Thumbnail hover DIV', 'mynx'),
                    $template_slug . '_extra' => esc_html__('Header extra DIV', 'mynx'),
                    $template_slug . '_meta' => esc_html__('Header meta DIV', 'mynx'),
                    $template_slug . '_header' => esc_html__('Header DIV', 'mynx'),
                    $template_slug . '_footer' => esc_html__('Footer DIV', 'mynx'),
                    $template_slug . '_data' => esc_html__('Data DIV', 'mynx'),
                    $template_slug . '_additions' => esc_html__('Additions DIV', 'mynx'),
                    $template_slug . '_next' => esc_html__('Append next', 'mynx'),
                );
                $post_fields = array();
                foreach ($places as $id => $name) {
                    $post_fields[] = array(
                        'id' => $id,
                        'type' => 'select',
                        'multi' => true,
                        'sortable' => true,
                        'title' => $name,
                        'options' => $azexo_fields
                    );
                }

                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => $template_name,
                    'subsection' => true,
                    'fields' => array_merge(array(
                        array(
                            'id' => $template_slug . '_show_thumbnail',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show thumbnail/gallery/video', 'mynx'),
                            'default' => '1'
                        ),
                        array(
                            'id' => $template_slug . '_image_thumbnail',
                            'type' => 'checkbox',
                            'title' => esc_html__('Only image thumbnail (no gallery/video)', 'mynx'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_gallery_slider_thumbnails',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show gallery slider thumbnails', 'mynx'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_gallery_slider_thumbnails_vertical',
                            'type' => 'checkbox',
                            'title' => esc_html__('Vertical gallery slider thumbnails', 'mynx'),
                            'default' => '0',
                            'required' => array($template_slug . '_gallery_slider_thumbnails', 'equals', '1'),
                        ),
                        array(
                            'id' => $template_slug . '_zoom',
                            'type' => 'checkbox',
                            'title' => esc_html__('Zoom image on mouse hover', 'mynx'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_lazy',
                            'type' => 'checkbox',
                            'title' => esc_html__('Lazy load images', 'mynx'),
                            'default' => '0',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_show_carousel',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show gallery as carousel', 'mynx'),
                            'default' => '0',
                            'required' => array(
                                array($template_slug . '_show_thumbnail', 'equals', '1'),
                                array($template_slug . '_image_thumbnail', '!=', '1')
                            )
                        ),
                        array(
                            'id' => $template_slug . '_thumbnail_size',
                            'type' => 'text',
                            'title' => esc_html__('Thumbnail size', 'mynx'),
                            'default' => 'large',
                            'required' => array($template_slug . '_show_thumbnail', 'equals', '1')
                        ),
                        array(
                            'id' => $template_slug . '_show_title',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show title', 'mynx'),
                            'default' => '1'
                        ),
                        array(
                            'id' => $template_slug . '_show_content',
                            'type' => 'select',
                            'title' => esc_html__('Show content/excerpt', 'mynx'),
                            'options' => array(
                                'hidden' => esc_html__('Hidden', 'mynx'),
                                'content' => esc_html__('Show content', 'mynx'),
                                'excerpt' => esc_html__('Show excerpt', 'mynx'),
                            ),
                            'default' => 'content',
                        ),
                        array(
                            'id' => $template_slug . '_excerpt_length',
                            'type' => 'text',
                            'title' => esc_html__('Excerpt length', 'mynx'),
                            'default' => '15',
                            'required' => array($template_slug . '_show_content', 'equals', 'excerpt')
                        ),
                        array(
                            'id' => $template_slug . '_excerpt_words_trim',
                            'type' => 'checkbox',
                            'title' => esc_html__('Excerpt trim by words', 'mynx'),
                            'default' => '1',
                            'required' => array($template_slug . '_show_content', 'equals', 'excerpt')
                        ),
                        array(
                            'id' => $template_slug . '_more_inside_content',
                            'type' => 'checkbox',
                            'title' => esc_html__('Show more link inside content', 'mynx'),
                            'default' => '1',
                            'required' => array($template_slug . '_show_content', 'equals', 'content')
                        ),
                            ), $post_fields)
                );
            }

            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('Fields configuration', 'mynx'),
                'fields' => array()
            );

            foreach ($azexo_fields as $field_slug => $field_name) {
                $fields = array();
                if (isset($taxonomy_fields[$field_slug]) || isset($meta_fields[$field_slug])) {
                    $fields[] = array(
                        'id' => str_replace('.php', '', $field_slug) . '_image',
                        'type' => 'media',
                        'title' => esc_html__('Image', 'mynx'),
                        'default' => '',
                    );
                    $fields[] = array(
                        'id' => str_replace('.php', '', $field_slug) . '_hide_empty',
                        'type' => 'checkbox',
                        'title' => esc_html__('Hide empty', 'mynx'),
                        'default' => '0',
                    );
                }
                $fields[] = array(
                    'id' => str_replace('.php', '', $field_slug) . '_prefix',
                    'type' => 'textarea',
                    'title' => esc_html__('Prefix', 'mynx'),
                    'default' => '',
                );
                if (isset($taxonomy_fields[$field_slug]) || isset($meta_fields[$field_slug])) {
                    $fields[] = array(
                        'id' => str_replace('.php', '', $field_slug) . '_suffix',
                        'type' => 'textarea',
                        'title' => esc_html__('Suffix', 'mynx'),
                        'default' => '',
                    );
                }
                $this->sections[] = array(
                    'icon' => 'el-icon-cogs',
                    'title' => $field_name,
                    'subsection' => true,
                    'fields' => $fields,
                );
            }

            $this->sections = apply_filters('azexo_settings_sections', $this->sections);

            $this->sections[] = array(
                'type' => 'divide',
            );

            $theme_info = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . wp_kses(__('<strong>Theme URL:</strong> ', 'mynx'), array('strong' => array())) . '<a href="' . esc_url($this->theme->get('ThemeURI')) . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . wp_kses(__('<strong>Author:</strong> ', 'mynx'), array('strong' => array())) . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . wp_kses(__('<strong>Version:</strong> ', 'mynx'), array('strong' => array())) . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . wp_kses(__('<strong>Tags:</strong> ', 'mynx'), array('strong' => array())) . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';

            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'mynx'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'mynx'),
                'icon' => 'el-icon-refresh',
                'fields' => array(
                    array(
                        'id' => 'import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'icon' => 'el-icon-info-sign',
                'title' => esc_html__('Theme Information', 'mynx'),
                'fields' => array(
                    array(
                        'id' => 'raw-info',
                        'type' => 'raw',
                        'content' => $item_info,
                    )
                ),
            );
        }

        public static function getArguments() {
            return array(
                'opt_name' => AZEXO_FRAMEWORK,
                'page_slug' => '_options',
                'page_title' => 'AZEXO Options',
                'update_notice' => true,
                'admin_bar' => false,
                'menu_type' => 'menu',
                'menu_title' => 'AZEXO Options',
                'allow_sub_menu' => true,
                'page_parent_post_type' => 'your_post_type',
                'customizer' => true,
                'default_mark' => '*',
                'hints' =>
                array(
                    'icon' => 'el-icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color' => 'lightgray',
                    'icon_size' => 'normal',
                    'tip_style' =>
                    array(
                        'color' => 'light',
                    ),
                    'tip_position' =>
                    array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect' =>
                    array(
                        'show' =>
                        array(
                            'duration' => '500',
                            'event' => 'mouseover',
                        ),
                        'hide' =>
                        array(
                            'duration' => '500',
                            'event' => 'mouseleave unfocus',
                        ),
                    ),
                ),
                'output' => true,
                'output_tag' => true,
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => true,
                'show_import_export' => true,
                'transient_time' => '3600',
                'network_sites' => true,
            );
        }

        public function setArguments() {

            $this->args = AZEXO_Redux_Framework::getArguments();

            $theme = wp_get_theme();
            $this->args["display_name"] = $theme->get("Name");
            $this->args["display_version"] = $theme->get("Version");
        }

    }

    global $azexo_redux_config;
    $azexo_redux_config = new AZEXO_Redux_Framework();
}

add_action('update_option_' . AZEXO_FRAMEWORK, 'update_azexo_options', 10, 3);

function update_azexo_options($old_value, $value, $option) {
    if (isset($value['favicon']) && !empty($value['favicon']) && isset($value['favicon']['id']) && !empty($value['favicon']['id'])) {
        update_option('site_icon', $value['favicon']['id']);
    }
}
