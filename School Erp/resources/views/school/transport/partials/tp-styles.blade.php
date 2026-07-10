{{-- ╔══════════════════════════════════════════════════════════╗
     ║  TRANSPORT MODULE — SHARED STYLES (included by all views)  ║
     ╚══════════════════════════════════════════════════════════╝ --}}
<style>
/* ── Transport shared design tokens (Blue & White Theme) ────── */
:root {
    --gold: #2563eb !important; /* Forces header icons and accents to render in brand blue */
    --tp-pick:   #2563eb;
    --tp-pick-bg:#eff6ff;
    --tp-drop:   #1d4ed8;
    --tp-drop-bg:#dbeafe;
    --tp-yes:    #1e40af;
    --tp-yes-bg: #eff6ff;
    --tp-no:     #64748b;
    --tp-no-bg:  #f1f5f9;
}

/* ── Custom Theme Buttons ────────────────────────────────────── */
.btn-gold {
    background: #2563eb !important;
    color: #ffffff !important;
    border: 1px solid #1d4ed8 !important;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.08) !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer !important;
    text-decoration: none !important;
}
.btn-gold:hover {
    background: #1d4ed8 !important;
    border-color: #1e40af !important;
    color: #ffffff !important;
    box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3) !important;
    transform: translateY(-1px) !important;
}

.btn-outline {
    background: transparent !important;
    color: #374151 !important;
    border: 1px solid var(--border) !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}
.btn-outline:hover {
    background: var(--page) !important;
    border-color: var(--t3) !important;
    color: var(--t1) !important;
}

/* ── Form Styling Overrides ──────────────────────────────────── */
.form-group {
    margin-bottom: 18px;
}
.form-label {
    display: block;
    font-weight: 700 !important;
    font-size: 12px !important;
    color: var(--t2) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 6px !important;
}
.form-control {
    width: 100%;
    border-radius: 10px !important;
    border: 1px solid var(--border) !important;
    padding: 10px 14px !important;
    font-size: 13.5px !important;
    font-family: inherit !important;
    transition: all 0.2s ease !important;
    background: var(--white) !important;
    color: var(--t1) !important;
}
.form-control:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15) !important;
    outline: none !important;
}
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E") !important;
    background-position: right 10px center !important;
    background-repeat: no-repeat !important;
    background-size: 20px !important;
    padding-right: 36px !important;
}

