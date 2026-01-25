<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<?php
// Load helper
use App\Helpers\AttendanceHelper;

// Calculate statistics if data exists
$stats = AttendanceHelper::calculateStats($attendanceData ?? []);
$legend = AttendanceHelper::getLegendData();
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    /* Enhanced Styles with CSS Variables */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .table-sticky-left {
        position: sticky;
        left: 0;
        z-index: 20;
        box-shadow: 2px 0 10px rgba(0,0,0,0.15);
    }
    
    .table-sticky-second {
        position: sticky;
        left: 60px;
        z-index: 15;
        box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    }
    
    .attendance-cell {
        min-width: 45px;
        width: 45px;
        max-width: 45px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    
    .attendance-cell:hover {
        transform: scale(1.2);
        z-index: 25;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        border-radius: 8px;
    }
    
    .gradient-header {
        background: var(--primary-gradient);
        background-size: 200% 200%;
        animation: gradientShift 4s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.8s ease forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-in-right {
        animation: slideInRight 0.6s ease forwards;
        opacity: 0;
        transform: translateX(50px);
    }
    
    @keyframes slideInRight {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .attendance-cell {
            min-width: 38px;
            width: 38px;
            max-width: 38px;
        }
        
        .table-sticky-second {
            left: 50px;
        }
    }
    
    @media print {
        .no-print { display: none !important; }
        .attendance-cell { font-size: 10px; }
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-2 lg:p-6">
    
    <!-- Header Component -->
    <?= $this->include('admin/absensi/components/header', [
        'filterKelas' => $filterKelas ?? '',
        'bulan_nama' => $bulan_nama ?? date('F'),
        'tahun' => $tahun ?? date('Y')
    ]) ?>

    <!-- Filter Component -->
    <?= $this->include('admin/absensi/components/filter', [
        'userRole' => $userRole,
        'allKelas' => $allKelas ?? [],
        'userKelas' => $userKelas ?? '',
        'filterKelas' => $filterKelas ?? '',
        'filterBulan' => $filterBulan ?? date('Y-m'),
        'attendanceData' => $attendanceData ?? []
    ]) ?>

    <?php if (!empty($attendanceData) && !empty($attendanceData['students'])): ?>
        
        <!-- Statistics Component -->
        <?= $this->include('admin/absensi/components/statistics', ['stats' => $stats]) ?>

        <!-- Professional Header Component -->
        <?= $this->include('admin/absensi/components/document_header', [
            'attendanceData' => $attendanceData
        ]) ?>

        <!-- Main Table Component -->
        <?= $this->include('admin/absensi/components/attendance_table', [
            'attendanceData' => $attendanceData,
            'legend' => $legend,
            'stats' => $stats
        ]) ?>

    <?php else: ?>
        
        <!-- No Data Component -->
        <?= $this->include('admin/absensi/components/no_data', [
            'filterKelas' => $filterKelas ?? '',
            'filterBulan' => $filterBulan ?? '',
            'userKelas' => $userKelas ?? ''
        ]) ?>
        
    <?php endif; ?>
</div>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Enhanced Rekap Absensi Module Loading...');
    
    // Initialize components
    AttendanceApp.init();
    
    console.log('✅ Enhanced Rekap Absensi Module Loaded Successfully!');
});

// Main Application Object
const AttendanceApp = {
    config: {
        animationDelay: 100,
        scrollDebounce: 150,
        loadingTimeout: 500
    },
    
    elements: {},
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.initAnimations();
        this.initResponsiveFeatures();
        this.initKeyboardShortcuts();
    },
    
    cacheElements() {
        this.elements = {
            filterForm: document.getElementById('filterForm'),
            kelasSelect: document.getElementById('kelas'),
            bulanInput: document.getElementById('bulan'),
            submitBtn: document.querySelector('button[type="submit"]'),
            downloadBtn: document.getElementById('downloadExcel'),
            tableContainer: document.querySelector('.overflow-x-auto'),
            attendanceTable: document.getElementById('attendanceTable'),
            attendanceCells: document.querySelectorAll('.attendance-cell'),
            studentRows: document.querySelectorAll('.student-row')
        };
    },
    
    bindEvents() {
        // Auto-submit functionality
        if (this.elements.kelasSelect) {
            this.elements.kelasSelect.addEventListener('change', () => this.autoSubmit());
        }
        
        if (this.elements.bulanInput) {
            this.elements.bulanInput.addEventListener('change', () => this.autoSubmit());
        }
        
        // Form submission
        if (this.elements.filterForm) {
            this.elements.filterForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
        
        // Excel download
        if (this.elements.downloadBtn) {
            this.elements.downloadBtn.addEventListener('click', () => this.handleExcelDownload());
        }
        
        // Table scroll effects
        if (this.elements.tableContainer) {
            this.elements.tableContainer.addEventListener('scroll', 
                this.debounce(() => this.handleTableScroll(), this.config.scrollDebounce)
            );
        }
        
        // Enhanced cell hover effects
        this.elements.attendanceCells.forEach(cell => {
            cell.addEventListener('mouseenter', () => this.enhanceCellHover(cell, true));
            cell.addEventListener('mouseleave', () => this.enhanceCellHover(cell, false));
            cell.addEventListener('click', () => this.handleCellClick(cell));
        });
    },
    
    autoSubmit() {
        const hasKelas = this.elements.kelasSelect?.value || '<?= $userKelas ?? '' ?>';
        const hasBulan = this.elements.bulanInput?.value;
        
        if (hasKelas && hasBulan) {
            this.showLoading(this.elements.submitBtn, 'Memuat Data...', 'fa-sync fa-spin');
            
            // Smooth transition effect
            if (this.elements.attendanceTable) {
                this.elements.attendanceTable.style.transition = 'all 0.4s ease';
                this.elements.attendanceTable.style.opacity = '0.6';
                this.elements.attendanceTable.style.transform = 'translateY(10px)';
            }
            
            setTimeout(() => {
                this.elements.filterForm?.submit();
            }, this.config.loadingTimeout);
        }
    },
    
    handleFormSubmit(e) {
        this.showLoading(this.elements.submitBtn, 'Memuat Data...', 'fa-sync fa-spin');
    },
    
    handleExcelDownload() {
        if (this.elements.downloadBtn?.disabled) return;
        
        this.showLoading(this.elements.downloadBtn, 'Mengunduh...', 'fa-download fa-bounce');
        
        const exportUrl = '<?= base_url('admin/absensi/export') ?>?' + 
            new URLSearchParams({
                kelas: '<?= $filterKelas ?? '' ?>',
                start_date: '<?= $filterBulan ?? '' ?>-01',
                end_date: '<?= isset($filterBulan) ? date('Y-m-t', strtotime($filterBulan . '-01')) : '' ?>'
            });
        
        // Create download iframe
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = exportUrl;
        document.body.appendChild(iframe);
        
        // Show success message
        this.showNotification('File Excel sedang diunduh...', 'success');
        
        // Reset button and cleanup
        setTimeout(() => {
            document.body.removeChild(iframe);
            this.hideLoading(this.elements.downloadBtn);
        }, 3000);
    },
    
    handleTableScroll() {
        const container = this.elements.tableContainer;
        if (!container) return;
        
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;
        
        // Dynamic shadow effects
        container.classList.toggle('scroll-shadow-left', scrollLeft > 10);
        container.classList.toggle('scroll-shadow-right', scrollLeft < scrollWidth - clientWidth - 10);
        
        // Update scroll indicator
        this.updateScrollIndicator(scrollLeft, scrollWidth, clientWidth);
    },
    
    enhanceCellHover(cell, isHover) {
        if (isHover) {
            cell.style.transform = 'scale(1.15)';
            cell.style.zIndex = '30';
            cell.style.boxShadow = '0 12px 30px rgba(0,0,0,0.4)';
            cell.style.borderRadius = '10px';
            
            // Show tooltip if available
            const tooltip = cell.getAttribute('title');
            if (tooltip) {
                this.showTooltip(cell, tooltip);
            }
        } else {
            cell.style.transform = 'scale(1)';
            cell.style.zIndex = 'auto';
            cell.style.boxShadow = 'none';
            cell.style.borderRadius = '0';
            
            this.hideTooltip();
        }
    },
    
    handleCellClick(cell) {
        // Add click animation
        cell.style.transform = 'scale(0.95)';
        setTimeout(() => {
            cell.style.transform = cell.matches(':hover') ? 'scale(1.15)' : 'scale(1)';
        }, 150);
        
        // Could be extended for edit functionality
        const status = cell.textContent.trim();
        console.log('Cell clicked:', status);
    },
    
    initAnimations() {
        // Staggered table animation
        if (this.elements.attendanceTable) {
            this.elements.attendanceTable.style.opacity = '0';
            this.elements.attendanceTable.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                this.elements.attendanceTable.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                this.elements.attendanceTable.style.opacity = '1';
                this.elements.attendanceTable.style.transform = 'translateY(0)';
            }, 200);
        }
        
        // Staggered row animations
        this.elements.studentRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-30px)';
            
            setTimeout(() => {
                row.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 300 + (index * 50));
        });
    },
    
    initResponsiveFeatures() {
        // Mobile optimizations
        const isMobile = () => window.innerWidth < 768;
        
        const optimizeForMobile = () => {
            if (isMobile()) {
                // Reduce animation complexity on mobile
                this.elements.attendanceCells.forEach(cell => {
                    cell.style.transition = 'all 0.2s ease';
                });
                
                // Add touch-friendly interactions
                this.addTouchSupport();
            }
        };
        
        optimizeForMobile();
        window.addEventListener('resize', this.debounce(optimizeForMobile, 250));
    },
    
    addTouchSupport() {
        this.elements.attendanceCells.forEach(cell => {
            cell.addEventListener('touchstart', () => {
                cell.style.transform = 'scale(1.1)';
                cell.style.zIndex = '30';
            });
            
            cell.addEventListener('touchend', () => {
                setTimeout(() => {
                    cell.style.transform = 'scale(1)';
                    cell.style.zIndex = 'auto';
                }, 200);
            });
        });
    },
    
    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + E for Excel export
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                this.elements.downloadBtn?.click();
                this.showNotification('Excel export triggered via keyboard shortcut', 'info');
            }
            
            // Ctrl/Cmd + R for refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                this.elements.submitBtn?.click();
                this.showNotification('Form submitted via keyboard shortcut', 'info');
            }
            
            // Escape to close any modals or reset focus
            if (e.key === 'Escape') {
                this.hideTooltip();
                document.activeElement?.blur();
            }
        });
    },
    
    showLoading(button, text, icon = 'fa-spinner fa-spin') {
        if (!button) return;
        
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<i class="fas ${icon} mr-2"></i>${text}`;
        button.classList.add('opacity-75', 'cursor-not-allowed');
        button.setAttribute('data-original', originalContent);
    },
    
    hideLoading(button) {
        if (!button) return;
        
        const originalContent = button.getAttribute('data-original');
        if (originalContent) {
            button.disabled = false;
            button.innerHTML = originalContent;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
            button.removeAttribute('data-original');
        }
    },
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        // Set type-specific styles
        const typeClasses = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-gray-900',
            info: 'bg-blue-500 text-white'
        };
        
        notification.className += ` ${typeClasses[type] || typeClasses.info}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(full)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    },
    
    showTooltip(element, text) {
        // Implementation for custom tooltip
        // Could be enhanced with a tooltip library
    },
    
    hideTooltip() {
        // Hide any visible tooltips
    },
    
    updateScrollIndicator(scrollLeft, scrollWidth, clientWidth) {
        // Update scroll indicator if available
        const indicator = document.querySelector('.scroll-indicator');
        if (indicator) {
            const progress = (scrollLeft / (scrollWidth - clientWidth)) * 100;
            indicator.style.width = `${progress}%`;
        }
    },
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Performance observer for monitoring
if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
        list.getEntries().forEach((entry) => {
            if (entry.entryType === 'measure') {
                console.log(`Performance: ${entry.name} took ${entry.duration}ms`);
            }
        });
    });
    observer.observe({ entryTypes: ['measure'] });
}
</script>

<?= $this->endSection() ?>
