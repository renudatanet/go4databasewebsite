<?php

namespace App\MenuBuilder;

use App\Helpers\LanguageHelper;
use App\Menu;
use DOMDocument;
use DOMXPath;

class MenuBuilderFrontendRender
{
    protected $page_id;

    public function render_frrontend_panel_menu($id)
    {
        $output = '';
        if (empty($id)) {
            return $output;
        }

        $menu_details_from_db = Menu::find($id);
        if (is_null($menu_details_from_db)) {
            return $output;
        }

        $default_lang = $menu_details_from_db->lang ?? LanguageHelper::default_slug();
        $menu_data = json_decode($menu_details_from_db->content);

        if (count((array)$menu_data) > 0) {
            $items_markup = '';
            $this->page_id = 1;
            foreach ($menu_data as $menu_item) {
                $this->page_id++;
                $items_markup .= $this->render_menu_item($menu_item, $this->page_id, $default_lang, false);
            }

            $output .= '<!-- Middle Centered Menu Links -->' . "\n";
            $output .= '<div class="g4d-nav" style="display:flex;align-items:center;gap:28px;position:absolute;left:50%;transform:translateX(-50%);overflow:visible">' . "\n";
            $output .= $items_markup;
            $output .= '</div>' . "\n";
        }

        return $output;
    }

