<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<style>
/* Calendar Styles */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: #e5e7eb;
    border: 1px solid #e5e7eb;
}

.calendar-header {
    background-color: #374151;
    color: white;
    text-align: center;
    padding: 0.75rem 0.25rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.calendar-day {
    background-color: white;
    min-height: 120px;
    padding: 0.5rem;
    position: relative;
    cursor: pointer;
    transition: all 0.2s ease;
}

.calendar-day:hover {
    background-color: #f9fafb;
    transform: scale(1.02);
}

.calendar-day.weekend {
    background-color: #fef2f2;
}

.calendar-day.today {
    background-color: #dbeafe;
    border: 2px solid #3b82f6;
}

.calendar-day.has-events {
    border-left: 4px solid #10b981;
}

.calendar-day.off {
    background-color: #f3f4f6;
    color: #6b7280;
}

.calendar-day.empty {
    background-color: #f9fafb;
    cursor: default;
}

.day-number {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.day-events {
    font-size: 0.75rem;
    overflow: hidden;
}

.event-badge {
    display: inline-block;
    padding: 0.125rem 0.25rem;
    margin: 0.125rem 0;
    border-radius: 0.25rem;
    color: white;
    font-size: 0.625rem;
    font-weight: 500;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease;
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 90%;
    max-height: 90%;
    overflow-y: auto;
    animation: slideUp 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Legend */
.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.legend-color {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
}

/* Navigation */
.nav-button {
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.nav-button:hover {
    background-color: #2563eb;
}

.nav-button:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
}
</style>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kalender Akademik</h1>
                <p class="text-gray-600">Kelola jadwal libur, ujian, dan kegiatan sekolah</p>
            </div>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <button onclick="changeMonth(-1)" class="nav-button">
                    ← Bulan Sebelumnya
                </button>
                <h2 class="text-2xl font-bold text-gray-900">
                    <?= $monthName ?> <?= $currentYear ?>
                </h2>
                <button onclick="changeMonth(1)" class="nav-button">
                    Bulan Selanjutnya →
                </button>
            </div>
            
            <div class="flex items-center space-x-4">
                <button onclick="goToToday()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Hari Ini
                </button>
            </div>
        </div>

        <!-- Legend -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Keterangan:</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                <div class="legend-item">
                    <div class="legend-color bg-gray-500"></div>
                    <span>Off</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-red-500"></div>
                    <span>Libur Nasional</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-orange-500"></div>
                    <span>Libur Sekolah</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-blue-500"></div>
                    <span>Ujian</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color bg-green-500"></div>
                    <span>Kegiatan</span>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
            <!-- Days of Week Header -->
            <div class="calendar-header">Minggu</div>
            <div class="calendar-header">Senin</div>
            <div class="calendar-header">Selasa</div>
            <div class="calendar-header">Rabu</div>
            <div class="calendar-header">Kamis</div>
            <div class="calendar-header">Jumat</div>
            <div class="calendar-header">Sabtu</div>

            <!-- Calendar Days -->
            <?php foreach ($calendarData as $dayData): ?>
                <?php if ($dayData === null): ?>
                    <div class="calendar-day empty"></div>
                <?php else: ?>
                    <div class="calendar-day <?= $dayData['isWeekend'] ? 'weekend' : '' ?> <?= $dayData['isToday'] ? 'today' : '' ?> <?= !empty($dayData['events']) ? 'has-events' : '' ?>" 
                         onclick="openDateModal('<?= $dayData['date'] ?>')" 
                         data-date="<?= $dayData['date'] ?>">
                        
                        <div class="day-number"><?= $dayData['day'] ?></div>
                        
                        <div class="day-events">
                            <?php if ($dayData['isWeekend']): ?>
                                <div class="event-badge bg-gray-500">Weekend</div>
                            <?php endif; ?>
                            
                            <?php foreach ($dayData['events'] as $event): ?>
                                <div class="event-badge <?= getStatusColor($event['status']) ?>">
                                    <?= esc($event['keterangan'] ?: getStatusLabel($event['status'])) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Mendatang</h3>
        <div class="space-y-3">
            <?php 
            $upcomingEvents = array_filter($events, function($event) {
                return strtotime($event['tanggal_mulai']) >= strtotime(date('Y-m-d'));
            });
            
            if (empty($upcomingEvents)): ?>
                <p class="text-gray-500 text-center py-4">Tidak ada event mendatang</p>
            <?php else: ?>
                <?php foreach (array_slice($upcomingEvents, 0, 5) as $event): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full <?= getStatusColor($event['status']) ?>"></div>
                            <div>
                                <div class="font-medium text-gray-900">
                                    <?= esc($event['keterangan'] ?: getStatusLabel($event['status'])) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('d M Y', strtotime($event['tanggal_mulai'])) ?>
                                    <?php if ($event['tanggal_mulai'] !== $event['tanggal_selesai']): ?>
                                        - <?= date('d M Y', strtotime($event['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                            <?= getStatusLabel($event['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Date Setting Modal -->
<div id="dateModal" class="modal">
    <div class="modal-content">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Setting Tanggal</h3>
                <button onclick="closeDateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="dateSettingForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <select id="tanggal_selesai" name="tanggal_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-2">
                    <button type="button" onclick="closeDateModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200">
                        Batal
                    </button>
                    <button type="button" onclick="deleteStatus(false)" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                        Hapus Manual
                    </button>
                    <button type="button" onclick="deleteStatus(true)" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                        Hapus Semua
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200">
                        Simpan
                    </button>
                </div>
            </form>
            
            <!-- Current Events Display -->
            <div id="currentEvents" class="mt-6 pt-4 border-t"></div>
        </div>
    </div>
</div>

<script>
let currentMonth = <?= $currentMonth ?>;
let currentYear = <?= $currentYear ?>;
let selectedDate = null;

// Navigation functions
function changeMonth(direction) {
    currentMonth += direction;
    
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    } else if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    
    window.location.href = `/admin/kalender-akademik?month=${currentMonth}&year=${currentYear}`;
}

function goToToday() {
    const today = new Date();
    const month = today.getMonth() + 1;
    const year = today.getFullYear();
    
    window.location.href = `/admin/kalender-akademik?month=${month}&year=${year}`;
}

// Modal functions
function openDateModal(date) {
    selectedDate = date;
    
    // Reset form for new event
    resetFormForNewEvent();
    
    // Set start date
    document.getElementById('tanggal_mulai').value = date;
    
    // Populate end date options
    populateEndDateOptions(date);
    
    // Load current events for this date
    loadCurrentEvents(date);
    
    // Show modal
    document.getElementById('dateModal').classList.add('show');
}

function resetFormForNewEvent() {
    const form = document.getElementById('dateSettingForm');
    
    // Reset form fields
    form.reset();
    
    // Remove event_id if exists
    const eventIdInput = form.querySelector('input[name="event_id"]');
    if (eventIdInput) {
        eventIdInput.remove();
    }
    
    // Reset submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.textContent = 'Simpan Event';
    submitBtn.className = submitBtn.className.replace('bg-green-600 hover:bg-green-700', 'bg-blue-600 hover:bg-blue-700');
}

function closeDateModal() {
    document.getElementById('dateModal').classList.remove('show');
    selectedDate = null;
    
    // Reset form when closing
    resetFormForNewEvent();
}

function populateEndDateOptions(startDate) {
    const endSelect = document.getElementById('tanggal_selesai');
    endSelect.innerHTML = '';
    
    const start = new Date(startDate);
    
    // Add options for next 30 days
    for (let i = 0; i <= 30; i++) {
        const date = new Date(start);
        date.setDate(start.getDate() + i);
        
        const dateStr = date.toISOString().split('T')[0];
        const option = document.createElement('option');
        option.value = dateStr;
        option.textContent = formatDate(dateStr);
        
        if (i === 0) {
            option.selected = true;
        }
        
        endSelect.appendChild(option);
    }
}

function loadCurrentEvents(date) {
    fetch('/admin/kalender-akademik/get-events-by-date', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `date=${encodeURIComponent(date)}`
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('currentEvents');
        
        if (data.success) {
            let html = '<h4 class="font-medium text-gray-900 mb-2">Event Saat Ini:</h4>';
            
            if (data.isWeekend) {
                html += '<div class="text-sm text-gray-600 mb-2">📅 ' + data.dayName + ' (Weekend)</div>';
            }
            
            if (data.events.length > 0) {
                html += '<div class="space-y-2">';
                data.events.forEach(event => {
                    const isBuiltIn = event.is_manual == 0;
                    const editBtnText = isBuiltIn ? 'Override' : 'Edit';
                    const editBtnClass = isBuiltIn ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-blue-500 hover:bg-blue-600';
                    
                    html += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                            <div class="flex-1">
                                <div class="font-medium">${event.keterangan || getStatusLabel(event.status)}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ${formatDate(event.tanggal_mulai)} - ${formatDate(event.tanggal_selesai)}
                                    ${isBuiltIn ? ' (Libur Bawaan)' : ' (Manual)'}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs px-2 py-1 rounded ${getStatusBadgeClass(event.status)}">
                                    ${getStatusLabel(event.status)}
                                </span>
                                <button onclick="editEvent(${event.id})" class="text-xs px-2 py-1 rounded text-white ${editBtnClass}">
                                    ${editBtnText}
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html += '<div class="text-sm text-gray-500">Tidak ada event</div>';
            }
            
            container.innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Error loading events:', error);
    });
}

// Form submission
document.getElementById('dateSettingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const eventId = formData.get('event_id');
    
    // Determine endpoint based on whether we're updating existing event
    const endpoint = eventId ? '/admin/kalender-akademik/update-event' : '/admin/kalender-akademik/save-event';
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = eventId ? 'Mengupdate...' : 'Menyimpan...';
    submitBtn.disabled = true;
    
    fetch(endpoint, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeDateModal();
            location.reload(); // Refresh to show updated calendar
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat ' + (eventId ? 'mengupdate' : 'menyimpan'));
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Delete status function
function deleteStatus(deleteAll = false) {
    if (!selectedDate) return;
    
    const confirmMessage = deleteAll 
        ? 'Apakah Anda yakin ingin menghapus SEMUA status (termasuk libur bawaan) untuk tanggal ini?' 
        : 'Apakah Anda yakin ingin menghapus status manual untuk tanggal ini?';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    fetch('/admin/kalender-akademik/delete-event', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `date=${encodeURIComponent(selectedDate)}&delete_all=${deleteAll}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeDateModal();
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus');
    });
}

// Edit event function
function editEvent(eventId) {
    fetch('/admin/kalender-akademik/get-events-by-date', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `date=${encodeURIComponent(selectedDate)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const event = data.events.find(e => e.id == eventId);
            if (event) {
                // Make sure modal is visible
                document.getElementById('dateModal').classList.add('show');
                
                // Populate form with event data
                document.getElementById('tanggal_mulai').value = event.tanggal_mulai;
                document.getElementById('status').value = event.status;
                document.getElementById('keterangan').value = event.keterangan || '';
                
                // Update end date options
                populateEndDateOptions(event.tanggal_mulai);
                document.getElementById('tanggal_selesai').value = event.tanggal_selesai;
                
                // Store event ID for update
                const form = document.getElementById('dateSettingForm');
                let eventIdInput = form.querySelector('input[name="event_id"]');
                if (!eventIdInput) {
                    eventIdInput = document.createElement('input');
                    eventIdInput.type = 'hidden';
                    eventIdInput.name = 'event_id';
                    form.appendChild(eventIdInput);
                }
                eventIdInput.value = eventId;
                
                // Change submit button text and color
                const submitBtn = form.querySelector('button[type="submit"]');
                const isBuiltIn = event.is_manual == 0;
                
                if (isBuiltIn) {
                    submitBtn.textContent = 'Override Event';
                    submitBtn.className = submitBtn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-yellow-600 hover:bg-yellow-700');
                } else {
                    submitBtn.textContent = 'Update Event';
                    submitBtn.className = submitBtn.className.replace('bg-blue-600 hover:bg-blue-700', 'bg-green-600 hover:bg-green-700');
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat data event');
    });
}

// Helper functions
function formatDate(dateStr) {
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                   'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function getStatusLabel(status) {
    const labels = {
        'off': 'Off',
        'libur_nasional': 'Libur Nasional',
        'libur_sekolah': 'Libur Sekolah',
        'ujian': 'Ujian',
        'kegiatan': 'Kegiatan'
    };
    return labels[status] || 'Unknown';
}

function getStatusBadgeClass(status) {
    const classes = {
        'off': 'bg-gray-100 text-gray-800',
        'libur_nasional': 'bg-red-100 text-red-800',
        'libur_sekolah': 'bg-orange-100 text-orange-800',
        'ujian': 'bg-blue-100 text-blue-800',
        'kegiatan': 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Close modal when clicking outside
document.getElementById('dateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDateModal();
    }
});

// ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDateModal();
    }
});
</script>

<?php
// Helper functions for the view
function getStatusColor($status) {
    $colors = [
        'off'           => 'bg-gray-500',
        'libur_nasional'=> 'bg-red-500',
        'libur_sekolah' => 'bg-orange-500',
        'ujian'         => 'bg-blue-500',
        'kegiatan'      => 'bg-green-500',
    ];
    return $colors[$status] ?? 'bg-gray-500';
}

function getStatusLabel($status) {
    $labels = [
        'off'           => 'Off',
        'libur_nasional'=> 'Libur Nasional',
        'libur_sekolah' => 'Libur Sekolah',
        'ujian'         => 'Ujian',
        'kegiatan'      => 'Kegiatan',
    ];
    return $labels[$status] ?? 'Unknown';
}
?>

<?= $this->endSection() ?>
