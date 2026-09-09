@if(\App\Http\Controllers\ChatWidgetSettingsController::is_live())
@php
    $g4chat = fn($k) => \App\Http\Controllers\ChatWidgetSettingsController::value($k);
@endphp

@push('styles')
<style>
/* ==========================================================================
   Floating chat launcher + panel. Styles are inlined rather than shipped as a
   separate stylesheet on purpose: the live server's assets folder is a real
   directory instead of a link to Laravel's, so a separate file would have to be
   copied across by hand on every deploy and would silently fall out of step.
   ========================================================================== */
.g4chat{
  --accent:{{ $g4chat('color') ?: '#6fd943' }};
  --accent-ink:#0b132a;
  --ink:#0b132a;
  --tx:#0c1420;
  --tx-2:#5b6675;
  --line:#e7ecf1;
  --bot-bg:#f4f7f9;
  position:fixed;
  right:24px;
  bottom:24px;
  z-index:9999;
  font-family:inherit;
  line-height:1.5;
}
.g4chat *{box-sizing:border-box;}

/* --- launcher ------------------------------------------------------------ */
.g4chat-launcher{
  position:relative;
  display:flex;
  align-items:center;
  justify-content:center;
  width:60px;
  height:60px;
  margin-left:auto;
  padding:0;
  border:none;
  border-radius:50%;
  cursor:pointer;
  background:var(--accent);
  color:var(--accent-ink);
  box-shadow:0 10px 26px rgba(11,19,42,.22), 0 2px 6px rgba(11,19,42,.12);
  transition:transform .18s ease, box-shadow .18s ease;
}
.g4chat-launcher:hover{
  transform:translateY(-3px);
  box-shadow:0 16px 34px rgba(11,19,42,.26), 0 3px 8px rgba(11,19,42,.14);
}
.g4chat-launcher:focus{outline:none;}
.g4chat-launcher:focus-visible{outline:3px solid var(--accent);outline-offset:3px;}
.g4chat-launcher svg{width:27px;height:27px;display:block;transition:opacity .16s ease, transform .16s ease;}
.g4chat-launcher .g4chat-ico-close{position:absolute;opacity:0;transform:rotate(-45deg) scale(.7);}
.g4chat.is-open .g4chat-launcher .g4chat-ico-chat{opacity:0;transform:rotate(45deg) scale(.7);}
.g4chat.is-open .g4chat-launcher .g4chat-ico-close{opacity:1;transform:rotate(0) scale(1);}

/* Soft halo, drawn behind the button so it never intercepts the click. */
.g4chat-launcher::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:50%;
  background:var(--accent);
  opacity:.42;
  z-index:-1;
  animation:g4chat-pulse 2.6s ease-out infinite;
}
.g4chat.is-open .g4chat-launcher::before,
.g4chat.is-touched .g4chat-launcher::before{animation:none;opacity:0;}
@keyframes g4chat-pulse{
  0%{transform:scale(1);opacity:.42;}
  70%{transform:scale(1.5);opacity:0;}
  100%{transform:scale(1.5);opacity:0;}
}

/* Little label that slides out on hover, desktop only. */
.g4chat-tip{
  position:absolute;
  right:72px;
  top:50%;
  transform:translateY(-50%) translateX(6px);
  white-space:nowrap;
  background:var(--ink);
  color:#fff;
  font-size:13px;
  font-weight:600;
  padding:8px 13px;
  border-radius:8px;
  opacity:0;
  pointer-events:none;
  transition:opacity .16s ease, transform .16s ease;
  box-shadow:0 6px 18px rgba(11,19,42,.2);
}
.g4chat-launcher:hover .g4chat-tip{opacity:1;transform:translateY(-50%) translateX(0);}
.g4chat.is-open .g4chat-tip{display:none;}

