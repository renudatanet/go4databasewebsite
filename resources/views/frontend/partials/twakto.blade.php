@php $twak__api = get_static_option('tawk_api_key'); @endphp
@if(!empty($twak__api))
   {{-- The snippet from General Settings > Third-party Scripts > Tawk.to API,
        started only once the page has loaded and the visitor scrolls, taps or
        types (or 4 seconds pass). Run straight away, its ~220KB of script was
        working while the browser drew its first frame, and on PageSpeed's
        phone test that frame was then dropped and the page stayed blank for
        another 1-2 seconds. Scripts inside a <template> don't run, so each one
        is copied into a fresh <script> when it's time. --}}
   <template id="g4-tawk">{!! $twak__api !!}</template>
   <script>
   (function () {
     var tpl = document.getElementById('g4-tawk');
     if (!tpl || !tpl.content) { return; }
     var started = false, events = ['scroll', 'pointerdown', 'keydown', 'touchstart'];
     function start() {
       if (started) { return; }
       started = true;
       events.forEach(function (e) { removeEventListener(e, start, true); });
       tpl.content.querySelectorAll('script').forEach(function (old) {
         var s = document.createElement('script');
         Array.prototype.forEach.call(old.attributes, function (a) { s.setAttribute(a.name, a.value); });
         s.text = old.textContent;
         document.body.appendChild(s);
       });
     }
     function arm() {
       events.forEach(function (e) { addEventListener(e, start, {capture: true, passive: true, once: true}); });
       setTimeout(start, 4000);
     }
     if (document.readyState === 'complete') { arm(); } else { addEventListener('load', arm); }
   })();
   </script>
@endif
