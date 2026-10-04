{{-- Desktop notifications for new acquisition requests (opt-in via "Desktop alerts" on the queue page). --}}
@if (filament()->auth()->check())
    <script>
        (() => {
            if (window.__eliteAcquisitionAlerts || !('Notification' in window)) return;
            window.__eliteAcquisitionAlerts = true;

            const endpoint = @js(route('filament.admin.acquisitions.pulse'));
            const key = 'elite.acquisitions.latest';
            const read = () => { try { return Number(localStorage.getItem(key) || 0); } catch { return 0; } };
            const write = (id) => { try { localStorage.setItem(key, String(id)); } catch {} };

            const poll = async () => {
                if (document.visibilityState === 'hidden' && Notification.permission !== 'granted') return;

                try {
                    const since = read();
                    const response = await fetch(`${endpoint}?since=${since}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    if (!response.ok) return;
                    const data = await response.json();

                    // Shared across tabs via localStorage, so each request alerts once.
                    if (data.latest > read()) write(data.latest);

                    if (since > 0 && Notification.permission === 'granted') {
                        data.requests.forEach((request) => {
                            const notification = new Notification(request.title, {
                                body: request.body,
                                icon: '/images/logo/elite-mark.svg',
                                tag: `elite-acquisition-${request.id}`,
                            });
                            notification.onclick = () => { window.focus(); window.location.href = request.url; };
                        });
                    }
                } catch {
                    // Offline or session expired: try again next tick.
                }
            };

            poll();
            setInterval(poll, 30000);
        })();
    </script>
@endif