    private function get_attribute_string(array $attributes): string
    {
        if (empty($attributes)) {
            return '';
        }

        $attr_val = '';
        foreach ($attributes as $attr => $value) {
            if ($attr === 'class') {
                $classes = is_array($value) ? implode(' ', array_filter($value)) : $value;
                if (!empty(trim($classes))) {
                    $attr_val .= ' class="' . trim($classes) . '"';
                }
            } elseif (!empty($value)) {
                $attr_val .= ' ' . $attr . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        return $attr_val;
    }

    private function render_menu_item($menu_item, int $page_id, $default_lang, bool $is_child = false)
    {
        if (empty((array)$menu_item)) {
            return '';
        }

        $menu_item = (object) $menu_item;
        $has_children = property_exists($menu_item, 'children') && !empty((array)$menu_item->children);

        $ptype = $menu_item->ptype ?? '';
        $pname = $menu_item->pname ?? '';
        $menutitle = $menu_item->menutitle ?? '';

        $title = $pname;
        $url = '#';

        // 1. MEGA MENU CHECK
        preg_match('/MegaMenus/', $ptype, $mega_matches);
        $is_mega_menu = !empty($mega_matches[0]);

        if ($is_mega_menu) {
            $class_name = '\\' . $ptype;
            if (!class_exists($class_name)) {
                return '';
            }

            $instance = new $class_name();
            if (!$instance->enable()) {
                return '';
            }

            $static_name = str_replace('[lang]', $default_lang, $instance->name());
            $db_title = htmlspecialchars(strip_tags(get_static_option($static_name)));
            $title = !empty($menutitle) ? $menutitle : $db_title;

            $slug = get_static_option($instance->slug());
            $url = $slug ? url('/') . '/' . $slug : '#';

            $anchor_attrs = [
                'href'   => $url,
                'class'  => ['nav-link-item'],
                'target' => $menu_item->antarget ?? '',
            ];

            // Render raw mega menu HTML from the class
            $raw_mega_menu_html = $instance->render($menu_item->items_id ?? '', $default_lang, [
                'sort' => $menu_item->mega_menu_order ?? '',
                'sort_by' => $menu_item->mega_menu_orderby ?? '',
                'category_status' => $menu_item->category_status ?? ''
            ]);

            // Convert to your exact modern wrapper structure
            $transformed_body = $this->transform_mega_menu_html($raw_mega_menu_html);

            $output = "\t" . '<div class="nav-dropdown-wrapper mega-dropdown-wrapper">' . "\n";
            $output .= $this->get_anchor_markup($title, $anchor_attrs, $menu_item->icon ?? null, true);
            $output .= "\t\t" . '<div class="nav-dropdown-menu mega-menu-dropdown">' . "\n";
            $output .= $transformed_body;
            $output .= "\t\t\t" . '<div class="dropdown-green-bar"></div>' . "\n";
            $output .= "\t\t" . '</div>' . "\n";
            $output .= "\t" . '</div>' . "\n";

            return $output;
        }

        // 2. CUSTOM LINK
        if ($ptype === 'custom') {
            $url = !empty($menu_item->purl) ? str_replace('@url', url('/'), $menu_item->purl) : '#';
            $title = !empty($menutitle) ? $menutitle : $pname;
        } 
        // 3. STATIC PAGE
        elseif ($ptype === 'static') {
            $menu_slug = get_static_option(str_replace('-', '_', $menu_item->pslug ?? '') . '_page_slug');
            $page_name = MenuBuilderSetup::multilang() ? '_page_' . $default_lang . '_name' : '_page_name';
            $static_title = get_static_option(str_replace('-', '_', $menu_item->pslug ?? '') . $page_name) ?? '';
            $title = !empty($menutitle) ? $menutitle : $static_title;
            $url = url('/') . '/' . ($menu_slug ?? '');
        } 
        // 4. DYNAMIC / ROUTED PAGES
        else {
            $menu_setup_instance = new MenuBuilderSetup();
            $all_dynamic_menus = $menu_setup_instance->register_dynamic_menus();
            $dynamic_menu_type = $all_dynamic_menus[$ptype] ?? null;

            if ($dynamic_menu_type) {
                $model_name = '\\' . $dynamic_menu_type['model'];
                $model = new $model_name();

                if ($dynamic_menu_type['query'] === 'old_lang') {
                    $item_details = $model->where(['lang' => $default_lang, 'id' => $menu_item->pid, 'status' => 'publish'])->first();
                } elseif ($dynamic_menu_type['query'] === 'new_lang') {
                    $item_details = $model->with(['lang_query' => function ($query) use ($default_lang) {
                        $query->where('lang', $default_lang);
                    }])->where(['id' => $menu_item->pid, 'status' => 'publish'])->first();
                } else {
                    $item_details = $model->where(['id' => $menu_item->pid, 'status' => 'publish'])->first();
                }

                if (empty($item_details)) {
                    return '';
                }

                $title_param = $dynamic_menu_type['title_param'];
                $db_title = ($dynamic_menu_type['query'] === 'new_lang')
                    ? ($item_details->lang_query->$title_param ?? '')
                    : ($item_details->$title_param ?? '');

                $title = !empty($menutitle) ? $menutitle : $db_title;

                $route_params = [];
                foreach ($dynamic_menu_type['route_params'] ?? [] as $param) {
                    $dynamic_param = ($dynamic_menu_type['query'] === 'new_lang')
                        ? ($item_details->lang_query->$param ?? '')
                        : ($item_details->$param ?? '');

                    $route_params[preg_match('/id/', $param) ? 'id' : $param] = $dynamic_param;
                }
                $url = route($dynamic_menu_type['route'], $route_params);
            }
        }

        // Anchor attributes
        $anchor_classes = $is_child ? ['simple-dropdown-item'] : ['nav-link-item'];
        $anchor_attrs = [
            'href'   => $url,
            'class'  => $anchor_classes,
            'target' => $menu_item->antarget ?? '',
        ];

        // Standard Dropdown or Single Link
        $output = '';
        if ($has_children) {
            $output .= "\t" . '<div class="nav-dropdown-wrapper">' . "\n";
            $output .= $this->get_anchor_markup($title, $anchor_attrs, $menu_item->icon ?? null, true);
            $output .= $this->render_children_item($menu_item->children, $default_lang);
            $output .= "\t" . '</div>' . "\n";
        } else {
            $output .= $this->get_anchor_markup($title, $anchor_attrs, $menu_item->icon ?? null, false);
        }

        return $output;
    }

    protected function render_children_item($children, $default_lang)
    {
        if (empty((array)$children)) {
            return '';
        }

        $output = "\t\t" . '<div class="nav-dropdown-menu">' . "\n";
        foreach ($children as $ch_item) {
            $this->page_id++;
            $output .= $this->render_menu_item($ch_item, $this->page_id, $default_lang, true);
        }
        $output .= "\t\t\t" . '<div class="dropdown-green-bar"></div>' . "\n";
        $output .= "\t\t" . '</div>' . "\n";

        return $output;
    }

    /**
     * Converts old Bootstrap mega-menu HTML into modern mega-menu-body markup.
     */
    protected function transform_mega_menu_html(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        // Load with UTF-8 encoding handling
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $columns = $xpath->query('//div[contains(@class, "xg-mega-menu-single-column-wrap")]');

        if ($columns->length === 0) {
            // Fallback if class structure differed
            return "\t\t\t" . '<div class="mega-menu-body">' . $html . '</div>' . "\n";
        }

        $output = "\t\t\t" . '<div class="mega-menu-body">' . "\n";

        // Max number of <li> items shown per mega-menu column.
        // Any links beyond this count are dropped (not shown).
        $items_per_ul = 5;

        foreach ($columns as $column) {
            $titleNode = $xpath->query('.//p[contains(@class, "mega-menu-title")]', $column)->item(0);
            $columnTitle = $titleNode ? trim($titleNode->textContent) : '';

            $output .= "\t\t\t\t" . '<div class="mega-menu-col">' . "\n";
            if (!empty($columnTitle)) {
                $output .= "\t\t\t\t\t" . '<h4 class="mega-col-title">' . htmlspecialchars($columnTitle) . '</h4>' . "\n";
            }

            $links = $xpath->query('.//ul/li/a', $column);
            $links_capped = array_slice(iterator_to_array($links), 0, $items_per_ul);

            $output .= "\t\t\t\t\t" . '<ul class="mega-col-list">' . "\n";

            foreach ($links_capped as $link) {
                $href = $link->getAttribute('href');
                $text = trim($link->textContent);
                $target = $link->getAttribute('target');
                $target_attr = !empty($target) ? ' target="' . htmlspecialchars($target) . '"' : '';

                $output .= "\t\t\t\t\t\t" . '<li><a href="' . htmlspecialchars($href) . '" class="mega-menu-item"' . $target_attr . '>' . htmlspecialchars($text) . '</a></li>' . "\n";
            }

            $output .= "\t\t\t\t\t" . '</ul>' . "\n";
            $output .= "\t\t\t\t" . '</div>' . "\n";
        }

        $output .= "\t\t\t" . '</div>' . "\n";

        return $output;
    }

    private function get_anchor_markup(string $title, array $args, $icon = null, bool $has_caret = false)
    {
        $icon_markup = $icon ? "<i class='" . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . "'></i> " : '';

        $caret_svg = '';
        if ($has_caret) {
            $caret_svg = "\n\t\t\t\t" . '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' .
                         '<path d="m6 9 6 6 6-6" />' .
                         '</svg>';
        }

        return "\t\t" . '<a' . $this->get_attribute_string($args) . '>' . $icon_markup . htmlspecialchars(strip_tags($title)) . $caret_svg . '</a>' . "\n";
    }
}
