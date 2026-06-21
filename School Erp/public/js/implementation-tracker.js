/* ═══════════════════════════════════════════════════════════════
   IMPLEMENTATION TRACKER — Scoped JS Logic
   ═══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {
    let currentTab = 'data';
    let editMode = false;

    // --- TAB SWITCHING ---
    const tabButtons = document.querySelectorAll('.impl-tab-btn');
    const tabContents = document.querySelectorAll('.impl-tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            if (editMode) {
                if (!confirm('You are in Edit Mode. Switching tabs will discard unsaved changes. Proceed?')) {
                    return;
                }
                exitEditMode();
            }

            const tab = this.getAttribute('data-tab');
            currentTab = tab;

            // Update active tab buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Update active tab content pane
            tabContents.forEach(c => c.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');
        });
    });

    // --- TOGGLE EDIT MODE ---
    const editBtn = document.getElementById('btn-edit-mode');
    const updateBtn = document.getElementById('btn-update-tracker');

    editBtn.addEventListener('click', function () {
        enterEditMode();
    });

    function enterEditMode() {
        editMode = true;
        editBtn.disabled = true;
        updateBtn.disabled = false;

        const activePane = document.getElementById(`tab-${currentTab}`);
        const readElements = activePane.querySelectorAll('.impl-read-mode');
        const editElements = activePane.querySelectorAll('.impl-edit-mode');

        readElements.forEach(el => el.style.display = 'none');
        editElements.forEach(el => el.style.display = 'block');
    }

    function exitEditMode() {
        editMode = false;
        editBtn.disabled = false;
        updateBtn.disabled = true;

        const activePane = document.getElementById(`tab-${currentTab}`);
        const readElements = activePane.querySelectorAll('.impl-read-mode');
        const editElements = activePane.querySelectorAll('.impl-edit-mode');

        readElements.forEach(el => el.style.display = 'block');
        editElements.forEach(el => el.style.display = 'none');
    }

    // --- UPDATE (AJAX BULK SUBMIT WITH FILES) ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    updateBtn.addEventListener('click', function () {
        const activePane = document.getElementById(`tab-${currentTab}`);
        const formElement = activePane.querySelector('.impl-tab-form');
        
        if (!formElement) return;

        const formData = new FormData(formElement);
        // Laravel spoofing for PUT request with multipart/form-data
        formData.append('_method', 'PUT');

        // Show updating status
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

        fetch(`/implementation-tracker/update/${currentTab}`, {
            method: 'POST', // must be POST for multipart files uploading
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('Updated successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(result.message || 'An error occurred.', 'danger');
                updateBtn.disabled = false;
                updateBtn.innerHTML = '<i class="fas fa-save"></i> UPDATE';
            }
        })
        .catch(error => {
            console.error(error);
            showToast('Update failed. Connection error.', 'danger');
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="fas fa-save"></i> UPDATE';
        });
    });

    // --- TOAST NOTIFICATIONS ---
    function showToast(message, type = 'success') {
        let toast = document.getElementById('appToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'appToast';
            document.body.appendChild(toast);
        }

        toast.innerHTML = message;
        toast.className = 'show';
        if (type === 'danger') {
            toast.style.borderLeftColor = '#ef4444';
        } else {
            toast.style.borderLeftColor = '#0B5394';
        }

        setTimeout(() => {
            toast.className = '';
        }, 3000);
    }

    // --- AUDIT LOGS MODAL ---
    const logsBtn = document.getElementById('btn-show-logs');
    const logsModal = document.getElementById('modal-logs');
    const logsTableBody = document.getElementById('logs-table-body');

    logsBtn.addEventListener('click', function () {
        // Clear old logs first
        logsTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading audit trail...</td></tr>';
        openModal('modal-logs');

        fetch('/implementation-tracker/logs', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(logs => {
            if (logs.length === 0) {
                logsTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#94a3b8;">No audit logs recorded yet.</td></tr>';
                return;
            }

            let html = '';
            logs.forEach(log => {
                html += `
                    <tr>
                        <td><strong>${log.tab_name}</strong></td>
                        <td>${log.row_reference}</td>
                        <td><span style="font-weight:600; color:#3b82f6;">${log.field_changed}</span></td>
                        <td style="color:#ef4444; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${log.old_value}</td>
                        <td style="color:#10b981; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${log.new_value}</td>
                        <td><strong>${log.changed_by}</strong></td>
                        <td>${log.changed_at}</td>
                    </tr>
                `;
            });
            logsTableBody.innerHTML = html;
        })
        .catch(error => {
            console.error(error);
            logsTableBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#ef4444;"><i class="fas fa-exclamation-triangle"></i> Failed to load logs.</td></tr>';
        });
    });
});

// --- HELPER OPEN/CLOSE MODALS ---
function openModal(id) {
    document.getElementById(id).classList.add('active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('impl-modal')) {
        event.target.classList.remove('active');
    }
});
