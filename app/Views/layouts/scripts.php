<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- html2canvas untuk screenshot -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    // Base URL for JS
    const base_url = '<?= base_url() ?>/';

    // CSRF Token untuk AJAX
    const csrfName = '<?= csrf_hash() ? csrf_token() : '' ?>';
    const csrfHash = '<?= csrf_hash() ?>';

    // Setup AJAX default
    $.ajaxSetup({
        dataType: 'json',
        beforeSend: function() {
            $('#loadingOverlay').addClass('show');
        },
        complete: function() {
            $('#loadingOverlay').removeClass('show');
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                window.location.href = '<?= base_url('login') ?>';
            }
        }
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        const colors = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-primary'
        };

        const toast = $(`
        <div class="toast align-items-center text-white ${colors[type] || colors.info} border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icons[type] || icons.info} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);

        $('.toast-container').append(toast);
        setTimeout(() => toast.fadeOut(300, function() {
            $(this).remove();
        }), 3000);
    }

    // Search form handler
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const query = $('#searchInput').val().trim();
        if (query) {
            window.location.href = '<?= base_url('search') ?>?q=' + encodeURIComponent(query);
        }
    });

    // Mobile search form handler
    $('#searchFormMobile').on('submit', function(e) {
        e.preventDefault();
        const query = $('#searchInputMobile').val().trim();
        if (query) {
            window.location.href = '<?= base_url('search') ?>?q=' + encodeURIComponent(query);
        }
    });

    // Lazy loading images
    document.addEventListener('DOMContentLoaded', function() {
        if ('IntersectionObserver' in window) {
            const imgObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        imgObserver.unobserve(img);
                    }
                });
            });
            document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
        }
    });

    // Update cart badge
    function updateCartBadge(count) {
        if (count > 0) {
            $('#cartBadge').text(count).show();
            $('#cartBadgeMobile').text(count).show();
        } else {
            $('#cartBadge').hide();
            $('#cartBadgeMobile').hide();
        }
    }

    // Format rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Sidebar auto-hide mobile (hanya jika sidebar ada)
    if ($('.sidebar').length) {
        if (!$('#sidebarBackdrop').length) {
            $('body').append('<div class="sidebar-backdrop" id="sidebarBackdrop"></div>');
        }

        $('#sidebarToggle').on('click', function() {
            $('.sidebar').addClass('show');
            $('#sidebarBackdrop').addClass('show');
        });

        $('#sidebarBackdrop').on('click', function() {
            $('.sidebar').removeClass('show');
            $(this).removeClass('show');
        });
    }

    // Preview cetak F4 (mobile: tombol jadi screenshot)
    function setPreviewTitle() {
        const menuName = ($('.sidebar .nav-link.active').text().trim()) ||
            (document.querySelector('.main-content h4') ? document.querySelector('.main-content h4').textContent.trim() : '') ||
            'Preview';
        $('#printPreviewModal .modal-title').html('<i class="bi bi-file-earmark-text me-2"></i>' + menuName);
    }

    function openPrintPreview() {
        const source = document.querySelector('.main-content');
        const paper = document.getElementById('printPreviewPaper');
        if (source) {
            paper.innerHTML = source.innerHTML;
        }
        // Preview cetak: tampilan kertas F4
        paper.classList.remove('paper-capture');
        setPreviewTitle();
        $('#printPreviewModal').modal('show');
    }

    // Tombol Cetak di modal preview: langsung buka dialog print F4
    $(document).on('click', '#previewPrintBtn', function() {
        window.print();
    });

    // Salin Gambar: capture tersembunyi (tanpa preview) lebar tetap 794px agar laporan utuh
    function copyReportImage() {
        if (typeof html2canvas === 'undefined') {
            showToast('Gagal memuat html2canvas, periksa koneksi', 'error');
            return;
        }
        const source = document.querySelector('.main-content');
        if (!source) {
            return;
        }
        // Clone tersembunyi di luar layar, lalu hapus setelah di-copy
        const wrapper = document.createElement('div');
        wrapper.className = 'print-preview';
        wrapper.style.position = 'fixed';
        wrapper.style.left = '-9999px';
        wrapper.style.top = '0';
        wrapper.style.width = '794px';
        const clone = source.cloneNode(true);
        clone.className += ' paper paper-capture';
        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);

        html2canvas(wrapper.querySelector('.paper'), {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(function(canvas) {
            wrapper.remove();
            // Salin gambar ke clipboard agar bisa paste di WhatsApp
            canvas.toBlob(function(blob) {
                if (blob && navigator.clipboard && window.ClipboardItem) {
                    navigator.clipboard.write([new ClipboardItem({
                        'image/png': blob
                    })]).then(function() {
                        // SweetAlert timer, tutup otomatis
                        Swal.fire({
                            icon: 'success',
                            title: 'Gambar Disalin',
                            text: 'Silakan paste di WhatsApp',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }).catch(function() {
                        downloadCanvas(canvas);
                    });
                } else {
                    downloadCanvas(canvas);
                }
            }, 'image/png');
        }).catch(function(err) {
            wrapper.remove();
            showToast('Gagal mengambil screenshot: ' + err.message, 'error');
        });
    }

    // Fallback: unduh PNG jika clipboard tidak tersedia
    function downloadCanvas(canvas) {
        const link = document.createElement('a');
        link.download = 'screenshot-' + Date.now() + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        showToast('Clipboard tidak tersedia, gambar diunduh', 'info');
    }
</script>

<!-- Modal Preview Cetak F4 -->
<div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content print-preview">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Preview F4</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-secondary">
                <div class="paper" id="printPreviewPaper"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="previewPrintBtn">
                    <i class="bi bi-printer me-1"></i>Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->renderSection('scripts') ?>

</body>

</html>