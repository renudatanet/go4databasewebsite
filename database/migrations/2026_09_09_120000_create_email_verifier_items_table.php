<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEmailVerifierItemsTable extends Migration
{
    /**
     * Repeatable blocks on the Email Verifier page. One table, one row per
     * block, `section` says which strip of the page it belongs to.
     */
    public function up()
    {
        if (!Schema::hasTable('email_verifier_items')) {
            Schema::create('email_verifier_items', function (Blueprint $table) {
                $table->id();
                $table->string('section');              // trust | check | step | glossary
                $table->string('badge_key')->nullable(); // glossary rows map to a checker status
                $table->string('icon')->nullable();      // lucide icon name
                $table->string('title');
                $table->text('description')->nullable();
                $table->tinyInteger('is_highlight')->default(0);
                $table->integer('sr_order')->default(0);
                $table->string('lang')->nullable();
                $table->string('status')->default('publish');
                $table->timestamps();
            });
        }

        // Seed with the copy the page currently ships, so nothing changes visually
        // until someone edits it in the admin panel.
        if (DB::table('email_verifier_items')->count() > 0) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $now = now();
        $rows = [];
        $add = function ($section, $icon, $title, $description = null, $badge = null, $highlight = 0) use (&$rows, $lang, $now) {
            $rows[] = [
                'section' => $section,
                'badge_key' => $badge,
                'icon' => $icon,
                'title' => $title,
                'description' => $description,
                'is_highlight' => $highlight,
                'sr_order' => count($rows),
                'lang' => $lang,
                'status' => 'publish',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        $add('trust', 'mail-x', 'No email ever delivered');
        $add('trust', 'server', 'Live mail server check');
        $add('trust', 'shield-check', 'GDPR-aware');
        $add('trust', 'lock', 'SSL secured');

        $add('check', 'spell-check', 'Syntax validation', 'Catches typos and malformed addresses before they ever cost you a send.');
        $add('check', 'server', 'MX record lookup', 'Confirms the domain actually has mail servers configured to receive email.');
        $add('check', 'globe', 'Domain health', 'Verifies the domain resolves properly and is set up to accept mail.');
        $add('check', 'trash-2', 'Disposable detection', 'Flags throwaway inboxes built to disappear within minutes of signup.');
        $add('check', 'users', 'Role account detection', 'Identifies shared inboxes like info@ and support@ that skew engagement.');
        $add('check', 'at-sign', 'Free provider detection', 'Tells you when an address is personal rather than a company domain.');
        $add('check', 'layers', 'Catch-all detection', 'Spots domains that accept everything, so you know when a result is unconfirmed.');
        $add('check', 'plug-zap', 'Live mailbox check', 'Connects to the real mail server and asks whether that mailbox exists.');
        $add('check', 'shield-check', 'Bounce prediction', 'Combines every signal above into one clear verdict you can act on.');

        $add('glossary', 'check-circle-2', 'Valid', 'The mailbox exists and is safe to send to.', 'valid');
        $add('glossary', 'x-circle', 'Invalid', "The address doesn't exist, or the domain can't receive mail at all.", 'invalid');
        $add('glossary', 'layers', 'Catch-All', 'The domain accepts mail for any address, so this one mailbox stays unconfirmed.', 'catch_all');
        $add('glossary', 'trash-2', 'Disposable', 'A known temporary or throwaway email provider.', 'disposable');
        $add('glossary', 'users', 'Role-Based', 'A shared inbox like info@ or support@ rather than a named person.', 'role_based');
        $add('glossary', 'help-circle', 'Unknown', 'Format and domain look fine, but the mailbox itself could not be confirmed right now.', 'unknown');

        $add('step', null, 'Invalid emails bounce', 'Every bad address comes straight back as a hard bounce, a wasted send and a wasted opportunity.');
        $add('step', null, 'Bounces trip the filters', 'Mailbox providers watch your bounce rate closely. A high one marks you as a likely spammer.');
        $add('step', null, 'Reputation blocks delivery', 'Once your sender reputation drops, even your genuinely good emails stop reaching the inbox.');
        $add('step', null, 'Clean lists fix all three', 'Verified lists mean better delivery, higher open rates and a lower cost per real lead.', null, 1);

        DB::table('email_verifier_items')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('email_verifier_items');
    }
}
