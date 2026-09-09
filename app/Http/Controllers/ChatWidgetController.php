<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Server-side bridge between the chat bubble on this site and the chat system
 * running on a separate server.
 *
 * The browser never talks to that server directly. It posts here, and this
 * forwards the message on. Doing it this way means there is no cross-origin
 * problem to solve, the API key never reaches the page source, and the chat
 * backend can sit behind plain HTTP without the browser refusing to load it.
 */
class ChatWidgetController extends Controller
{
    /**
     * Where the reply text usually lives, tried in order when the admin has
     * not named the field explicitly. Covers the shapes returned by most
     * hand-rolled chat endpoints as well as OpenAI-style responses.
     */
    private const REPLY_CANDIDATES = [
        'reply', 'message', 'response', 'answer', 'text', 'output', 'content',
        'data.reply', 'data.message', 'data.response', 'data.answer', 'data.text',
        'result.reply', 'result.message', 'result.response', 'result.answer',
        'choices.0.message.content',
    ];

    /** Field names a chat backend might use to hand back its own session id. */
    private const SESSION_CANDIDATES = [
        'session_id', 'sessionId', 'session', 'conversation_id', 'conversationId',
        'chat_id', 'chatId', 'thread_id', 'threadId',
        'data.session_id', 'data.sessionId', 'data.conversation_id', 'data.chat_id',
    ];

    public function send(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:120',
            'page_url' => 'nullable|string|max:500',
        ]);

        if (!ChatWidgetSettingsController::is_live()) {
            return $this->offline('The chat widget is switched off in the admin panel.');
        }

        $endpoint = trim(ChatWidgetSettingsController::value('api_url'));
        if ($endpoint === '') {
            return $this->offline('No chat API URL has been saved yet.');
        }

        $body = [
            ChatWidgetSettingsController::value('message_field') => $request->input('message'),
            ChatWidgetSettingsController::value('session_field') => $request->input('session_id'),
        ];

        if (ChatWidgetSettingsController::value('send_page_url') === '1') {
            $body['page_url'] = $request->input('page_url');
        }

        $extra = json_decode(ChatWidgetSettingsController::value('extra_payload'), true);
        if (is_array($extra)) {
            $body = array_merge($body, $extra);
        }

        $headers = ['Accept' => 'application/json'];
        $auth = trim(ChatWidgetSettingsController::value('auth_value'));
        if ($auth !== '') {
            $headers[ChatWidgetSettingsController::value('auth_header') ?: 'Authorization'] = $auth;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout((int) ChatWidgetSettingsController::value('timeout') ?: 20)
                ->asJson()
                ->post($endpoint, $body);
        } catch (\Throwable $e) {
            Log::warning('Chat widget could not reach ' . $endpoint . ' : ' . $e->getMessage());

            return $this->offline('Could not reach the chat API.');
        }

        if ($response->failed()) {
            Log::warning('Chat widget got HTTP ' . $response->status() . ' from ' . $endpoint . ' : ' . mb_substr($response->body(), 0, 500));

            return $this->offline('Chat API returned HTTP ' . $response->status());
        }

        $reply = $this->extract_reply($response, ChatWidgetSettingsController::value('reply_path'));

        if ($reply === null) {
            Log::warning('Chat widget could not find a reply in the response from ' . $endpoint . ' : ' . mb_substr($response->body(), 0, 500));

            return $this->offline('The chat API answered in a shape we could not read.');
        }

        return response()->json([
            'ok' => true,
            'reply' => $reply,
            'session_id' => $this->extract_session($response) ?: $request->input('session_id'),
        ]);
    }

    /**
     * Pull the reply text out of whatever the chat backend sent back.
     * Public because the admin "Test connection" button reuses it.
     */
    public function extract_reply($response, $path = '')
    {
        $json = $response->json();

        if (is_string($json) && trim($json) !== '') {
            return $json;
        }

        if (is_array($json)) {
            $path = trim((string) $path);
            $candidates = $path !== '' ? [$path] : self::REPLY_CANDIDATES;

            foreach ($candidates as $candidate) {
                $found = data_get($json, $candidate);
                if (is_string($found) && trim($found) !== '') {
                    return $found;
                }
            }

            return null;
        }

        // Not JSON at all. Some endpoints simply return the reply as plain text.
        $raw = trim($response->body());

        return ($raw !== '' && mb_strlen($raw) <= 5000) ? $raw : null;
    }

    private function extract_session($response)
    {
        $json = $response->json();

        if (!is_array($json)) {
            return null;
        }

        foreach (self::SESSION_CANDIDATES as $candidate) {
            $found = data_get($json, $candidate);
            if ((is_string($found) || is_int($found)) && (string) $found !== '') {
                return (string) $found;
            }
        }

        return null;
    }

    /**
     * One shape for every failure. The visitor always sees the admin-written
     * offline message, the real cause goes to the log and to `reason`, which
     * only shows while the site is in debug mode.
     */
    private function offline($reason)
    {
        return response()->json([
            'ok' => false,
            'reply' => ChatWidgetSettingsController::value('offline_text'),
            'reason' => config('app.debug') ? $reason : null,
        ]);
    }
}
