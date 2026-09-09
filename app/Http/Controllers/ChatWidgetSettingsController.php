<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatWidgetSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Every setting the chat widget reads, with the value it falls back to
     * when the admin leaves the field blank.
     *
     * The connection fields default to empty on purpose. Until an API URL is
     * saved the widget refuses to pretend, it shows the offline message rather
     * than swallowing the visitor's question.
     */
    public static function defaults()
    {
        return [
            /* --- where the conversation actually runs ------------------- */
            'api_url' => '',
            'auth_header' => 'Authorization',
            'auth_value' => '',
            'message_field' => 'message',
            'session_field' => 'session_id',
            'reply_path' => '',
            'extra_payload' => '',
            'send_page_url' => '1',
            'timeout' => '20',

            /* --- what the visitor sees --------------------------------- */
            'status' => '0',
            'title' => 'Go4Database Support',
            'subtitle' => 'We usually reply within a minute',
            'greeting' => 'Hi there. Ask me anything about our databases, coverage or pricing.',
            'placeholder' => 'Type your message...',
            'launcher_label' => 'Chat with us',
            'offline_text' => "We can't reach the chat service right now. Please email info@go4database.com and we'll come straight back to you.",
            'color' => '#6fd943',
        ];
    }

    /** Saved value if there is one, otherwise the default above. */
    public static function value($key)
    {
        $saved = get_static_option('chat_widget_' . $key);

        return ($saved === null || $saved === '')
            ? (self::defaults()[$key] ?? '')
            : $saved;
    }

    /** Whether to render the launcher at all. */
    public static function is_live()
    {
        return self::value('status') === '1';
    }

    public function index()
    {
        return view('backend.pages.chat-widget-settings');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'chat_widget_api_url' => 'nullable|url|max:500',
            'chat_widget_auth_header' => 'nullable|string|max:100',
            'chat_widget_auth_value' => 'nullable|string|max:500',
            'chat_widget_message_field' => 'nullable|string|max:100',
            'chat_widget_session_field' => 'nullable|string|max:100',
            'chat_widget_reply_path' => 'nullable|string|max:190',
            'chat_widget_extra_payload' => 'nullable|string|max:2000',
            'chat_widget_timeout' => 'nullable|integer|min:3|max:120',
            'chat_widget_title' => 'nullable|string|max:100',
            'chat_widget_subtitle' => 'nullable|string|max:190',
            'chat_widget_greeting' => 'nullable|string|max:500',
            'chat_widget_placeholder' => 'nullable|string|max:100',
            'chat_widget_launcher_label' => 'nullable|string|max:100',
            'chat_widget_offline_text' => 'nullable|string|max:500',
            'chat_widget_color' => 'nullable|string|max:30',
        ]);

        // Extra payload is merged into every outgoing request, so it has to be
        // a JSON object. Reject anything else here rather than silently
        // dropping it at send time.
        $extra = trim((string) $request->chat_widget_extra_payload);
        if ($extra !== '') {
            $decoded = json_decode($extra, true);
            if (!is_array($decoded)) {
                return redirect()->back()->with([
                    'msg' => __('Extra Request Fields must be a valid JSON object, for example {"bot_id":"12"}'),
                    'type' => 'danger',
                ]);
            }
        }

        foreach (array_keys(self::defaults()) as $key) {
            if (in_array($key, ['status', 'send_page_url'], true)) {
                continue;
            }
            update_static_option('chat_widget_' . $key, $request->input('chat_widget_' . $key));
        }

        update_static_option('chat_widget_status', $request->has('chat_widget_status') ? '1' : '0');
        update_static_option('chat_widget_send_page_url', $request->has('chat_widget_send_page_url') ? '1' : '0');

        return redirect()->back()->with(['msg' => __('Chat Widget Settings Updated...'), 'type' => 'success']);
    }

    /**
     * Send one throwaway message to the configured API and report exactly what
     * came back. This is how you wire up a new chat backend without guessing:
     * paste the URL, hit Test, and read which field the reply arrived in.
     */
    public function test(Request $request)
    {
        $endpoint = trim((string) $request->input('api_url', self::value('api_url')));

        if ($endpoint === '' || !filter_var($endpoint, FILTER_VALIDATE_URL)) {
            return response()->json([
                'ok' => false,
                'summary' => __('Save a valid API URL first.'),
            ]);
        }

        $body = [
            self::value('message_field') => 'Hello, this is a connection test from the Go4Database website.',
            self::value('session_field') => 'test-' . uniqid(),
        ];

        if (self::value('send_page_url') === '1') {
            $body['page_url'] = url('/');
        }

        $extra = json_decode(self::value('extra_payload'), true);
        if (is_array($extra)) {
            $body = array_merge($body, $extra);
        }

        $headers = ['Accept' => 'application/json'];
        $auth = trim(self::value('auth_value'));
        if ($auth !== '') {
            $headers[self::value('auth_header') ?: 'Authorization'] = $auth;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout((int) self::value('timeout') ?: 20)
                ->asJson()
                ->post($endpoint, $body);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'summary' => __('Could not reach the API: ') . $e->getMessage(),
                'sent' => $body,
            ]);
        }

        $reply = (new ChatWidgetController)->extract_reply($response, self::value('reply_path'));

        return response()->json([
            'ok' => $response->successful() && $reply !== null,
            'summary' => $response->successful()
                ? ($reply !== null
                    ? __('Connected. The reply was read successfully.')
                    : __('The API answered, but we could not find the reply text. Set "Reply Field Path" to the field holding it.'))
                : __('The API answered with HTTP ') . $response->status(),
            'http_status' => $response->status(),
            'sent' => $body,
            'reply' => $reply,
            'raw' => mb_substr($response->body(), 0, 3000),
        ]);
    }
}
