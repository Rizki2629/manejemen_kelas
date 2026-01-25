<?= $this->extend('layouts/siswa_layout') ?>

<?= $this->section('title') ?>Rekap Bulanan 7 Kebiasaan<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
/* Modern Design System */
body {
  background: #f1f5f9;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #1e293b;
}

.monthly-container {max-width:1900px; margin:0 auto; padding:1rem 2rem;}

/* Header */
.monthly-header {
  text-align: center;
  margin-bottom: 2rem;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border-radius: 20px;
  color: white;
  box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
}

.monthly-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.monthly-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
}

/* Month Selector */
.month-selector {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.month-selector-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.month-selector label {
  font-weight: 600;
  color: #1e293b;
  font-size: 1rem;
}

.month-selector input[type="month"] {
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  cursor: pointer;
  transition: border-color 0.3s ease;
}

.month-selector input[type="month"]:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.btn-current-month {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border: none;
  border-radius: 10px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.btn-current-month:hover {
  transform: scale(1.05);
}


/* Table Container */
.table-container {background:white; border-radius:16px; padding:2rem 2.5rem; box-shadow:0 4px 20px rgba(0,0,0,0.08); width:100%; max-width:1700px; margin:0 auto 2rem;}
/* Scroll wrapper for sticky header/first col */
.monthly-table-scroll{max-height:72vh; overflow:auto; border-radius:14px; position:relative; box-shadow:inset 0 0 0 1px #e2e8f0;}

.table-title {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #1e293b;
  font-size: 1.5rem;
  font-weight: 700;
}

/* Monthly Report Table - Spreadsheet Style */
.monthly-table {width:100%; border-collapse:separate; border-spacing:0; font-size:.7rem; min-width:1280px; margin:0 auto; table-layout:auto; --topHeaderH:44px; --subHeaderH:28px;}
.monthly-table th,.monthly-table td {padding:8px 8px; text-align:center; vertical-align:middle; position:relative; font-size:.7rem; line-height:1.15; font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;}
.monthly-table thead tr.top-header th {background:linear-gradient(135deg,#d3f1ff 0%,#b9e7fb 60%,#afe0f6 100%); font-weight:700; color:#0f172a; letter-spacing:.4px; height:var(--topHeaderH);}
.monthly-table thead tr.sub-header th {background:#e6f7ff; font-weight:600; color:#334155; height:var(--subHeaderH); padding-top:4px; padding-bottom:4px;}
/* Soft grid lines */
.monthly-table tbody td {border-top:1px solid #e2e8f0; border-right:1px solid #eef2f7;}
.monthly-table tbody tr:last-child td {border-bottom:1px solid #e2e8f0;}
.monthly-table tbody td:last-child {border-right:1px solid #e2e8f0;}
.monthly-table thead th {border-right:1px solid #cbd5e1;}
.monthly-table thead th:last-child {border-right:1px solid #cbd5e1;}
/* Rounded corners */
.monthly-table thead tr.top-header th:first-child {border-top-left-radius:14px;}
.monthly-table thead tr.top-header th:last-child {border-top-right-radius:14px;}
.monthly-table tbody tr:last-child td:first-child {border-bottom-left-radius:14px;}
.monthly-table tbody tr:last-child td:last-child {border-bottom-right-radius:14px;}
/* Zebra & hover */
.monthly-table tbody tr:nth-child(even) td {background:#f8fafc;}
.monthly-table tbody tr:nth-child(odd) td {background:#ffffff;}
.monthly-table tbody tr:hover td {background:#eef6ff;}
/* Strong full-row hover effect overriding individual cell backgrounds */
.monthly-table tbody tr:hover td,
.monthly-table tbody tr:hover td.simple-habit.completed,
.monthly-table tbody tr:hover td.simple-habit.not-completed,
.monthly-table tbody tr:hover td.simple-habit.no-data,
.monthly-table tbody tr:hover td.time-habit.good,
.monthly-table tbody tr:hover td.time-habit.bad,
.monthly-table tbody tr:hover td.time-habit.no-data,
.monthly-table tbody tr:hover td.pray-cell.done,
.monthly-table tbody tr:hover td.pray-cell.miss,
.monthly-table tbody tr:hover td.worship-others {
  background:#f0f7ff !important;
  color:#0f172a !important;
}
.monthly-table tbody tr:hover td.status-cell .status-bar {background:#e3f1ff;}
/* Sticky first column */
.monthly-table td.date-cell, .monthly-table th.date-header {position:sticky; left:0; z-index:6; box-shadow:2px 0 0 0 #f1f5f9;}
.monthly-table th.date-header {z-index:8;}
/* Sticky header (multi-row) */
/* First header row */
.monthly-table thead tr.top-header th {position:sticky; top:0; z-index:12;}
/* Second header row sits below first */
.monthly-table thead tr.sub-header th {position:sticky; top:var(--topHeaderH); z-index:11; border-top:1px solid #c7dfe9;}
/* Ensure left sticky date header (rowspan) stays above others */
.monthly-table thead tr.top-header th.date-header{z-index:15;}
/* Reserve space so content starts below double header when printing (optional) */
/* Status cell with progress bar */
.status-cell {padding:0 4px; min-width:70px;}
.status-bar {position:relative; width:100%; height:26px; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; font-weight:600; font-size:.65rem; display:flex; align-items:center; justify-content:center; color:#334155;}
.status-bar-fill {position:absolute; inset:0; background:linear-gradient(90deg,#34d399,#10b981); width:0%; transition:width .5s ease; opacity:.18;}
.status-bar[data-pct="0"] .status-bar-fill {display:none;}
.status-bar .status-text {position:relative; z-index:2; letter-spacing:.4px;}
/* Future & today accents */
.monthly-table td.date-cell.today {box-shadow:inset 0 0 0 2px #1d4ed8;}
.monthly-table td.date-cell.weekend {font-weight:700;}
/* Prayer cells refine */
.monthly-table td.pray-cell {font-size:.58rem; font-weight:700; letter-spacing:.5px;}
.monthly-table td.pray-cell.done {color:#059669; background:#ecfdf5;}
.monthly-table td.pray-cell.miss {color:#94a3b8;}
/* Transition for interactive cells */
.monthly-table td {transition:background .25s, color .25s, box-shadow .25s;}
.monthly-table th.date-header {min-width:140px; font-size:.7rem;}
.monthly-table th.status-header {min-width:60px;}
.monthly-table th.pray-header {width:20px !important; padding:1px 0; font-size:.5rem;}
.monthly-table td.pray-cell {width:10px !important; padding:0;}
.monthly-table col.pray-col {width:10px !important;}
.monthly-table th.lainnya-header {min-width:130px;}
.monthly-table td {height:34px;}
.monthly-table td.date-cell {font-weight:600; text-align:left; padding:4px 6px; font-size:.7rem;}
.monthly-table td.status-cell {font-weight:700;}
.monthly-table td.pray-cell {font-size:.6rem; font-weight:700; cursor:pointer;}
.monthly-table td.pray-cell.done {color:#059669;}
.monthly-table td.pray-cell.miss {color:#94a3b8;}
.monthly-table td.worship-others {text-align:left; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:pointer;}
.monthly-table td.worship-others.has-data {background:linear-gradient(90deg,#ecfdf5,#d1fae5); color:#065f46; font-weight:500;}
.monthly-table td.pray-cell.miss .miss-x {background:#fee2e2!important; color:#b91c1c!important;}
.monthly-table td.pray-cell.miss {color:#b91c1c;}
.monthly-table td.simple-habit {cursor:pointer; font-weight:600; font-size:.7rem;}
.monthly-table td.simple-habit.completed {background:#dcfce7; color:#166534;}
.monthly-table td.simple-habit.not-completed {background:#fef2f2; color:#b91c1c;}
.monthly-table td.simple-habit.no-data {background:#f8fafc; color:#94a3b8;}
.monthly-table td.time-habit {font-size:.65rem; font-weight:600; letter-spacing:.5px; cursor:pointer;}
.monthly-table td.time-habit.good {background:#dcfce7; color:#166534;}
.monthly-table td.time-habit.bad {background:#fef2f2; color:#b91c1c;}
.monthly-table td.time-habit.no-data {background:#f8fafc; color:#94a3b8;}
.monthly-table td.date-cell.weekend {background:#fef2f2; color:#dc2626;}
.monthly-table td.date-cell.today {background:#dbeafe; color:#1d4ed8;}

/* Date column */
.date-cell {
  font-weight: 600;
  background: #f8fafc;
  color: #475569;
  min-width: 90px;
  font-size: 0.8rem;
  line-height: 1.2;
}

.date-cell.weekend {
  background: #fef2f2;
  color: #dc2626;
}

.date-cell.today {
  background: #dbeafe;
  color: #1d4ed8;
  font-weight: 700;
}

/* Habit cells */
.habit-cell {
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 55px;
  height: 45px;
  font-size: 1rem;
}

.habit-cell:hover {
  background: #f1f5f9;
  transform: scale(1.08);
  z-index: 5;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.habit-cell.completed {
  background: #dcfce7;
  color: #166534;
}

.habit-cell.completed::after {
  content: "✓";
  font-size: 1.1rem;
  font-weight: bold;
  color: #059669;
}

.habit-cell.not-completed {
  background: #fef2f2;
  color: #991b1b;
}

.habit-cell.not-completed::after {
  content: "✗";
  font-size: 1.1rem;
  font-weight: bold;
  color: #dc2626;
}

.habit-cell.no-data {
  background: #f8fafc;
  color: #94a3b8;
}

.habit-cell.no-data::after {
  content: "-";
  font-size: 1.1rem;
  color: #94a3b8;
}

/* Future dates */
.habit-cell.future {
  background: #f8fafc;
  color: #cbd5e1;
  cursor: not-allowed;
}

.habit-cell.future::after {
  content: "";
}

/* Legend */
.legend {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.legend-color {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}

.legend-color.completed {
  background: #dcfce7;
}

.legend-color.not-completed {
  background: #fef2f2;
}

.legend-color.no-data {
  background: #f8fafc;
}

/* Responsive Design */
@media (max-width: 768px) {
  .monthly-container {
    padding: 0.5rem;
  }
  
  .monthly-header h1 {
    font-size: 1.8rem;
  }
  
  .monthly-header p {
    font-size: 1rem;
  }
  
  .table-container {
    padding: 1rem;
    margin: 0 -0.5rem;
    border-radius: 8px;
  }
  
  .monthly-table {
    font-size: 0.75rem;
    min-width: 700px;
  }
  
  .monthly-table th,
  .monthly-table td {
    padding: 0.4rem 0.2rem;
  }
  
  .monthly-table th.date-header {
    min-width: 70px;
    font-size: 0.7rem;
  }
  
  .monthly-table th.habit-header {
    min-width: 80px;
    font-size: 0.65rem;
    padding: 0.4rem 0.2rem;
  }
  
  .date-cell {
    min-width: 70px;
    font-size: 0.7rem;
  }
  
  .habit-cell {
    min-width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
  
  .habit-cell:hover {
    transform: scale(1.15);
  }
  
  .legend {
    gap: 0.5rem;
  }
  
  .legend-item {
    font-size: 0.8rem;
  }
  
  
  .export-buttons {
    gap: 0.5rem;
  }
  
  .btn-export {
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
  }
  
  .habit-detail-content {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .habit-detail-title {
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .monthly-header h1 {
    font-size: 1.5rem;
  }
  
  .monthly-table {
    font-size: 0.7rem;
    min-width: 600px;
  }
  
  .monthly-table th.habit-header {
    min-width: 70px;
    font-size: 0.6rem;
  }
  
  .date-cell {
    min-width: 60px;
    font-size: 0.65rem;
  }
  
  .habit-cell {
    min-width: 35px;
    height: 35px;
    font-size: 0.8rem;
  }
  
  
  .export-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-export {
    width: 100%;
    max-width: 200px;
  }
  
  .habit-detail-content {
    margin: 0.5rem;
    padding: 1rem;
  }
}

/* Print Styles */
@media print {
  .month-selector,
  .btn-current-month {
    display: none;
  }
  
  .table-container {
    box-shadow: none;
    border: 1px solid #e2e8f0;
  }
  
  .monthly-table th.habit-header,
  .monthly-table th.date-header {
    background: #f8fafc !important;
    color: #475569 !important;
  }
}

/* Loading State */
.loading {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #64748b;
}

.loading-content {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 90%;
}

.loading-spinner {
  display: inline-block;
  width: 60px;
  height: 60px;
  border: 6px solid #e2e8f0;
  border-radius: 50%;
  border-top-color: #6366f1;
  border-right-color: #8b5cf6;
  animation: spin 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
  margin-bottom: 1.5rem;
}

.loading-text {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.loading-subtitle {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0;
}

.loading-progress {
  width: 100%;
  height: 4px;
  background: #e2e8f0;
  border-radius: 2px;
  margin-top: 1rem;
  overflow: hidden;
}

.loading-progress-bar {
  height: 100%;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border-radius: 2px;
  animation: progress 2s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes progress {
  0% { width: 0%; transform: translateX(-100%); }
  50% { width: 70%; transform: translateX(0%); }
  100% { width: 100%; transform: translateX(100%); }
}

.loading-dots {
  display: inline-flex;
  gap: 4px;
  margin-left: 4px;
}

.loading-dot {
  width: 4px;
  height: 4px;
  background: #6366f1;
  border-radius: 50%;
  animation: loadingDots 1.4s infinite ease-in-out;
}

.loading-dot:nth-child(1) { animation-delay: 0s; }
.loading-dot:nth-child(2) { animation-delay: 0.2s; }
.loading-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes loadingDots {
  0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
  40% { transform: scale(1.2); opacity: 1; }
}

/* Export Buttons */
.export-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn-export {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.btn-export.pdf {
  background: #dc2626;
  color: white;
}

.btn-export.excel {
  background: #059669;
  color: white;
}

.btn-export.print {
  background: #6366f1;
  color: white;
}

.btn-export:hover {
  transform: translateY(-2px);
}

/* Habit Icons */
.habit-icon {
  font-size: 0.8rem;
  margin-right: 0.25rem;
}

/* Modal for habit details */
.habit-detail-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

.habit-detail-modal.show {
  display: flex !important;
}

.habit-detail-content {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  max-width: 600px;
  width: 95%;
  max-height: 85vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes modalSlideIn {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(-50px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.habit-detail-close {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #f3f4f6;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  transition: all 0.2s ease;
  z-index: 10;
}

.habit-detail-close:hover {
  background: #e5e7eb;
  transform: scale(1.1);
}

.habit-detail-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e2e8f0;
}

.habit-detail-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.habit-detail-date {
  color: #64748b;
  font-size: 1rem;
  font-weight: 500;
}

.habit-detail-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  border-radius: 25px;
  font-weight: 600;
  margin-bottom: 1.5rem;
  font-size: 1rem;
}

.habit-detail-status.completed {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  color: #166534;
  border: 2px solid #22c55e;
}

.habit-detail-status.not-completed {
  background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
  color: #991b1b;
  border: 2px solid #ef4444;
}

.habit-detail-status.no-data {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  color: #64748b;
  border: 2px solid #94a3b8;
}

.habit-detail-info {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 1.25rem;
  border-radius: 12px;
  margin-bottom: 1rem;
  border-left: 4px solid #6366f1;
}

.habit-detail-info h4 {
  margin: 0 0 0.75rem 0;
  color: #1e293b;
  font-size: 1.1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.habit-detail-info p {
  margin: 0.5rem 0;
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.5;
}

.habit-detail-info strong {
  color: #1e293b;
  font-weight: 600;
}

/* Edit Mode Styles */
.habit-edit-mode {
  border-left-color: #f59e0b;
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.habit-edit-form {
  display: grid;
  gap: 1rem;
  margin-top: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.9rem;
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 0.75rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.checkbox-group:hover {
  border-color: #6366f1;
  background: #f8fafc;
}

.checkbox-group input[type="checkbox"] {
  width: 20px;
  height: 20px;
  margin: 0;
  cursor: pointer;
}

.checkbox-group label {
  margin: 0;
  cursor: pointer;
  font-weight: 500;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.btn-form {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  min-width: 120px;
}

.btn-form.primary {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  color: white;
}

.btn-form.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.btn-form.secondary {
  background: #f1f5f9;
  color: #475569;
  border: 2px solid #e2e8f0;
}

.btn-form.secondary:hover {
  background: #e2e8f0;
  border-color: #cbd5e1;
}

.btn-form.danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.btn-form.danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

/* Success Animation */
.save-success {
  position: fixed;
  top: 20px;
  right: 20px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
  z-index: 10000;
  animation: slideInRight 0.5s ease-out, slideOutRight 0.5s ease-in 2.5s forwards;
}

/* Recap cards */
.recap-grid {display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:8px;}
.recap-card {border:1px solid #e2e8f0;border-radius:10px;padding:6px 6px 8px;position:relative;background:#f8fafc;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;min-height:70px;transition:.2s box-shadow,.2s transform;cursor:default;}
.recap-card.completed{background:#dcfce7;border-color:#bbf7d0;}
.recap-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.08);transform:translateY(-2px);}
.recap-day {font-size:.65rem;font-weight:600;color:#334155;line-height:1; padding:2px 6px; border-radius:12px; background:#e2e8f0; margin-bottom:4px;}
.recap-card.completed .recap-day{background:#86efac;color:#065f46;}
.recap-value {font-size:.85rem;font-weight:700;color:#1e293b;line-height:1;margin-bottom:2px;}
.recap-card.completed .recap-value{color:#065f46;}
.recap-detail {font-size:.55rem;color:#64748b;line-height:1.05;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;width:100%;}
.recap-category-badge{font-size:.6rem;padding:4px 8px;border-radius:20px;background:#f1f5f9;color:#334155;font-weight:600;}
.recap-category-badge strong{color:#6366f1;}
/* Daily cards grid (5 columns desktop, responsive) */
.daily-cards-wrapper {background: #fff; border-radius: 18px; padding: 1.75rem 1.5rem 1.9rem; box-shadow: 0 4px 18px rgba(0,0,0,0.06); margin-bottom:2.2rem;}
.daily-cards-grid {display:grid;grid-template-columns:repeat(5,1fr);gap:18px;}
@media (max-width:1200px){.daily-cards-grid{grid-template-columns:repeat(4,1fr);} }
@media (max-width:950px){.daily-cards-grid{grid-template-columns:repeat(3,1fr);} }
@media (max-width:680px){.daily-cards-grid{grid-template-columns:repeat(2,1fr);} }
@media (max-width:420px){.daily-cards-grid{grid-template-columns:repeat(1,1fr);} }
.day-card {position:relative; background:#fbe4d4; border-radius:32px; padding:16px 16px 18px; display:flex; flex-direction:column; min-height:165px; box-shadow:0 6px 14px -4px rgba(0,0,0,0.08); transition:.25s transform,.25s box-shadow;}
.day-card:hover {transform:translateY(-4px); box-shadow:0 10px 22px -6px rgba(0,0,0,0.12);} 
.day-card-inner {flex:1; background:#ffffff; border-radius:18px; padding:14px 14px 16px; display:flex; flex-direction:column; overflow:visible;}
.day-number {position:absolute; top:8px; left:50%; transform:translateX(-50%); font-weight:700; font-size:1.05rem; color:#1e293b; letter-spacing:.5px;}
.day-empty .day-card-inner {justify-content:center; align-items:center; color:#94a3b8; font-size:.7rem;}
.habit-line {display:flex; align-items:flex-start; gap:6px; font-size:.78rem; line-height:1.25; margin-bottom:6px; color:#334155; font-weight:500; word-break:break-word;}
.habit-line:last-child {margin-bottom:0;}
.habit-line .h-icon {font-size:.85rem; line-height:1;}
.habit-line .h-label {font-weight:600; color:#111827; font-size:.8rem;}
.day-complete-indicator {position:absolute; top:6px; right:10px; font-size:.7rem; font-weight:600; background:#dcfce7; color:#065f46; padding:2px 7px; border-radius:12px; box-shadow:0 0 0 2px #f0fdf4;}
.day-partial-indicator {position:absolute; top:6px; right:10px; font-size:.7rem; font-weight:600; background:#ffeaa7; color:#92400e; padding:2px 7px; border-radius:12px; box-shadow:0 0 0 2px #fff7ed;}
.day-none-indicator {position:absolute; top:6px; right:10px; font-size:.7rem; font-weight:600; background:#e2e8f0; color:#475569; padding:2px 7px; border-radius:12px;}
.day-card.completed {background:#d9fbe7;}

.day-card.completed .day-card-inner {background:#ffffff;}
</style>

<div class="monthly-container" x-data="monthlyReportApp()" x-init="init()">

  <!-- Month Selector -->
  <div class="month-selector">
    <div class="month-selector-content">
      <label>Pilih Bulan:</label>
      <input type="month" 
             x-model="selectedMonth" 
             :max="getCurrentMonth()"
             @change="loadMonthlyData()">
      <button class="btn-current-month" @click="goToCurrentMonth()">
        Bulan Ini
      </button>
      <?php if (!empty($students)): ?>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
          <label for="studentSelect" style="font-weight:600;">Siswa:</label>
          <select id="studentSelect" x-model="selectedStudentId" @change="loadMonthlyData()" style="padding:.55rem .8rem;border:2px solid #e2e8f0;border-radius:8px;min-width:220px;">
            <option value="">-- Pilih Siswa --</option>
            <?php foreach ($students as $s): ?>
              <?php 
                $labelNama = $s['nama'] ?: ($s['nisn'] ?? $s['nis']);
                $labelKelas = $s['kelas_nama'] ?? ($s['kelas'] ?? ($s['kelas_id'] ?? '-'));
              ?>
              <option value="<?= $s['id'] ?>"><?= esc($labelNama) ?> (<?= esc($labelKelas) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
    </div>
  </div>



  <!-- Export Buttons -->
  <div class="export-buttons">
    <button class="btn-export pdf" @click="exportToPDF()">
      📄 Export PDF
    </button>
    <button class="btn-export excel" @click="exportToExcel()">
      📊 Export Excel
    </button>
    <button class="btn-export print" @click="printReport()">
      🖨️ Print
    </button>
  </div>

  <!-- Loading State -->
  <div x-show="loading" class="loading">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <div class="loading-text">
        Memuat Data Kebiasaan
        <div class="loading-dots">
          <div class="loading-dot"></div>
          <div class="loading-dot"></div>
          <div class="loading-dot"></div>
        </div>
      </div>
      <p class="loading-subtitle">Mohon tunggu sebentar...</p>
      <div class="loading-progress">
        <div class="loading-progress-bar"></div>
      </div>
    </div>
  </div>

  <!-- Table Container -->
  <div x-show="!loading" class="table-container">
    <h2 class="table-title" x-text="getTableTitle()"></h2>
    <template x-if="!selectedStudentId && document.getElementById('studentSelect')">
      <div style="text-align:center;padding:1.2rem;border:2px dashed #cbd5e1;border-radius:12px;margin-bottom:1rem;color:#475569;font-weight:500;">
        Pilih siswa terlebih dahulu untuk menampilkan data kebiasaan.
      </div>
    </template>
    
  <div class="monthly-table-scroll">
  <table class="monthly-table" id="monthlyTable" x-show="!document.getElementById('studentSelect') || selectedStudentId">
        <colgroup>
          <col span="1">
          <col span="1">
          <col span="1">
          <col span="1">
          <col class="pray-col">
          <col class="pray-col">
          <col class="pray-col">
            <col class="pray-col">
            <col class="pray-col">
          <col span="1">
          <col span="1">
          <col span="1">
          <col span="1">
          <col span="1">
        </colgroup>
        <thead>
          <tr class="top-header">
            <th rowspan="2" class="date-header">HARI TANGGAL</th>
            <th rowspan="2" class="status-header">STATUS</th>
            <th rowspan="2">BANGUN PAGI</th>
            <th colspan="6" class="worship-group">BERIBADAH</th>
            <th rowspan="2">BEROLAHRAGA</th>
            <th rowspan="2">MAKAN SEHAT</th>
            <th rowspan="2">GEMAR BELAJAR</th>
            <th rowspan="2">BERMASYARAKAT</th>
            <th rowspan="2">TIDUR CEPAT</th>
          </tr>
          <tr class="sub-header">
            <th class="pray-header">S</th>
            <th class="pray-header">D</th>
            <th class="pray-header">A</th>
            <th class="pray-header">M</th>
            <th class="pray-header">I</th>
            <th class="lainnya-header">Ibadah Lainnya</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="day in daysInMonth" :key="day">
            <tr>
              <td class="date-cell" :class="[isWeekend(day)?'weekend':'', isToday(day)?'today':'']" x-text="formatDateForDetail(day)"></td>
              <td class="status-cell">
                <div class="status-bar" :data-pct="getDailyPercent(day)">
                  <div class="status-bar-fill" :style="'width:'+getDailyPercent(day)+'%'" :aria-valuenow="getDailyPercent(day)" aria-valuemin="0" aria-valuemax="100"></div>
                  <span class="status-text" x-text="getDailyStatus(day)"></span>
                </div>
              </td>
              <td :class="getBangunCellClass(day)" @click="showHabitDetail(day,1)" x-html="getBangunCellContent(day)"></td>
              <td class="pray-cell" :class="getPrayerClass(day,'subuh')" @click="showHabitDetail(day,2)" x-html="getPrayerMark(day,'subuh')"></td>
              <td class="pray-cell" :class="getPrayerClass(day,'dzuhur')" @click="showHabitDetail(day,2)" x-html="getPrayerMark(day,'dzuhur')"></td>
              <td class="pray-cell" :class="getPrayerClass(day,'ashar')" @click="showHabitDetail(day,2)" x-html="getPrayerMark(day,'ashar')"></td>
              <td class="pray-cell" :class="getPrayerClass(day,'maghrib')" @click="showHabitDetail(day,2)" x-html="getPrayerMark(day,'maghrib')"></td>
              <td class="pray-cell" :class="getPrayerClass(day,'isya')" @click="showHabitDetail(day,2)" x-html="getPrayerMark(day,'isya')"></td>
              <td class="worship-others" :class="getWorshipOthersClass(day)" @click="showHabitDetail(day,2)" x-text="getWorshipOthers(day)"></td>
              <td :class="getHabitSimpleClass(day,3)" @click="showHabitDetail(day,3)" x-html="getHabitSimpleMark(day,3)"></td>
              <td :class="getHabitSimpleClass(day,4)" @click="showHabitDetail(day,4)" x-html="getHabitSimpleMark(day,4)"></td>
              <td :class="getHabitSimpleClass(day,5)" @click="showHabitDetail(day,5)" x-html="getHabitSimpleMark(day,5)"></td>
              <td :class="getHabitSimpleClass(day,6)" @click="showHabitDetail(day,6)" x-html="getHabitSimpleMark(day,6)"></td>
              <td :class="getTidurCellClass(day)" @click="showHabitDetail(day,7)" x-html="getTidurCellContent(day)"></td>
            </tr>
          </template>
        </tbody>
      </table>
  </div>

    <!-- Legend -->
    <div class="legend">
      <div class="legend-item">
        <div class="legend-color completed"></div>
        <span>Selesai ✓</span>
      </div>
      <div class="legend-item">
        <div class="legend-color not-completed"></div>
        <span>Belum Selesai ✗</span>
      </div>
      <div class="legend-item">
        <div class="legend-color no-data"></div>
        <span>Tidak Ada Data -</span>
      </div>
    </div>
  </div>

  <!-- Habit Detail Modal -->
  <div class="habit-detail-modal" :class="showDetailModal ? 'show' : ''" @click.self="closeDetailModal()">
    <div class="habit-detail-content">
      <button class="habit-detail-close" @click="closeDetailModal()">✕</button>
      
      <div class="habit-detail-header">
        <h2 class="habit-detail-title">
          <span x-text="selectedHabitDetail.habitIcon"></span>
          <span x-text="selectedHabitDetail.habitName"></span>
        </h2>
        <p class="habit-detail-date" x-text="selectedHabitDetail.dateText"></p>
      </div>

      <div class="habit-detail-status" :class="selectedHabitDetail.statusClass">
        <span x-text="selectedHabitDetail.statusIcon"></span>
        <span x-text="selectedHabitDetail.statusText"></span>
      </div>

      <!-- View Mode -->
      <div x-show="!editMode">
        <div x-show="selectedHabitDetail.hasData" class="habit-detail-info">
          <h4>📋 Detail Aktivitas</h4>
          <div x-show="selectedHabitDetail.time">
            <p><strong>⏰ Waktu:</strong> <span x-text="selectedHabitDetail.time"></span></p>
          </div>
          <div x-show="selectedHabitDetail.duration">
            <p><strong>⏱️ Durasi:</strong> <span x-text="selectedHabitDetail.duration + ' menit'"></span></p>
          </div>
          <div x-show="selectedHabitDetail.notes">
            <p><strong>📝 Catatan:</strong> <span x-text="selectedHabitDetail.notes"></span></p>
          </div>
        </div>

        <div x-show="!selectedHabitDetail.hasData" class="habit-detail-info">
          <h4>ℹ️ Informasi</h4>
          <p x-show="selectedHabitDetail.isFuture">Tanggal ini belum tiba. Data belum bisa diinput.</p>
          <p x-show="!selectedHabitDetail.isFuture && !selectedHabitDetail.hasData">Tidak ada data yang tercatat untuk tanggal ini. Mulai catat kebiasaan Anda untuk membangun karakter yang lebih baik!</p>
        </div>

        <!-- Action Buttons for View Mode -->
        <div class="form-actions" x-show="!selectedHabitDetail.isFuture">
          <button x-show="selectedHabitDetail.hasData" @click="enterEditMode()" class="btn-form primary">
            ✏️ Edit Data
          </button>
          <button x-show="!selectedHabitDetail.hasData" @click="enterEditMode()" class="btn-form primary">
            ➕ Tambah Data
          </button>
          <button @click="closeDetailModal()" class="btn-form secondary">
            Tutup
          </button>
        </div>
      </div>

      <!-- Edit Mode -->
      <div x-show="editMode">
        <div class="habit-detail-info habit-edit-mode">
          <h4>✏️ Edit Kebiasaan</h4>
          
          <form @submit.prevent="saveHabitData()" class="habit-edit-form">
            <!-- Completion Status -->
            <div class="form-group">
              <div class="checkbox-group">
                <input type="checkbox" id="completed" x-model="editForm.completed">
                <label for="completed">Kebiasaan telah diselesaikan</label>
              </div>
            </div>

            <!-- Time Input (for wake up and sleep habits) -->
            <div x-show="selectedHabitDetail.habitId === 1 || selectedHabitDetail.habitId === 7" class="form-group">
              <label x-text="selectedHabitDetail.habitId === 1 ? 'Waktu Bangun:' : 'Waktu Tidur:'"></label>
              <input type="time" x-model="editForm.time" :disabled="!editForm.completed">
            </div>

            <!-- Duration Input (for exercise) -->
            <div x-show="selectedHabitDetail.habitId === 3" class="form-group">
              <label>Durasi Olahraga (menit):</label>
              <input type="number" x-model="editForm.duration" min="1" max="300" placeholder="30" :disabled="!editForm.completed">
            </div>

            <!-- Notes Input -->
            <div class="form-group">
              <label>Catatan Tambahan:</label>
              <textarea x-model="editForm.notes" placeholder="Tambahkan catatan tentang kebiasaan ini..." :disabled="!editForm.completed"></textarea>
            </div>

            <!-- Prayer Checkboxes (for religious habits) -->
            <div x-show="selectedHabitDetail.habitId === 2 && editForm.completed" class="form-group">
              <label>Sholat yang Dilaksanakan:</label>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.5rem; margin-top: 0.5rem;">
                <template x-for="prayer in prayers" :key="prayer.key">
                  <div class="checkbox-group" style="padding: 0.5rem; font-size: 0.85rem;">
                    <input type="checkbox" :id="'prayer_' + prayer.key" x-model="editForm.prayers[prayer.key]">
                    <label :for="'prayer_' + prayer.key" x-text="prayer.name"></label>
                  </div>
                </template>
              </div>
            </div>

            <!-- Action Buttons for Edit Mode -->
            <div class="form-actions">
              <button type="submit" class="btn-form primary" :disabled="saving">
                <span x-show="!saving">💾 Simpan</span>
                <span x-show="saving">⏳ Menyimpan...</span>
              </button>
              <button type="button" @click="cancelEdit()" class="btn-form secondary" :disabled="saving">
                Batal
              </button>
              <button x-show="selectedHabitDetail.hasData" type="button" @click="deleteHabitData()" class="btn-form danger" :disabled="saving">
                🗑️ Hapus
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tips untuk setiap kebiasaan -->
      <div x-show="!editMode" class="habit-detail-info" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b;">
        <h4>💡 Tips</h4>
        <div x-show="selectedHabitDetail.habitName === 'Bangun Pagi'">
          <p>Bangun pagi membantu memulai hari dengan semangat. Cobalah tidur lebih awal dan siapkan alarm.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Beribadah'">
          <p>Beribadah secara rutin membantu memperkuat karakter spiritual dan memberikan ketenangan batin.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Berolahraga'">
          <p>Olahraga minimal 30 menit sehari dapat meningkatkan kesehatan fisik dan mental Anda.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Makan Sehat'">
          <p>Konsumsi makanan bergizi seimbang dengan sayur, buah, dan protein untuk tumbuh kembang optimal.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Gemar Belajar'">
          <p>Belajar hal baru setiap hari membantu mengembangkan potensi diri dan memperluas wawasan.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Bermasyarakat'">
          <p>Berinteraksi dan membantu sesama membangun rasa empati dan keterampilan sosial yang baik.</p>
        </div>
        <div x-show="selectedHabitDetail.habitName === 'Tidur Cepat'">
          <p>Tidur cukup dan teratur penting untuk pemulihan tubuh dan konsentrasi belajar keesokan harinya.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function monthlyReportApp() {
  return {
    // Data properties
    selectedMonth: '',
    monthlyData: {},
  selectedStudentId: '<?php if (!empty($students)) { echo ''; } ?>', // default kosong (admin pilih siswa)
    loading: false,
    daysInMonth: [],
    habits: [
      { id: 1, name: 'Bangun Pagi', icon: '🌅' },
      { id: 2, name: 'Beribadah', icon: '🤲' },
      { id: 3, name: 'Berolahraga', icon: '⚽' },
      { id: 4, name: 'Makan Sehat', icon: '🥗' },
      { id: 5, name: 'Gemar Belajar', icon: '📚' },
      { id: 6, name: 'Bermasyarakat', icon: '🤝' },
      { id: 7, name: 'Tidur Cepat', icon: '🌙' }
    ],
  // Removed category recap & statistics properties
    
    // Modal for habit details
    showDetailModal: false,
    editMode: false,
    saving: false,
    selectedHabitDetail: {
      habitId: 0,
      habitName: '',
      habitIcon: '',
      dateText: '',
      statusClass: '',
      statusIcon: '',
      statusText: '',
      hasData: false,
      isFuture: false,
      time: '',
      duration: '',
      notes: '',
      selectedDate: ''
    },
    
    // Edit form data
    editForm: {
      completed: false,
      time: '',
      duration: '',
      notes: '',
      prayers: {
        subuh: false,
        dzuhur: false,
        ashar: false,
        maghrib: false,
        isya: false
      }
    },
    
    // Prayer options
    prayers: [
      { key: 'subuh', name: 'Subuh' },
      { key: 'dzuhur', name: 'Dzuhur' },
      { key: 'ashar', name: 'Ashar' },
      { key: 'maghrib', name: 'Maghrib' },
      { key: 'isya', name: 'Isya' }
    ],
    
    // Initialize
    init() {
      console.log('📊 Monthly Report App initialized');
      this.selectedMonth = this.getCurrentMonth();
      // Jika ada dropdown siswa (admin mode) dan belum pilih, jangan fetch dulu
      const hasStudentDropdown = !!document.getElementById('studentSelect');
      if (hasStudentDropdown) {
        console.log('🛈 Mode admin/walikelas: menunggu pemilihan siswa sebelum load data');
        this.loading = false;
      } else {
        this.loadMonthlyData();
      }
    },
    
    // Get current month in YYYY-MM format
    getCurrentMonth() {
      const now = new Date();
      return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
    },
    
    // Navigate to current month
    goToCurrentMonth() {
      this.selectedMonth = this.getCurrentMonth();
      this.loadMonthlyData();
    },
    
    // Generate table title
    getTableTitle() {
      if (!this.selectedMonth) return 'Rekap Bulanan';
      
      const [year, month] = this.selectedMonth.split('-');
      const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
      ];
      
      return `Rekap Kebiasaan ${monthNames[parseInt(month) - 1]} ${year}`;
    },
    
    // Load monthly data
    async loadMonthlyData() {
      if (!this.selectedMonth) return;
      
      this.loading = true;
      
      try {
        // Generate days for the month
        this.generateDaysInMonth();
        
        // Simulate loading progress for better UX
        await this.simulateLoadingProgress();
        
        // Fetch data from server
        let url = `<?= base_url('siswa/habits/monthly-data') ?>?month=${this.selectedMonth}`;
        if (this.selectedStudentId) {
          console.log('🆔 selectedStudentId before fetch =', this.selectedStudentId);
          url += `&student_id=${this.selectedStudentId}`;
        }
        console.log('🔄 Fetching monthly data from:', url);
        
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          credentials: 'same-origin'
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', [...response.headers.entries()]);
        
        if (!response.ok) {
          const errorText = await response.text();
          console.error('❌ HTTP Error:', response.status, response.statusText);
          console.error('❌ Error response:', errorText);
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('✅ API Response received:', result);
        
        this.monthlyData = result.data || {};
        if (Object.keys(this.monthlyData).length === 0) {
          console.log('ℹ️ Tidak ada data kebiasaan untuk parameter ini.');
        }
        
        console.log('📊 Monthly data loaded:', this.monthlyData);
        console.log('📅 Available date keys:', Object.keys(this.monthlyData));
        console.log('🎯 Looking for date: 2025-08-21');
        console.log('🎯 Date exists:', '2025-08-21' in this.monthlyData ? 'YES' : 'NO');
        
        if ('2025-08-21' in this.monthlyData) {
          console.log('📋 Data for 2025-08-21:', this.monthlyData['2025-08-21']);
        }
  // Category recap removed
        
        // Add small delay for smooth UX
        await new Promise(resolve => setTimeout(resolve, 300));
        
      } catch (error) {
        console.error('💥 Error loading monthly data:', error);
        console.error('💥 Error details:', {
          message: error.message,
          stack: error.stack,
          selectedMonth: this.selectedMonth
        });
        
        // Show error to user
        alert(`Gagal memuat data kebiasaan untuk bulan ${this.selectedMonth}.\nError: ${error.message}\n\nSilakan refresh halaman atau hubungi administrator.`);
        
        this.monthlyData = {};
  // stats removed
      } finally {
        this.loading = false;
      }
    },
  // Category recap functions removed
    
    // Simulate loading progress for better UX
    async simulateLoadingProgress() {
      const steps = [
        { delay: 200, message: 'Menghubungkan ke server...' },
        { delay: 300, message: 'Memuat data kebiasaan...' },
        { delay: 250, message: 'Memproses statistik...' },
        { delay: 200, message: 'Menyiapkan tampilan...' }
      ];
      
      for (const step of steps) {
        await new Promise(resolve => setTimeout(resolve, step.delay));
        // You can update loading message here if needed
      }
    },
    
    // Generate days in selected month
    generateDaysInMonth() {
      if (!this.selectedMonth) return;
      
      const [year, month] = this.selectedMonth.split('-');
      const daysInMonth = new Date(year, month, 0).getDate();
      
      this.daysInMonth = [];
      for (let day = 1; day <= daysInMonth; day++) {
        this.daysInMonth.push(day);
      }
    },
    
    // Check if day is weekend
    isWeekend(day) {
      const [year, month] = this.selectedMonth.split('-');
      const date = new Date(year, month - 1, day);
      const dayOfWeek = date.getDay();
      return dayOfWeek === 0 || dayOfWeek === 6; // Sunday or Saturday
    },
    
    // Check if day is today
    isToday(day) {
      const [year, month] = this.selectedMonth.split('-');
      const today = new Date();
      
      return today.getFullYear() == year && 
             (today.getMonth() + 1) == month && 
             today.getDate() == day;
    },
    
    // Check if day is in future
    isFuture(day) {
      const [year, month] = this.selectedMonth.split('-');
      const date = new Date(year, month - 1, day);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      return date > today;
    },
    
    // Format date for table display
    formatDateForTable(day) {
      const [year, month] = this.selectedMonth.split('-');
      const date = new Date(year, month - 1, day);
      const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
      
      return `${day}\n${dayNames[date.getDay()]}`;
    },
    
    // Get habit cell CSS class
    getHabitCellClass(day, habitId) {
      if (this.isFuture(day)) {
        return 'future';
      }
      
      const dateKey = this.getDateKey(day);
      const dayData = this.monthlyData[dateKey];
      
      if (!dayData) {
        return 'no-data';
      }
      
      const habitData = dayData[`habit_${habitId}`];
      
      if (!habitData) {
        return 'no-data';
      }
      
      return habitData.completed ? 'completed' : 'not-completed';
    },
    
    // Get habit cell title (tooltip)
    getHabitCellTitle(day, habitId) {
      const habitName = this.habits[habitId - 1]?.name || 'Kebiasaan';
      
      if (this.isFuture(day)) {
        return `${habitName} - Belum tiba`;
      }
      
      const dateKey = this.getDateKey(day);
      const dayData = this.monthlyData[dateKey];
      
      if (!dayData) {
        return `${habitName} - Tidak ada data`;
      }
      
      const habitData = dayData[`habit_${habitId}`];
      
      if (!habitData) {
        return `${habitName} - Tidak ada data`;
      }
      
      const status = habitData.completed ? 'Selesai' : 'Belum selesai';
      let details = '';
      
      if (habitData.time) {
        details += ` (${habitData.time})`;
      }
      if (habitData.duration) {
        details += ` (${habitData.duration} menit)`;
      }
      if (habitData.notes) {
        details += ` - ${habitData.notes}`;
      }
      
      return `${habitName} - ${status}${details}`;
    },

    // NEW TABLE HELPERS
    getDailyStatus(day){
      if(this.isFuture(day)) return '-';
      const dateKey=this.getDateKey(day); const dd=this.monthlyData[dateKey];
      let done=0;
      if(dd){
        for(let i=1;i<=7;i++){
          const h=dd[`habit_${i}`];
            if(h && h.completed) done++;
        }
      }
      return `${done}/7`;
    },
    getDailyPercent(day){
      if(this.isFuture(day)) return 0;
      const dateKey=this.getDateKey(day); const dd=this.monthlyData[dateKey];
      let done=0;
      if(dd){
        for(let i=1;i<=7;i++){
          const h=dd[`habit_${i}`];
          if(h && h.completed) done++;
        }
      }
      return Math.round((done/7)*100);
    },
    // Bangun Pagi cell: show time if exists & mark good/bad
    getBangunCellContent(day){
      if(this.isFuture(day)) return '';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_1; if(!h||!h.time){
        return this.isPast(day)? this.getRedXIcon():'';
      }
      return h.time<= '06:00'? `<span>${h.time}</span>`: `<span style='color:#dc2626;'>${h.time}</span>`;
    },
    getBangunCellClass(day){
      if(this.isFuture(day)) return 'time-habit no-data';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_1; if(!h||!h.time) return 'time-habit no-data';
      return h.time<= '06:00'? 'time-habit good':'time-habit bad';
    },
    // Tidur Cepat cell
    getTidurCellContent(day){
      if(this.isFuture(day)) return '';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_7; if(!h||!h.time){
        return this.isPast(day)? this.getRedXIcon():'';
      }
      return h.time<= '21:00'? `<span>${h.time}</span>`: `<span style='color:#dc2626;'>${h.time}</span>`;
    },
    getTidurCellClass(day){
      if(this.isFuture(day)) return 'time-habit no-data';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_7; if(!h||!h.time) return 'time-habit no-data';
      return h.time<= '21:00'? 'time-habit good':'time-habit bad';
    },
    // Worship prayer mark (S D A M I)
    getPrayerMark(day, prayer){
      if(this.isFuture(day)) return '';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_2; if(!h||!h.notes) {
        // show red X icon for past days with missing prayer data
        return this.isPast(day)? this.getRedXIcon():'';
      }
      const txt=h.notes.toLowerCase();
      if(txt.includes(prayer)) return '✓';
      // Past day and not done -> red X
      return this.isPast(day)? this.getRedXIcon():'';
    },
    getRedXIcon(){
      return `<span class='miss-x' style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:9999px;background:#fecaca;color:#b91c1c;font-size:11px;font-weight:600;">×</span>`;
    },
    isPast(day){
      const today=new Date();
      const [y,m]=this.selectedMonth.split('-');
      const currentMonthKey=`${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}`;
      const todayDay = today.getDate();
      if(this.selectedMonth<currentMonthKey) return true; // previous month entirely past
      if(this.selectedMonth>currentMonthKey) return false; // future month
      return day < todayDay; // same month, earlier day
    },
    getPrayerClass(day, prayer){
      if(this.isFuture(day)) return 'pray-cell';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_2; const txt=(h?.notes||'').toLowerCase();
      if(txt.includes(prayer)) return 'pray-cell done';
      return 'pray-cell '+(this.isPast(day)? 'miss':'');
    },
    getWorshipOthersClass(day){
      if(this.isFuture(day)) return 'worship-others no-data';
      const content=this.getWorshipOthers(day);
      if(content) return 'worship-others has-data';
      return 'worship-others no-data';
    },
  getWorshipOthers(day){
      if(this.isFuture(day)) return '';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.habit_2; if(!h||!h.notes) return '';
      let txt=h.notes;
      ['subuh','dzuhur','ashar','maghrib','isya'].forEach(p=>{txt=txt.replace(new RegExp(p,'ig'),'');});
  txt=txt.replace(/sholat:?/ig,'');
  // remove leftover commas and extra spaces
  txt=txt.split(',').map(s=>s.trim()).filter(s=>s.length>0).join(', ');
  txt=txt.replace(/\s+/g,' ').trim();
  return txt;
    },
    // Simple habits (3-6) format teks: "Catatan (X menit)" atau hanya salah satunya. Fallback ✓ / ✗.
    getHabitSimpleMark(day, habitId){
      if(this.isFuture(day)) return '';
      const dateKey=this.getDateKey(day);
      const h=this.monthlyData[dateKey]?.[`habit_${habitId}`];
      if(!h){
        return this.isPast(day)? this.getRedXIcon():'';
      }
      let notes = (h.notes||'').trim();
      // Normalisasi pemisah koma (hapus spasi ganda)
      notes = notes.replace(/\s*,\s*/g, ', ').replace(/\s{2,}/g,' ');
      let durText = '';
      if(h.duration){
        const num = parseFloat(h.duration);
        if(!isNaN(num)){
          durText = (Number.isInteger(num)? num.toString(): num.toFixed(1)) + ' menit';
        } else {
          // Jika tidak numerik, tampilkan apa adanya tanpa 'm'
          durText = h.duration.toString().replace(/m$/,'') + ' menit';
        }
      }
      let combined='';
      if(notes && durText) combined = notes + ' ('+durText+')';
      else if(notes) combined = notes;
      else if(durText) combined = durText;
      if(combined){
        if(combined.length>50) combined = combined.substring(0,47)+'…';
        return combined;
      }
      if(h.completed) return '✓';
      return this.isPast(day)? this.getRedXIcon():'✗';
    },
    getHabitSimpleClass(day, habitId){
      if(this.isFuture(day)) return 'simple-habit no-data';
      const dateKey=this.getDateKey(day); const h=this.monthlyData[dateKey]?.[`habit_${habitId}`]; if(!h) return 'simple-habit no-data';
      return 'simple-habit '+(h.completed? 'completed':'not-completed');
    },
    
    // Get date key for data lookup
    getDateKey(day) {
      const [year, month] = this.selectedMonth.split('-');
      return `${year}-${month}-${String(day).padStart(2, '0')}`;
    },
    
    // Show habit detail (enhanced with modal)
    showHabitDetail(day, habitId) {
      if (this.isFuture(day)) {
        this.selectedHabitDetail = {
          habitId: habitId,
          habitName: this.habits[habitId - 1]?.name || 'Kebiasaan',
          habitIcon: this.habits[habitId - 1]?.icon || '📋',
          dateText: this.formatDateForDetail(day),
          statusClass: 'no-data',
          statusIcon: '📅',
          statusText: 'Belum Tiba',
          hasData: false,
          isFuture: true,
          time: '',
          duration: '',
          notes: '',
          selectedDate: this.getDateKey(day)
        };
        this.showDetailModal = true;
        return;
      }
      
      const dateKey = this.getDateKey(day);
      const dayData = this.monthlyData[dateKey];
      const habitName = this.habits[habitId - 1]?.name || 'Kebiasaan';
      const habitIcon = this.habits[habitId - 1]?.icon || '📋';
      
      if (!dayData || !dayData[`habit_${habitId}`]) {
        this.selectedHabitDetail = {
          habitId: habitId,
          habitName: habitName,
          habitIcon: habitIcon,
          dateText: this.formatDateForDetail(day),
          statusClass: 'no-data',
          statusIcon: '❌',
          statusText: 'Tidak Ada Data',
          hasData: false,
          isFuture: false,
          time: '',
          duration: '',
          notes: '',
          selectedDate: dateKey
        };
        this.showDetailModal = true;
        return;
      }
      
      const habitData = dayData[`habit_${habitId}`];
      
      this.selectedHabitDetail = {
        habitId: habitId,
        habitName: habitName,
        habitIcon: habitIcon,
        dateText: this.formatDateForDetail(day),
        statusClass: habitData.completed ? 'completed' : 'not-completed',
        statusIcon: habitData.completed ? '✅' : '❌',
        statusText: habitData.completed ? 'Selesai' : 'Belum Selesai',
        hasData: true,
        isFuture: false,
        time: habitData.time || '',
        duration: habitData.duration || '',
        notes: habitData.notes || '',
        selectedDate: dateKey
      };
      
      this.showDetailModal = true;
    },
    
    // Format date for detail modal
    formatDateForDetail(day) {
      const [year, month] = this.selectedMonth.split('-');
      const date = new Date(year, month - 1, day);
      let dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
      const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
      return `${dayNames[date.getDay()]} ${day} ${monthNames[parseInt(month)-1]} ${year}`;
    },
    
    // Close detail modal
    closeDetailModal() {
      this.showDetailModal = false;
      this.editMode = false;
      this.saving = false;
      this.resetEditForm();
    },
    
    // Enter edit mode
    enterEditMode() {
      this.editMode = true;
      
      // Populate form with existing data
      if (this.selectedHabitDetail.hasData) {
        this.editForm.completed = this.selectedHabitDetail.statusClass === 'completed';
        this.editForm.time = this.selectedHabitDetail.time;
        this.editForm.duration = this.selectedHabitDetail.duration;
        
        // Parse notes for prayer data
        const notes = this.selectedHabitDetail.notes;
        if (notes && notes.includes('Sholat:')) {
          const parts = notes.split('Sholat:');
          if (parts.length > 1) {
            const prayerPart = parts[1].split('.')[0] || parts[1].split(',')[0];
            const prayerNames = prayerPart.toLowerCase();
            
            this.editForm.prayers.subuh = prayerNames.includes('subuh');
            this.editForm.prayers.dzuhur = prayerNames.includes('dzuhur');
            this.editForm.prayers.ashar = prayerNames.includes('ashar');
            this.editForm.prayers.maghrib = prayerNames.includes('maghrib');
            this.editForm.prayers.isya = prayerNames.includes('isya');
            
            // Remove prayer part from notes
            if (parts.length > 1 && parts[1].includes('.')) {
              this.editForm.notes = parts[1].split('.').slice(1).join('.').trim();
            } else {
              this.editForm.notes = '';
            }
          } else {
            this.editForm.notes = notes;
          }
        } else {
          this.editForm.notes = notes;
        }
      } else {
        this.resetEditForm();
        this.editForm.completed = false;
      }
    },
    
    // Cancel edit mode
    cancelEdit() {
      this.editMode = false;
      this.resetEditForm();
    },
    
    // Reset edit form
    resetEditForm() {
      this.editForm = {
        completed: false,
        time: '',
        duration: '',
        notes: '',
        prayers: {
          subuh: false,
          dzuhur: false,
          ashar: false,
          maghrib: false,
          isya: false
        }
      };
    },
    
    // Save habit data
    async saveHabitData() {
      this.saving = true;
      
      try {
        // Prepare data to send
        const habitData = {
          student_id: null, // Will be resolved on server
          habit_id: this.selectedHabitDetail.habitId,
          log_date: this.selectedHabitDetail.selectedDate,
          completed: this.editForm.completed,
          time: this.editForm.time || null,
          duration: this.editForm.duration || null,
          notes: this.prepareNotesForSave()
        };
        
        console.log('💾 Saving habit data:', habitData);
        
        // Send to server
        const response = await fetch('<?= base_url('siswa/habits/save') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(habitData),
          credentials: 'same-origin'
        });
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.status === 'success') {
          // Show success message
          this.showSuccessMessage('Data kebiasaan berhasil disimpan!');
          
          // Reload monthly data
          await this.loadMonthlyData();
          
          // Close modal
          this.closeDetailModal();
        } else {
          throw new Error(result.message || 'Gagal menyimpan data');
        }
        
      } catch (error) {
        console.error('💥 Error saving habit data:', error);
        alert(`Gagal menyimpan data kebiasaan.\nError: ${error.message}\n\nSilakan coba lagi.`);
      } finally {
        this.saving = false;
      }
    },
    
    // Delete habit data
    async deleteHabitData() {
      if (!confirm('Apakah Anda yakin ingin menghapus data kebiasaan ini?')) {
        return;
      }
      
      this.saving = true;
      
      try {
        const response = await fetch('<?= base_url('siswa/habits/delete') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            habit_id: this.selectedHabitDetail.habitId,
            log_date: this.selectedHabitDetail.selectedDate
          }),
          credentials: 'same-origin'
        });
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.status === 'success') {
          this.showSuccessMessage('Data kebiasaan berhasil dihapus!');
          await this.loadMonthlyData();
          this.closeDetailModal();
        } else {
          throw new Error(result.message || 'Gagal menghapus data');
        }
        
      } catch (error) {
        console.error('💥 Error deleting habit data:', error);
        alert(`Gagal menghapus data kebiasaan.\nError: ${error.message}\n\nSilakan coba lagi.`);
      } finally {
        this.saving = false;
      }
    },
    
    // Prepare notes for saving
    prepareNotesForSave() {
      let notes = '';
      
      // Add prayer information for religious habits
      if (this.selectedHabitDetail.habitId === 2 && this.editForm.completed) {
        const selectedPrayers = Object.keys(this.editForm.prayers)
          .filter(key => this.editForm.prayers[key])
          .map(key => {
            const prayer = this.prayers.find(p => p.key === key);
            return prayer ? prayer.name : key;
          });
        
        if (selectedPrayers.length > 0) {
          notes += `Sholat: ${selectedPrayers.join(', ')}`;
          if (this.editForm.notes.trim()) {
            notes += `. ${this.editForm.notes.trim()}`;
          }
        } else {
          notes = this.editForm.notes.trim();
        }
      } else {
        notes = this.editForm.notes.trim();
      }
      
      return notes || null;
    },
    
    // Show success message
    showSuccessMessage(message) {
      const successEl = document.createElement('div');
      successEl.className = 'save-success';
      successEl.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span>✅</span>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(successEl);
      
      setTimeout(() => {
        if (successEl.parentNode) {
          successEl.parentNode.removeChild(successEl);
        }
      }, 3000);
    },
    
  // Removed statistics & category recap helper functions
    
    // Generate mock data for demonstration
    generateMockData() {
      this.monthlyData = {};
      
      this.daysInMonth.forEach(day => {
        if (this.isFuture(day)) return;
        
        // Simulate some days having data
        if (Math.random() > 0.3) {
          const dateKey = this.getDateKey(day);
          this.monthlyData[dateKey] = {};
          
          // Simulate habit completions
          for (let habitId = 1; habitId <= 7; habitId++) {
            const completed = Math.random() > 0.3;
            this.monthlyData[dateKey][`habit_${habitId}`] = {
              completed: completed,
              time: habitId === 1 || habitId === 7 ? '06:00' : null,
              duration: habitId === 3 ? '30' : null,
              notes: completed ? 'Tercatat' : null
            };
          }
        }
      });
    },
    
    // Export functions
    async exportToPDF() {
      if (Object.keys(this.monthlyData).length === 0) {
        alert('Tidak ada data untuk diekspor');
        return;
      }
      alert('Fitur export PDF akan segera tersedia.\n\nUntuk sementara, gunakan Print untuk menyimpan sebagai PDF.');
    },
    
    async exportToExcel() {
      if (Object.keys(this.monthlyData).length === 0) {
        alert('Tidak ada data untuk diekspor');
        return;
      }
      
      // Prepare data for Excel export
      const excelData = [];
      
      // Add headers
      const headers = ['Tanggal', 'Bangun Pagi', 'Beribadah', 'Berolahraga', 'Makan Sehat', 'Gemar Belajar', 'Bermasyarakat', 'Tidur Cepat'];
      excelData.push(headers);
      
      // Add data rows
      this.daysInMonth.forEach(day => {
        if (this.isFuture(day)) return;
        
        const row = [this.formatDateForTable(day).replace('\n', ' ')];
        
        for (let habitId = 1; habitId <= 7; habitId++) {
          const dateKey = this.getDateKey(day);
          const dayData = this.monthlyData[dateKey];
          
          if (!dayData || !dayData[`habit_${habitId}`]) {
            row.push('-');
          } else {
            const habitData = dayData[`habit_${habitId}`];
            row.push(habitData.completed ? '✓' : '✗');
          }
        }
        
        excelData.push(row);
      });
      
      // Create CSV content
      const csvContent = excelData.map(row => 
        row.map(cell => `"${cell}"`).join(',')
      ).join('\n');
      
      // Download CSV file
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', `rekap_habits_${this.selectedMonth}.csv`);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    
    printReport() {
      // Hide export buttons and modal before printing
      const exportButtons = document.querySelector('.export-buttons');
      const modal = document.querySelector('.habit-detail-modal');
      
      if (exportButtons) exportButtons.style.display = 'none';
      if (modal) modal.style.display = 'none';
      
      window.print();
      
      // Restore buttons after printing
      setTimeout(() => {
        if (exportButtons) exportButtons.style.display = 'flex';
        if (modal && this.showDetailModal) modal.style.display = 'flex';
      }, 1000);
    }
  };
}
</script>

<?= $this->endSection() ?>
