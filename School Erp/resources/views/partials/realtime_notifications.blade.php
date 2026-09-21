<!-- Firebase Web Push SDK -->
<script defer src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

<script>
/**
 * SchoolCloud ERP Real-Time Notification & Firebase Background Push Engine
 */
(function() {
    const FIREBASE_CONFIG = {
        projectId: 'erp-1-23074',
        messagingSenderId: '628420405085',
        appId: '1:628420405085:web:schoolerp'
    };
    const VAPID_KEY = 'BE40TJMbrT9egACxo9MEllPfrMK8Qa12dy8KnvBzOJM7SorOd521v7yrw4VbSYHxssJNMZQ6N61wkz8b61bRhI';

    let evtSource = null;
    let fallbackInterval = null;
    let knownNotifIds = new Set();
    let isInitialLoad = true;
    let swRegistration = null;

    // 1. Initialize Firebase Cloud Messaging for Background Lock-screen Push
    function initFirebaseMessaging() {
        if (!('serviceWorker' in navigator)) {
            console.debug('ServiceWorker not supported');
            return;
        }

        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(reg => {
                swRegistration = reg;

                if (typeof firebase !== 'undefined') {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(FIREBASE_CONFIG);
                        }
                        const messaging = firebase.messaging();

                        // Request permission and fetch real FCM Device Token from Google
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                messaging.getToken({
                                    vapidKey: VAPID_KEY,
                                    serviceWorkerRegistration: reg
                                }).then(currentToken => {
                                    if (currentToken) {
                                        sendTokenToServer(currentToken);
                                    }
                                }).catch(err => {
                                    console.debug('Error retrieving FCM token:', err);
                                    fallbackLocalRegistration();
                                });
                            } else {
                                fallbackLocalRegistration();
                            }
                        });

                        // Foreground message listener
                        messaging.onMessage(payload => {
                            const item = {
                                id: Date.now(),
                                title: payload.notification?.title || payload.data?.title || 'School Notification',
                                message: payload.notification?.body || payload.data?.body || '',
                                action_url: payload.data?.action_url || '/',
                                color: '#1d4ed8',
                                icon: 'fa-bell',
                                time: 'Just now'
                            };
                            playNotificationChime();
                            showFloatingPushBanner(item);
                        });
                    } catch (e) {
                        console.debug('Firebase initialization fallback:', e);
                        fallbackLocalRegistration();
                    }
                }
            })
            .catch(err => {
                console.debug('SW Registration failed:', err);
                fallbackLocalRegistration();
            });
    }

    function sendTokenToServer(token) {
        const isAndroid = /android/i.test(navigator.userAgent);
        const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
        const platform = isAndroid ? 'android' : (isIOS ? 'ios' : 'web');
        const deviceName = isAndroid ? 'Android Phone' : (isIOS ? 'iPhone Device' : 'Desktop Browser');

        fetch("{{ route('notifications.register-device') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                token: token,
                platform: platform,
                device_name: deviceName,
                school_id: {{ auth()->check() ? (auth()->user()->school_id ?? 'null') : 'null' }}
            })
        }).catch(e => console.debug('Token save error:', e));
    }

    function fallbackLocalRegistration() {
        let deviceToken = localStorage.getItem('school_erp_device_token');
        if (!deviceToken) {
            deviceToken = 'web_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
            localStorage.setItem('school_erp_device_token', deviceToken);
        }
        sendTokenToServer(deviceToken);
    }

    // 2. Synthesized Audio Chime Tone
    function playNotificationChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            const notes = [
                { freq: 587.33, delay: 0.00, dur: 0.28 }, // D5
                { freq: 880.00, delay: 0.12, dur: 0.32 }, // A5
                { freq: 1174.66, delay: 0.24, dur: 0.45 } // D6
            ];

            notes.forEach(note => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(note.freq, ctx.currentTime + note.delay);

                gain.gain.setValueAtTime(0.0001, ctx.currentTime + note.delay);
                gain.gain.exponentialRampToValueAtTime(0.28, ctx.currentTime + note.delay + 0.04);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + note.delay + note.dur);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(ctx.currentTime + note.delay);
                osc.stop(ctx.currentTime + note.delay + note.dur);
            });

            if (navigator.vibrate) {
                navigator.vibrate([150, 75, 150]);
            }
        } catch (e) {
            console.debug('Audio chime error:', e);
        }
    }

    // 3. Floating Mobile Push Banner UI
    function showFloatingPushBanner(item) {
        if (!item) return;

        let container = document.getElementById('mobilePushBannerContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'mobilePushBannerContainer';
            container.style.cssText = `
                position: fixed;
                top: 16px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 999999;
                width: calc(100% - 32px);
                max-width: 420px;
                pointer-events: none;
                display: flex;
                flex-direction: column;
                gap: 10px;
            `;
            document.body.appendChild(container);
        }

        const banner = document.createElement('div');
        banner.style.cssText = `
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #ffffff;
            border-radius: 16px;
            padding: 14px 16px;
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            pointer-events: auto;
            cursor: pointer;
            animation: slideDownPush 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        `;

        const iconColor = item.color || '#38bdf8';
        const iconClass = item.icon || 'fa-bell';
        const itemUrl = (item.action_url && item.action_url !== '#') ? item.action_url : 'javascript:void(0);';

        banner.innerHTML = `
            <style>
                @keyframes slideDownPush {
                    0% { transform: translateY(-120%); opacity: 0; }
                    100% { transform: translateY(0); opacity: 1; }
                }
                @keyframes slideUpPush {
                    0% { transform: translateY(0); opacity: 1; }
                    100% { transform: translateY(-120%); opacity: 0; }
                }
            </style>
            <div style="width:38px; height:38px; border-radius:10px; background:${iconColor}25; color:${iconColor}; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:16px;">
                <i class="fas ${iconClass}"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2px;">
                    <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:#94a3b8; letter-spacing:0.5px;">School Notification</span>
                    <span style="font-size:10px; color:#64748b;">Just now</span>
                </div>
                <div style="font-weight:700; font-size:13px; color:#ffffff; line-height:1.3; margin-bottom:2px;">${escapeHtml(item.title)}</div>
                <div style="font-size:12px; color:#cbd5e1; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">${escapeHtml(item.message)}</div>
            </div>
            <button type="button" style="background:none; border:none; color:#94a3b8; font-size:14px; cursor:pointer; padding:0 4px;" onclick="event.stopPropagation(); this.closest('div').remove();">
                <i class="fas fa-times"></i>
            </button>
        `;

        banner.onclick = () => {
            if (typeof markNotificationRead === 'function' && item.id) {
                markNotificationRead(item.id);
            }
            if (itemUrl && itemUrl !== 'javascript:void(0);') {
                window.location.href = itemUrl;
            }
            banner.remove();
        };

        container.appendChild(banner);

        setTimeout(() => {
            if (banner.parentNode) {
                banner.style.animation = 'slideUpPush 0.3s forwards';
                setTimeout(() => banner.remove(), 300);
            }
        }, 6000);
    }

    function initRealtimeStream() {
        if (!window.EventSource) {
            startPollingFallback();
            return;
        }

        try {
            evtSource = new EventSource("{{ route('notifications.stream') }}", { withCredentials: true });

            evtSource.addEventListener('connected', function(e) {});

            evtSource.addEventListener('notification', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    if (data.type === 'new_notifications') {
                        handleIncomingNotifications(data.items, data.unread_count);
                        triggerDynamicUISync();
                    }
                    if (data.active_session_id) {
                        checkAcademicSessionSync(data.active_session_id, data.active_session_name);
                    }
                } catch (err) {
                    console.error('Realtime notification parse error:', err);
                }
            });

            evtSource.addEventListener('ping', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    if (typeof data.unread_count !== 'undefined') {
                        updateBadgeOnly(data.unread_count);
                    }
                    if (data.active_session_id) {
                        checkAcademicSessionSync(data.active_session_id, data.active_session_name);
                    }
                } catch (err) {}
            });

            evtSource.onerror = function(e) {
                if (evtSource) {
                    evtSource.close();
                }
                startPollingFallback();
            };
        } catch (err) {
            startPollingFallback();
        }
    }

    function startPollingFallback() {
        if (fallbackInterval) return;
        fetchLatestNotifications();
        fallbackInterval = setInterval(function() {
            if (!document.hidden) {
                fetchLatestNotifications();
            }
        }, 15000);
    }

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            fetchLatestNotifications();
        }
    });

    window.addEventListener('focus', function() {
        fetchLatestNotifications();
    });

    let isSyncingAcademicSession = false;

    function checkAcademicSessionSync(serverSessionId, serverSessionName) {
        if (!serverSessionId || isSyncingAcademicSession) return;

        // Strictly ignore role-restricted admins, teachers, students, parents - their sessions are protected
        if (window.isSessionRestricted || (typeof window.canManageAcademicSession !== 'undefined' && !window.canManageAcademicSession)) {
            return;
        }

        const selectEl = document.getElementById('topbarAcademicYear');
        const currentSessionVal = (typeof window.currentAcademicSessionId !== 'undefined' && window.currentAcademicSessionId) 
            ? window.currentAcademicSessionId 
            : (selectEl ? (selectEl.value || selectEl.getAttribute('value')) : null);

        if (!currentSessionVal) return;

        const currentSessId = parseInt(currentSessionVal, 10);
        const serverSessId = parseInt(serverSessionId, 10);

        if (serverSessId && currentSessId && serverSessId !== currentSessId) {
            // Check if recently switched locally in this tab (within last 3.5 seconds) to avoid redundant reload
            const lastSwitchTime = sessionStorage.getItem('last_academic_session_switch_time');
            if (lastSwitchTime && (Date.now() - parseInt(lastSwitchTime, 10) < 3500)) {
                return;
            }

            isSyncingAcademicSession = true;
            const displayName = serverSessionName || 'active session';

            if (typeof showToast === 'function') {
                showToast('Academic Year changed to ' + displayName + ' on another device. Syncing...', 'info');
            }

            setTimeout(function() {
                window.location.reload();
            }, 900);
        }
    }

    function fetchLatestNotifications() {
        fetch("{{ route('notifications.fetch-latest') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && typeof data.unread_count !== 'undefined') {
                handleIncomingNotifications(data.notifications, data.unread_count);
                triggerDynamicUISync();
            }
            if (data && data.active_session_id) {
                checkAcademicSessionSync(data.active_session_id, data.active_session_name);
            }
        })
        .catch(err => console.error('Fetch notifications error:', err));
    }

    function handleIncomingNotifications(items, unreadCount) {
        if (items && Array.isArray(items)) {
            let hasNewItems = false;
            let newestItem = null;

            items.forEach(item => {
                if (item && item.id) {
                    if (!knownNotifIds.has(item.id)) {
                        knownNotifIds.add(item.id);
                        if (!isInitialLoad && !item.is_read) {
                            hasNewItems = true;
                            newestItem = item;
                        }
                    }
                }
            });

            if (hasNewItems && newestItem) {
                playNotificationChime();
                showFloatingPushBanner(newestItem);
            }

            isInitialLoad = false;
        }

        updateNavbarNotifications(items, unreadCount);
    }

    function updateBadgeOnly(count) {
        const badges = document.querySelectorAll('.notif-badge, .badge-count, #notifBadgeCount, .sb-notif-badge, .sb-muf-badge');
        badges.forEach(b => {
            b.textContent = count;
            b.style.display = count > 0 ? 'inline-block' : 'none';
        });

        const unreadPills = document.querySelectorAll('#notifUnreadPill, #sbNotifUnreadPill');
        unreadPills.forEach(p => {
            p.textContent = count + ' Unread';
        });

        const dots = document.querySelectorAll('.badge-dot, #notifBadgeDot');
        dots.forEach(d => {
            d.style.display = count > 0 ? 'block' : 'none';
        });
    }

    function updateNavbarNotifications(items, unreadCount) {
        updateBadgeOnly(unreadCount);

        const adminDropContainer = document.querySelector('.notif-drop #notifListContainer, .notif-drop div[style*="max-height"]');
        if (adminDropContainer && items) {
            if (items.length === 0) {
                adminDropContainer.innerHTML = '<div class="nd-empty" style="padding:15px; text-align:center; color:#94a3b8; font-size:13px;">No new notifications today</div>';
            } else {
                let html = '';
                items.forEach(n => {
                    const iconClass = n.icon || 'fa-bell';
                    const color = n.color || '#8b5cf6';
                    const itemUrl = (n.action_url && n.action_url !== '#') ? n.action_url : 'javascript:void(0);';
                    const isUnreadBg = !n.is_read ? 'background: rgba(37,99,235,0.06); font-weight:600;' : 'background: #ffffff;';
                    
                    html += `
                        <a href="${itemUrl}" class="nd-item" onclick="markNotificationRead(${n.id})" style="${isUnreadBg} display:flex; gap:10px; padding:10px; border-bottom:1px solid #f1f5f9; text-decoration:none; color:inherit;">
                            <div class="nd-ico" style="background: ${color}20; color: ${color}; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fas ${iconClass}"></i>
                            </div>
                            <div class="nd-body" style="flex:1;">
                                <div class="nd-title" style="font-weight: 700; font-size:12.5px; color:#1e293b;">${escapeHtml(n.title)}</div>
                                <div class="nd-desc" style="font-size:11.5px; color:#64748b; margin-top:2px;">${escapeHtml(n.message)}</div>
                                <div class="nd-time" style="font-size:10px; color:#94a3b8; margin-top:4px;">${n.time}</div>
                            </div>
                        </a>
                    `;
                });
                adminDropContainer.innerHTML = html;
            }
        }

        const listContainers = document.querySelectorAll('#notifListContainer, #sbNotifListContainer');
        listContainers.forEach(container => {
            if (container === adminDropContainer) return;
            if (!items) return;

            if (items.length === 0) {
                container.innerHTML = `
                    <div style="padding: 40px 15px; text-align: center; color: #94a3b8;">
                        <i class="fas fa-bell-slash" style="font-size: 32px; margin-bottom: 12px; opacity: 0.4;"></i>
                        <div style="font-weight:700; color:#334155; font-size:14px;">No notifications yet</div>
                        <div style="font-size:12px; margin-top:4px;">You're all caught up!</div>
                    </div>`;
            } else {
                let html = '';
                items.forEach(item => {
                    const iconClass = item.icon || 'fa-bell';
                    const color = item.color || '#2563eb';
                    const unreadStyle = !item.is_read ? 'border-left: 3px solid #2563eb; background: rgba(37,99,235,0.04);' : 'background: #ffffff;';
                    const itemUrl = item.action_url && item.action_url !== '#' ? item.action_url : 'javascript:void(0);';

                    html += `
                        <a href="${itemUrl}" class="notif-item ${!item.is_read ? 'unread' : ''}" style="${unreadStyle} display:block; padding: 12px 14px; margin-bottom:6px; border-radius:12px; border: 1px solid rgba(0,0,0,0.05); text-decoration:none; color:inherit; transition: background 0.2s;" onclick="markNotificationRead(${item.id})">
                            <div style="display:flex; gap:12px; align-items:flex-start;">
                                <div style="width:36px; height:36px; border-radius:12px; background:${color}18; color:${color}; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px;">
                                    <i class="fas ${iconClass}"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <div style="font-weight:700; font-size:13px; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(item.title)}</div>
                                        ${!item.is_read ? '<span style="width:7px; height:7px; border-radius:50%; background:#2563eb; display:inline-block; flex-shrink:0;"></span>' : ''}
                                    </div>
                                    <div style="font-size:12px; color:#64748b; margin-top:3px; line-height:1.35;">${escapeHtml(item.message)}</div>
                                    <div style="font-size:10.5px; color:#94a3b8; margin-top:6px; font-weight:500;"><i class="far fa-clock"></i> ${item.time}</div>
                                </div>
                            </div>
                        </a>`;
                });
                container.innerHTML = html;
            }
        });
    }

    function triggerDynamicUISync() {
        if (typeof window.syncTeacherLeaveUI === 'function') {
            window.syncTeacherLeaveUI();
        }
        if (typeof window.syncAdminLeaveTable === 'function') {
            window.syncAdminLeaveTable();
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    window.markNotificationRead = function(id, event) {
        if (event && event.target && (event.target.getAttribute('href') === '#' || event.target.getAttribute('href') === 'javascript:void(0);')) {
            event.preventDefault();
        }
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).then(() => {
            fetchLatestNotifications();
        }).catch(err => console.error('Mark read error:', err));
    };

    window.markAllNotifsAsRead = function() {
        updateBadgeOnly(0);
        const listContainers = document.querySelectorAll('#notifListContainer, #sbNotifListContainer, .notif-drop #notifListContainer, .notif-drop div[style*="max-height"]');
        listContainers.forEach(c => {
            c.innerHTML = '<div class="nd-empty" style="padding:20px; text-align:center; color:#94a3b8; font-size:13px;"><i class="fas fa-check-circle" style="font-size:20px; color:#10b981; margin-bottom:6px; display:block;"></i>All notifications marked as read</div>';
        });

        fetch("{{ route('notifications.read-all') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(res => res.json())
        .then(() => fetchLatestNotifications())
        .catch(err => console.error('Mark all read error:', err));
    };

    window.playNotificationChime = playNotificationChime;

    document.addEventListener('DOMContentLoaded', () => {
        initFirebaseMessaging();
        initRealtimeStream();
    });
})();
</script>
