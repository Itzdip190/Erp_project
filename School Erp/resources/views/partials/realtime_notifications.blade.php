<script>
/**
 * SchoolCloud ERP Real-Time Notification & Dynamic Sync Engine
 */
(function() {
    let evtSource = null;
    let fallbackInterval = null;

    function initRealtimeStream() {
        if (!window.EventSource) {
            startPollingFallback();
            return;
        }

        try {
            evtSource = new EventSource("{{ route('notifications.stream') }}", { withCredentials: true });

            evtSource.addEventListener('connected', function(e) {
                // Connected successfully
            });

            evtSource.addEventListener('notification', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    if (data.type === 'new_notifications') {
                        updateNavbarNotifications(data.items, data.unread_count);
                        triggerDynamicUISync();
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
                } catch (err) {}
            });

            evtSource.onerror = function(e) {
                if (evtSource) {
                    evtSource.close();
                }
                // Fallback to high-frequency polling on stream error
                startPollingFallback();
            };
        } catch (err) {
            startPollingFallback();
        }
    }

    function startPollingFallback() {
        if (fallbackInterval) return;
        fetchLatestNotifications();
        fallbackInterval = setInterval(fetchLatestNotifications, 8000);
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
                updateNavbarNotifications(data.notifications, data.unread_count);
                triggerDynamicUISync();
            }
        })
        .catch(err => console.error('Fetch notifications error:', err));
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

        // Update Admin Layout Dropdown
        const adminDropContainer = document.querySelector('.notif-drop #notifListContainer, .notif-drop div[style*="max-height"]');
        if (adminDropContainer && items) {
            if (items.length === 0) {
                adminDropContainer.innerHTML = '<div class="nd-empty" style="padding:15px; text-align:center; color:#94a3b8; font-size:13px;">No new notifications today</div>';
            } else {
                let html = '';
                items.forEach(n => {
                    const iconClass = n.icon || 'fa-bell';
                    const color = n.color || '#8b5cf6';
                    const isUnreadBg = !n.is_read ? 'background:#f8fafc; font-weight:600;' : '';
                    
                    html += `
                        <a href="${n.action_url || '#'}" class="nd-item" onclick="markNotificationRead(${n.id})" style="${isUnreadBg} display:flex; gap:10px; padding:10px; border-bottom:1px solid #f1f5f9; text-decoration:none; color:inherit;">
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

        // Update Mobile & Panel Notification Containers
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

    window.markNotificationRead = function(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).then(() => fetchLatestNotifications());
    };

    window.markAllNotifsAsRead = function() {
        fetch("{{ route('notifications.read-all') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).then(() => fetchLatestNotifications());
    };

    document.addEventListener('DOMContentLoaded', initRealtimeStream);
})();
</script>
