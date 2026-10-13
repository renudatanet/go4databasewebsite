<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Menu;

/**
 * The homepage navbar is hardcoded and carries all three Solutions links, but
 * every other page renders from the Menu Builder, where only Email Verifier
 * exists -- so /email-finder and /affiliate were unreachable from the nav on
 * roughly 500 pages, soon 5500.
 *
 * Rather than assume a shape, this locates the existing "Email Verifier" entry
 * wherever it sits and adds the two siblings beside it, cloned from it so they
 * carry every field the admin screen expects. A menu without that entry is
 * left untouched.
 */
class AddSolutionsItemsToPrimaryMenu extends Migration
{
    private $wanted = [
        ['label' => 'Email Finder',      'slug' => 'email-finder'],
        ['label' => 'Affiliate Program', 'slug' => 'affiliate'],
    ];

    private function labelOf(array $node)
    {
        $t = trim((string) ($node['menutitle'] ?? ''));
        return $t !== '' ? $t : trim((string) ($node['pname'] ?? ''));
    }

    /** Path of array keys to the node carrying $label, or null. */
    private function findPath(array $nodes, $label, array $trail = [])
    {
        foreach ($nodes as $i => $node) {
            if (!is_array($node)) { continue; }
            if (strcasecmp($this->labelOf($node), $label) === 0) {
                return array_merge($trail, [$i]);
            }
            if (!empty($node['children']) && is_array($node['children'])) {
                $hit = $this->findPath($node['children'], $label, array_merge($trail, [$i, 'children']));
                if ($hit !== null) { return $hit; }
            }
        }
        return null;
    }

    private function buildFrom(array $template, $label, $slug)
    {
        $item = $template;
        $item['ptype']     = 'custom';
        $item['pname']     = $label;
        $item['menutitle'] = $label;
        $item['pslug']     = $slug;
        $item['children']  = [];

        $existing = (string) ($template['purl'] ?? '');
        $item['purl'] = ($existing !== '' && strpos($existing, 'email-verifier') !== false)
            ? str_replace('email-verifier', $slug, $existing)   // keep whatever form the menu already uses
            : '@url/' . $slug;

        foreach (['items_id', 'mega_menu_order', 'mega_menu_orderby', 'category_status'] as $k) {
            if (array_key_exists($k, $item)) { $item[$k] = ''; }
        }
        return $item;
    }

    public function up()
    {
        if (!Schema::hasTable('menus')) { return; }

        foreach (Menu::all() as $menu) {
            $nodes = json_decode((string) $menu->content, true);
            if (!is_array($nodes) || empty($nodes)) { continue; }

            $path = $this->findPath($nodes, 'Email Verifier');
            if ($path === null) { continue; }

            $leafIndex = array_pop($path);              // the Email Verifier slot
            $siblings  = &$this->refTo($nodes, $path);  // the array holding it
            if (!is_array($siblings)) { unset($siblings); continue; }

            $template = $siblings[$leafIndex];
            $present  = [];
            foreach ($siblings as $n) {
                if (is_array($n)) { $present[] = mb_strtolower($this->labelOf($n)); }
            }

            $added = false;
            foreach ($this->wanted as $w) {
                if (in_array(mb_strtolower($w['label']), $present, true)) { continue; }
                $siblings[] = $this->buildFrom($template, $w['label'], $w['slug']);
                $added = true;
            }
            unset($siblings);

            if ($added) {
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

            $path = $this->findPath($nodes, 'Email Verifier');
            if ($path === null) { continue; }

            array_pop($path);
            $siblings = &$this->refTo($nodes, $path);
            if (!is_array($siblings)) { unset($siblings); continue; }

            $kept = [];
            foreach ($siblings as $n) {
                if (is_array($n) && in_array(mb_strtolower($this->labelOf($n)), $labels, true)) { continue; }
                $kept[] = $n;
            }
            $changed = count($kept) !== count($siblings);
            if ($changed) { $siblings = $kept; }
            unset($siblings);

            if ($changed) {
                $menu->content = json_encode($nodes);
                $menu->save();
            }
        }
    }

    /** Reference to the nested array at $path. */
    private function &refTo(array &$nodes, array $path)
    {
        $ref = &$nodes;
        foreach ($path as $key) {
            $ref = &$ref[$key];
        }
        return $ref;
    }
}
