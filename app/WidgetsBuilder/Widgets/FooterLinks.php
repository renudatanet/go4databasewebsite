<?php

namespace App\WidgetsBuilder\Widgets;

use App\Widgets;

class FooterLinks
{
    protected $args;
    protected $widget;

    public function __construct($args = [])
    {
        $this->args = $args;
        $this->widget = Widgets::find($args['id'] ?? null);
    }

    public function frontend_render()
    {
        if (empty($this->widget)) {
            return '';
        }

        $data = json_decode($this->widget->content);
        if (empty($data)) {
            return '';
        }

        $title = $data->title ?? '';
        $links = $data->links ?? [];

        if (empty($links)) {
            return '';
        }

        $output = "\t\t" . '<div>' . "\n";

        if (!empty($title)) {
            $output .= "\t\t\t" . '<div style="font-size:17px;font-weight:800;color:#111;margin-bottom:22px">'
                . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>' . "\n";
        }

        $output .= "\t\t\t" . '<div style="display:flex;flex-direction:column;gap:16px">' . "\n";

        foreach ($links as $link) {
            $label = $link->label ?? '';
            if (empty($label)) {
                continue;
            }
            $href = $link->url ?? '#';

            $output .= "\t\t\t\t" . '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8')
                . '" class="g4d-footlink" style="display:flex;align-items:center;gap:8px;color:#555;font-size:14.5px">&rsaquo; '
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>' . "\n";
        }

        $output .= "\t\t\t" . '</div>' . "\n";
        $output .= "\t\t" . '</div>' . "\n";

        return $output;
    }
}