/* ── Table ────────────────────────────────────────────────────  */
.tp-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 12px;
    border: 1px solid var(--border);
    background: var(--white);
}
table.tp-table { width: 100%; border-collapse: collapse; min-width: 600px; }
table.tp-table th {
    padding: 14px 18px; text-align: left;
    font-size: 11px; font-weight: 700; color: var(--t2);
    text-transform: uppercase; letter-spacing: .5px;
    background: var(--page); border-bottom: 2px solid var(--border);
    white-space: nowrap;
}
table.tp-table td {
    padding: 14px 18px; font-size: 13.5px; color: var(--t1);
    border-bottom: 1px solid var(--border); vertical-align: middle;
}
table.tp-table tr:last-child td { border-bottom: none; }
table.tp-table tr:hover td { background: rgba(37,99,235,.02); }
body.dark-mode table.tp-table th { background: #111827; }
body.dark-mode table.tp-table td { border-color: #1e293b; }

/* ── Action buttons ────────────────────────────────────────── */
.tp-btn-edit, .tp-btn-del {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: none; cursor: pointer; font-size: 13px; transition: all .15s ease;
}
.tp-btn-edit { background: #eff6ff; color: #2563eb; }
.tp-btn-edit:hover { background: #dbeafe; color: #1d4ed8; transform: translateY(-1px); }
.tp-btn-del  { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
.tp-btn-del:hover { background: #fee2e2; color: #dc2626; transform: translateY(-1px); }
body.dark-mode .tp-btn-edit { background: rgba(37,99,235,.15); color: #60a5fa; }
body.dark-mode .tp-btn-del  { background: rgba(239,68,68,.15);  color: #f87171; }

/* ── Badges ─────────────────────────────────────────────────── */
.tp-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700;
}
.tp-badge-pick  { background: var(--tp-pick-bg);  color: var(--tp-pick); }
.tp-badge-drop  { background: var(--tp-drop-bg);  color: var(--tp-drop); }
.tp-badge-yes   { background: var(--tp-yes-bg);   color: var(--tp-yes);  }
.tp-badge-no    { background: var(--tp-no-bg);     color: var(--tp-no);   }
.tp-badge-blue  { background: #eff6ff; color: #2563eb; }
.tp-badge-purple{ background: #f5f3ff; color: #7c3aed; }
.tp-badge-gold  { background: #eff6ff; color: #2563eb; }

/* ── Stat cards ─────────────────────────────────────────────── */
.tp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
.tp-stat {
    position: relative;
    background: var(--white); border-radius: 18px; padding: 20px 24px;
    border: 1px solid var(--border);
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01), 0 2px 4px -1px rgba(0,0,0,0.006);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.tp-stat::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 5px;
    background: var(--sc, var(--gold));
    transition: all 0.3s;
}
.tp-stat:hover { 
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02); 
    transform: translateY(-4px);
    border-color: rgba(37, 99, 235, 0.2);
}
.tp-stat:hover::before {
    width: 8px;
}
.tp-stat-icon {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; background: var(--sb, #eff6ff); color: var(--sc, var(--gold));
    transition: all 0.3s;
}
.tp-stat:hover .tp-stat-icon {
    transform: scale(1.1) rotate(6deg);
}
.tp-stat-label { font-size: 11px; font-weight: 700; color: var(--t2); text-transform: uppercase; letter-spacing: .75px; margin-bottom: 4px; }
.tp-stat-val   { font-size: 24px; font-weight: 800; color: var(--t1); line-height: 1; }

/* ── Alert override for transport ───────────────────────────── */
.tp-alert-info {
    background: linear-gradient(135deg, #1e3a8a, #2563eb); border-radius: 16px;
    padding: 18px 24px; margin-bottom: 24px;
    display: flex; align-items: flex-start; gap: 14px; color: #fff;
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
}
.tp-alert-info i { font-size: 22px; flex-shrink: 0; margin-top: 2px; opacity: .9; }

/* ── Quick action grid ──────────────────────────────────────── */
.tp-quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 16px; }
.tp-quick-btn {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 10px; padding: 22px 14px;
    border: 1px solid var(--border); border-radius: 16px;
    background: var(--white); text-decoration: none; color: var(--t1);
    transition: all .25s cubic-bezier(0.4, 0, 0.2, 1); text-align: center; min-height: 105px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
}
.tp-quick-btn:hover {
    border-color: var(--qc, var(--gold)); background: var(--qbg, #eff6ff);
    color: var(--qc, var(--gold)); transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02); 
    text-decoration: none;
}
.tp-quick-btn i { font-size: 26px; color: var(--qc, var(--gold)); transition: transform .25s ease; }
.tp-quick-btn:hover i { transform: scale(1.15); }
.tp-quick-btn span { font-size: 13px; font-weight: 700; }

/* ── Card header with badge ─────────────────────────────────── */
.tp-card-hdr { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.tp-card-hdr h3 { font-size: 15px; font-weight: 700; color: var(--t1); margin: 0; }

/* ── Premium Modal ───────────────────────────────────────────── */
body.modal-open { overflow: hidden; }
.tp-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(15,23,42,.65); backdrop-filter: blur(8px);
    align-items: center; justify-content: center; padding: 16px;
    opacity: 0; transition: opacity 0.25s ease;
}
.tp-modal-overlay.open { display: flex; opacity: 1; }
.tp-modal {
    background: var(--white); border-radius: 20px; width: 100%;
    max-width: 580px; max-height: 90vh; overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
    border: 1px solid rgba(255,255,255,0.1);
    transform: translateY(20px) scale(0.96); transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    display: flex; flex-direction: column;
}
.tp-modal-overlay.open .tp-modal {
    transform: translateY(0) scale(1);
}
.tp-modal-hdr {
    padding: 20px 24px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; background: var(--white); z-index: 10;
}
.tp-modal-hdr h3 { font-size: 16px; font-weight: 800; color: var(--t1); margin: 0; display: flex; align-items: center; gap: 8px; }
.tp-modal-close { background: var(--page); border: none; font-size: 18px; cursor: pointer; color: var(--t2); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s; }
.tp-modal-close:hover { background: #fef2f2; color: #ef4444; transform: rotate(90deg); }
.tp-modal-body { padding: 24px; }
.tp-modal-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    color: var(--t3); letter-spacing: 1px; margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.tp-modal-section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }
.tp-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.tp-g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
@media (max-width: 480px) {
    .tp-g2, .tp-g3 { grid-template-columns: 1fr; }
    .tp-modal { max-height: 95vh; }
}

/* ── Fare total row ──────────────────────────────────────────── */
.tp-fare-total { background: var(--page); border-radius: 12px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-top: 8px; border: 1px solid var(--border); }
.tp-fare-total-label { font-size: 13.5px; font-weight: 700; color: var(--t2); }
.tp-fare-total-val   { font-size: 18px; font-weight: 800; color: #16a34a; }

/* ── Empty state ─────────────────────────────────────────────── */
.tp-empty { padding: 50px 24px; text-align: center; }
.tp-empty i { font-size: 42px; color: var(--t3); opacity: 0.5; display: block; margin-bottom: 16px; }
.tp-empty p { color: var(--t2); font-size: 14px; margin: 0; font-weight: 500; }

/* ── Vehicle plate ────────────────────────────────────────────── */
.tp-plate {
    background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff;
    font-weight: 800; font-size: 13px; border-radius: 8px;
    padding: 7px 14px; letter-spacing: 1px; text-align: center;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.15);
}

/* ── Trip type badges ─────────────────────────────────────────── */
.tp-trip-pick { background: #eff6ff; color: #2563eb; }
.tp-trip-drop { background: #fffbeb; color: #d97706; }
.tp-trip-both { background: #f0fdf4; color: #16a34a; }

/* ── Responsive adjustment ────────────────────────────────────── */
.tp-page-container {
    background: var(--white);
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
}
</style>

<script>
// Modal toggle helpers
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('open');
        document.body.classList.add('modal-open');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('open');
        document.body.classList.remove('modal-open');
    }
}

// Close when clicking outside modal content
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tp-modal-overlay')) {
            const modalId = e.target.id;
            closeModal(modalId);
            // Call resets if present
            if (modalId === 'vehicleModal' && typeof vReset === 'function') vReset();
            if (modalId === 'stopModal' && typeof stopReset === 'function') stopReset();
            if (modalId === 'routeModal' && typeof routeReset === 'function') routeReset();
            if (modalId === 'tripModal' && typeof tripReset === 'function') tripReset();
        }
    });
});
</script>

