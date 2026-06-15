<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        $('#cartBadge').text(count);
        $('#cartBadgeMobile').text(count);
    }

    // Format rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }
</script>

<?= $this->renderSection('scripts') ?>

</body>

</html>