<div class="impl-modal" id="modal-logs">
    <div class="impl-modal-content" style="max-width: 900px;">
        <div class="impl-modal-hdr">
            <h3 style="font-family:'Plus Jakarta Sans',sans-serif; font-size:15px; font-weight:800; margin:0;">
                <i class="fas fa-history"></i> SYSTEM AUDIT TRAIL LOGS
            </h3>
            <button class="impl-modal-close" onclick="closeModal('modal-logs')">&times;</button>
        </div>
        <div class="impl-modal-body">
            <div class="impl-table-container" style="max-height: 55vh;">
                <table class="impl-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tab Name</th>
                            <th style="width: 15%;">Row Reference</th>
                            <th style="width: 15%;">Field Changed</th>
                            <th style="width: 20%;">Old Value</th>
                            <th style="width: 20%;">New Value</th>
                            <th style="width: 15%;">Changed By</th>
                            <th style="width: 15%;">Changed At</th>
                        </tr>
                    </thead>
                    <tbody id="logs-table-body">
                        <tr>
                            <td colspan="7" style="text-align:center; color:#64748b; padding:20px;">
                                <i class="fas fa-spinner fa-spin"></i> Loading audit logs...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
