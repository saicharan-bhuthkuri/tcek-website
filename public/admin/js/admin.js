/**
 * TCEK Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Toggle
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    const adminSidebar = document.getElementById('adminSidebar');

    if (btnToggleSidebar && adminSidebar) {
        btnToggleSidebar.addEventListener('click', () => {
            adminSidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!adminSidebar.contains(e.target) && !btnToggleSidebar.contains(e.target)) {
                    adminSidebar.classList.remove('open');
                }
            }
        });
    }

    // 2. Drag & Drop Upload Zone
    const dropzoneBox = document.getElementById('dropzoneBox');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('selectedFileInfo');
    const titleInput = document.getElementById('upload_title');

    if (dropzoneBox && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.classList.remove('drag-over');
            });
        });

        dropzoneBox.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelected(fileInput.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                handleFileSelected(fileInput.files[0]);
            }
        });

        function handleFileSelected(file) {
            if (!file) return;

            const sizeKB = (file.size / 1024).toFixed(1);
            const sizeStr = sizeKB > 1024 ? (sizeKB / 1024).toFixed(2) + ' MB' : sizeKB + ' KB';
            
            if (fileInfo) {
                fileInfo.style.display = 'block';
                fileInfo.innerHTML = `<i class="fas fa-file-check"></i> <strong>${file.name}</strong> (${sizeStr})`;
            }

            // Auto-populate Title if currently blank
            if (titleInput && (!titleInput.value || titleInput.value.trim() === '')) {
                const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                titleInput.value = baseName.replace(/[-_]/g, ' ');
            }
        }
    }
});

/**
 * Filter Explorer Items by Type (All, PDF, Image, Video)
 */
function filterExplorer(type, btn) {
    const cards = document.querySelectorAll('.explorer-card');
    const buttons = document.querySelectorAll('#explorerFilters .filter-pill');

    buttons.forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    cards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        if (type === 'all' || cardType === type) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Copy file path to clipboard and display toast notification
 */
function copyPathToClipboard(path, btn) {
    // Generate clean link relative or absolute
    const cleanPath = path.startsWith('../') ? path.substring(3) : path;
    const fullUrl = window.location.origin + '/' + cleanPath;

    navigator.clipboard.writeText(cleanPath).then(() => {
        showToast(`Copied path: "${cleanPath}"`);
        
        if (btn) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check" style="color:#10b981;"></i> Copied';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 2000);
        }
    }).catch(() => {
        // Fallback for older browsers
        const temp = document.createElement('input');
        temp.value = cleanPath;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        showToast(`Copied: "${cleanPath}"`);
    });
}

/**
 * Trigger Toast Notification
 */
function showToast(message) {
    const toast = document.getElementById('copyToast');
    if (!toast) return;

    toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}
