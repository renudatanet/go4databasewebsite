<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Menu;

/**
 * Supersedes 2026_10_13_120000, which matched on the literal text
 * "Email Verifier" and so found nothing: that entry is a page reference
 * ({"ptype":"pages","pid":37}) whose label comes from the pages table, not
 * from the menu JSON.
 *
 * This matches the "Solutions" node by pname -- which IS stored literally --
 * and appends two custom links beside the existing child, so Email Finder and
 * Affiliate Program are reachable from the nav on every inner page, not just
 * the hardcoded homepage navbar.
 */
class AddSolutionsLinksToHeaderMenu extends Migration
{
    private $wanted = [
        ['label' => 'Email Finder',      'slug' => 'email-finder'],
        ['label' => 'Affiliate Program', 'slug' => 'affiliate'],
    ];

    /** Highest id anywhere in the tree, so new entries do not collide. */
    private function maxId($nodes, $max = 0)
    {
        foreach ((array) $nodes as $n) {
            $n = (array) $n;
            if (empty($n)) { continue; }
            if (isset($n['id']) && is_numeric($n['id'])) { $max = max($max, (int) $n['id']); }
            if (!empty($n['children'])) { $max = $this->maxId($n['children'], $max); }
        }
        return $max;
    }

    public function up()
    {
        if (!Schema::hasTable('menus')) { return; }

        foreach (Menu::all() as $menu) {
            $nodes = json_decode((string) $menu->content, true);
            if (!is_array($nodes) || empty($nodes)) { continue; }

            $changed = false;
            $nextId  = $this->maxId($nodes) + 1;

            foreach ($nodes as $i => $node) {
                if (!is_array($node) || strcasecmp(trim((string) ($node['pname'] ?? '')), 'Solutions') !== 0) {
                    continue;
                }

                $children = isset($node['children']) && is_array($node['children']) ? $node['children'] : [];

                // Labels already there, so a re-run adds nothing.
                $present = [];
                $lastReal = -1;
                foreach ($children as $ci => $c) {
                    $c = (array) $c;
                    if (empty($c)) { continue; }
                    $lastReal = $ci;
                    $present[] = mb_strtolower(trim((string) ($c['pname'] ?? '')));
                }

                $new = [];
                foreach ($this->wanted as $w) {
                    if (in_array(mb_strtolower($w['label']), $present, true)) { continue; }
                    $new[] = [
                        'ptype'     => 'custom',
                        'id'        => $nextId++,
                        'menutitle' => '',
                        'antarget'  => '',
                        'icon'      => '',
                        'pname'     => $w['label'],
                        // @url is swapped for the site root at render time, so
                        // this carries between environments.
                        'purl'      => '@url/' . $w['slug'],
                    ];
                }
                if (empty($new)) { continue; }

                // Sit them directly after the last real child, ahead of the
                // empty slots the builder leaves behind.
                array_splice($children, $lastReal + 1, 0, $new);
                $nodes[$i]['children'] = $children;
                $changed = true;
            }

            if ($changed) {
                $menu->content = json_encode($nodes);
                $menu->save();
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('menus')) { return; }

        $labels = [];
        foreach ($this->wanted as $w) { $labels[] = mb_strtolower($w['label']); }

        foreach (Menu::all() as $menu) {
            $nodes = json_decode((string) $menu->content, true);
            if (!is_array($nodes) || empty($nodes)) { continue; }

            $changed = false;
            foreach ($nodes as $i => $node) {
                if (!is_array($node) || empty($node['children']) || !is_array($node['children'])) { continue; }
                $kept = [];
                foreach ($node['children'] as $c) {
                    $ca = (array) $c;
                    if (!empty($ca) && in_array(mb_strtolower(trim((string) ($ca['pname'] ?? ''))), $labels, true)) {
                        $changed = true;
                        continue;
                    }
                    $kept[] = $c;
                }
                $nodes[$i]['children'] = $kept;
            }

            if ($changed) {
                $menu->content = json_encode($nodes);
                $menu->save();
            }
        }
    }
}