/* --- panel --------------------------------------------------------------- */
.g4chat-panel{
  position:absolute;
  right:0;
  bottom:76px;
  width:376px;
  max-height:min(600px, calc(100vh - 130px));
  display:flex;
  flex-direction:column;
  overflow:hidden;
  background:#fff;
  border:1px solid var(--line);
  border-radius:18px;
  box-shadow:0 24px 60px rgba(11,19,42,.24), 0 4px 12px rgba(11,19,42,.1);
  opacity:0;
  visibility:hidden;
  transform:translateY(14px) scale(.97);
  transform-origin:bottom right;
  transition:opacity .2s ease, transform .2s ease, visibility .2s;
}
.g4chat.is-open .g4chat-panel{opacity:1;visibility:visible;transform:translateY(0) scale(1);}

.g4chat-head{
  display:flex;
  align-items:center;
  gap:12px;
  padding:16px 18px;
  background:var(--ink);
  color:#fff;
}
.g4chat-avatar{
  flex:none;
  width:38px;
  height:38px;
  border-radius:50%;
  background:var(--accent);
  color:var(--accent-ink);
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:800;
  font-size:15px;
}
.g4chat-head-tx{min-width:0;flex:1;}
.g4chat-title{margin:0;font-size:15px;font-weight:700;color:#fff;line-height:1.3;}
.g4chat-sub{
  margin:2px 0 0;
  font-size:12.5px;
  color:rgba(255,255,255,.66);
  display:flex;
  align-items:center;
  gap:6px;
  line-height:1.3;
}
.g4chat-dot{width:7px;height:7px;border-radius:50%;background:var(--accent);flex:none;}
.g4chat-close{
  flex:none;
  width:30px;
  height:30px;
  padding:0;
  border:none;
  border-radius:8px;
  background:rgba(255,255,255,.1);
  color:#fff;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:background .15s ease;
}
.g4chat-close:hover{background:rgba(255,255,255,.2);}
.g4chat-close:focus{outline:none;}
.g4chat-close:focus-visible{outline:2px solid var(--accent);outline-offset:2px;}
.g4chat-close svg{width:15px;height:15px;}

.g4chat-log{
  flex:1;
  min-height:180px;
  overflow-y:auto;
  overscroll-behavior:contain;
  padding:18px;
  display:flex;
  flex-direction:column;
  gap:12px;
  background:#fff;
}
.g4chat-log::-webkit-scrollbar{width:6px;}
.g4chat-log::-webkit-scrollbar-thumb{background:#d8dfe6;border-radius:3px;}

.g4chat-msg{
  max-width:82%;
  padding:11px 14px;
  border-radius:14px;
  font-size:14px;
  color:var(--tx);
  white-space:pre-wrap;
  overflow-wrap:anywhere;
  animation:g4chat-in .22s ease both;
}
@keyframes g4chat-in{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}
.g4chat-msg.bot{align-self:flex-start;background:var(--bot-bg);border-bottom-left-radius:5px;}
.g4chat-msg.me{align-self:flex-end;background:var(--accent);color:var(--accent-ink);font-weight:500;border-bottom-right-radius:5px;}
.g4chat-msg.err{align-self:flex-start;background:#fef4f4;border:1px solid #f8d3d3;color:#8f2020;border-bottom-left-radius:5px;}
.g4chat-msg a{color:inherit;text-decoration:underline;}

.g4chat-typing{align-self:flex-start;display:flex;gap:4px;padding:13px 14px;background:var(--bot-bg);border-radius:14px;border-bottom-left-radius:5px;}
.g4chat-typing span{width:6px;height:6px;border-radius:50%;background:#9aa5b1;animation:g4chat-bounce 1.3s infinite;}
.g4chat-typing span:nth-child(2){animation-delay:.16s;}
.g4chat-typing span:nth-child(3){animation-delay:.32s;}
@keyframes g4chat-bounce{0%,60%,100%{transform:translateY(0);opacity:.45;}30%{transform:translateY(-4px);opacity:1;}}

.g4chat-foot{
  display:flex;
  align-items:flex-end;
  gap:9px;
  padding:12px;
  border-top:1px solid var(--line);
  background:#fff;
}
.g4chat-input{
  flex:1;
  resize:none;
  max-height:104px;
  min-height:42px;
  padding:11px 14px;
  border:1px solid var(--line);
  border-radius:11px;
  font-family:inherit;
  font-size:14px;
  color:var(--tx);
  background:#fff;
  transition:border-color .15s ease, box-shadow .15s ease;
}
.g4chat-input::placeholder{color:#9aa5b1;}
.g4chat-input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(111,217,67,.18);}
.g4chat-send{
  flex:none;
  width:42px;
  height:42px;
  padding:0;
  border:none;
  border-radius:11px;
  background:var(--accent);
  color:var(--accent-ink);
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:opacity .15s ease, transform .15s ease;
}
.g4chat-send:hover:not(:disabled){transform:translateY(-1px);}
.g4chat-send:disabled{opacity:.4;cursor:not-allowed;}
.g4chat-send:focus{outline:none;}
.g4chat-send:focus-visible{outline:3px solid var(--accent);outline-offset:2px;}
.g4chat-send svg{width:18px;height:18px;}

@media (max-width:520px){
  .g4chat{right:16px;bottom:16px;}
  .g4chat-panel{
    position:fixed;
    right:0;
    left:0;
    bottom:0;
    width:100%;
    max-height:82vh;
    border-radius:18px 18px 0 0;
    transform-origin:bottom center;
  }
  .g4chat-tip{display:none;}
  /* The sheet fills the width, so the launcher would sit on top of the send
     button. Hide it while open, the header's close button takes over. */
  .g4chat.is-open .g4chat-launcher{opacity:0;pointer-events:none;transform:scale(.6);}
}

@media (prefers-reduced-motion:reduce){
  .g4chat *,
  .g4chat *::before{animation:none !important;transition-duration:.01ms !important;}
}
</style>
@endpush

<div class="g4chat" id="g4chat" data-endpoint="{{ route('frontend.chat.send') }}" data-token="{{ csrf_token() }}">

    <div class="g4chat-panel" id="g4chat-panel" role="dialog" aria-modal="false"
         aria-labelledby="g4chat-title" aria-hidden="true">

        <div class="g4chat-head">
            <div class="g4chat-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($g4chat('title'), 0, 1)) }}</div>
            <div class="g4chat-head-tx">
                <p class="g4chat-title" id="g4chat-title">{{ $g4chat('title') }}</p>
                <p class="g4chat-sub"><span class="g4chat-dot" aria-hidden="true"></span>{{ $g4chat('subtitle') }}</p>
            </div>
            <button type="button" class="g4chat-close" id="g4chat-close" aria-label="{{ __('Close chat') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="g4chat-log" id="g4chat-log" role="log" aria-live="polite"></div>

        <form class="g4chat-foot" id="g4chat-form">
            <textarea class="g4chat-input" id="g4chat-input" rows="1" maxlength="2000"
                      placeholder="{{ $g4chat('placeholder') }}" aria-label="{{ __('Your message') }}"></textarea>
            <button type="submit" class="g4chat-send" id="g4chat-send" aria-label="{{ __('Send message') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </form>
    </div>

    <button type="button" class="g4chat-launcher" id="g4chat-launcher"
            aria-expanded="false" aria-controls="g4chat-panel" aria-label="{{ $g4chat('launcher_label') }}">
        <svg class="g4chat-ico-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
        </svg>
        <svg class="g4chat-ico-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
        <span class="g4chat-tip">{{ $g4chat('launcher_label') }}</span>
    </button>
</div>

<script>
(function () {
    var root = document.getElementById('g4chat');
    if (!root) { return; }

    var panel    = document.getElementById('g4chat-panel'),
        launcher = document.getElementById('g4chat-launcher'),
        closeBtn = document.getElementById('g4chat-close'),
        log      = document.getElementById('g4chat-log'),
        form     = document.getElementById('g4chat-form'),
        input    = document.getElementById('g4chat-input'),
        sendBtn  = document.getElementById('g4chat-send');

    var ENDPOINT = root.dataset.endpoint,
        TOKEN    = root.dataset.token,
        GREETING = @json($g4chat('greeting')),
        OFFLINE  = @json($g4chat('offline_text')),
        SID_KEY  = 'g4chat_sid',
        LOG_KEY  = 'g4chat_log',
        LOG_MAX  = 40;

    var busy = false;

    /* --- storage helpers, all of it optional ----------------------------- */
    function store(key, val) {
        try { val === null ? localStorage.removeItem(key) : localStorage.setItem(key, val); } catch (e) {}
    }
    function recall(key) {
        try { return localStorage.getItem(key); } catch (e) { return null; }
    }

    /* The conversation itself lives on the chat server. This id is only how we
       tell it which conversation a message belongs to. */
    function sessionId() {
        var id = recall(SID_KEY);
        if (!id) {
            id = (window.crypto && crypto.randomUUID)
                ? crypto.randomUUID()
                : 'g4-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
            store(SID_KEY, id);
        }
        return id;
    }

    function saveTranscript() {
        var items = [].slice.call(log.querySelectorAll('.g4chat-msg')).slice(-LOG_MAX).map(function (el) {
            return { who: el.classList.contains('me') ? 'me' : (el.classList.contains('err') ? 'err' : 'bot'), tx: el.textContent };
        });
        try { store(LOG_KEY, JSON.stringify(items)); } catch (e) {}
    }

    function bubble(who, text, skipSave) {
        var el = document.createElement('div');
        el.className = 'g4chat-msg ' + who;
        el.textContent = text;
        log.appendChild(el);
        log.scrollTop = log.scrollHeight;
        if (!skipSave) { saveTranscript(); }
        return el;
    }

    function restore() {
        var saved = null;
        try { saved = JSON.parse(recall(LOG_KEY) || 'null'); } catch (e) {}

        if (saved && saved.length) {
            saved.forEach(function (m) { bubble(m.who, m.tx, true); });
        } else if (GREETING) {
            bubble('bot', GREETING);
        }
    }

    /* --- open / close ---------------------------------------------------- */
    function open() {
        root.classList.add('is-open', 'is-touched');
        panel.setAttribute('aria-hidden', 'false');
        launcher.setAttribute('aria-expanded', 'true');
        log.scrollTop = log.scrollHeight;
        setTimeout(function () { input.focus(); }, 220);
    }
    function close() {
        root.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
        launcher.setAttribute('aria-expanded', 'false');
    }

    launcher.addEventListener('click', function () {
        root.classList.contains('is-open') ? close() : open();
    });
    closeBtn.addEventListener('click', function () { close(); launcher.focus(); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.classList.contains('is-open')) { close(); launcher.focus(); }
    });

    /* --- composing ------------------------------------------------------- */
    function autoGrow() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 104) + 'px';
    }
    input.addEventListener('input', autoGrow);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit ? form.requestSubmit() : send(); }
    });

    form.addEventListener('submit', function (e) { e.preventDefault(); send(); });

    function send() {
        var text = input.value.trim();
        if (!text || busy) { return; }

        bubble('me', text);
        input.value = '';
        autoGrow();

        busy = true;
        sendBtn.disabled = true;

        var typing = document.createElement('div');
        typing.className = 'g4chat-typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        log.appendChild(typing);
        log.scrollTop = log.scrollHeight;

        fetch(ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                message: text,
                session_id: sessionId(),
                page_url: window.location.href
            })
        })
        .then(function (r) { return r.json().catch(function () { return { ok: false, reply: OFFLINE }; }); })
        .then(function (data) {
            typing.remove();

            // The chat server may hand back its own id for this conversation.
            // Adopt it so every later message lands in the same thread there.
            if (data && data.session_id) { store(SID_KEY, data.session_id); }

            bubble(data && data.ok ? 'bot' : 'err', (data && data.reply) || OFFLINE);
        })
        .catch(function () {
            typing.remove();
            bubble('err', OFFLINE);
        })
        .finally(function () {
            busy = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    restore();
})();
</script>
@endif
