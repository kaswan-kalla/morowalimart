// === Midtrans Snap Pay ===
$(document).ready(function () {
  // Tombol bayar Snap — buka popup Midtrans
  $('#btnSnapPay').on('click', function () {
    if (typeof snap !== 'undefined' && snapToken) {
      snap.pay(snapToken, {
        onSuccess: function (result) {
          // Ubah tombol bayar jadi "Lihat Pesanan"
          $('#btnSnapPay')
            .removeClass('btn-primary')
            .addClass('btn-success')
            .html('<i class="bi bi-receipt"></i> Lihat Pesanan')
            .off('click')
            .on('click', function () {
              window.location.href = base_url + 'order/' + orderId;
            });
        },
        onPending: function (result) {
          showToast(
            'Pembayaran sedang diproses. Silakan selesaikan pembayaran Anda.',
            'info',
          );
          setTimeout(function () {
            location.reload();
          }, 2000);
        },
        onError: function (result) {
          showToast('Pembayaran gagal, silakan coba lagi.', 'danger');
        },
        onClose: function () {
          // Popup ditutup tanpa bayar — tidak reload, tombol tetap tersedia
        },
      });
    } else {
      showToast('Snap belum siap, silakan refresh halaman.', 'warning');
    }
  });
});

// === Upload Bukti Manual (fallback) ===
$('#paymentForm').on('submit', function (e) {
  e.preventDefault();
  if (!$(this).length) return;
  $('#btnUploadProof')
    .prop('disabled', true)
    .html(
      '<span class="spinner-border spinner-border-sm"></span> Mengupload...',
    );
  $.ajax({
    url: base_url + 'payment/upload',
    method: 'POST',
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status) {
        showToast('Bukti pembayaran diupload!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(res.message, 'danger');
        $('#btnUploadProof')
          .prop('disabled', false)
          .html('<i class="bi bi-upload"></i> Upload Bukti');
      }
    },
    error: function () {
      showToast('Gagal upload', 'danger');
      $('#btnUploadProof')
        .prop('disabled', false)
        .html('<i class="bi bi-upload"></i> Upload Bukti');
    },
  });
});
