function toggleBtnMulai() {
  $('#btnMulai').prop('disabled', !$('#siap_member').is(':checked'));
}
$(document).ready(function () {
  toggleBtnMulai();
  $(document).on('change click', '#siap_member', toggleBtnMulai);

  $('#surveyForm').on('submit', function (e) {
    e.preventDefault();

    let btn = $('#btnMulai');
    btn
      .prop('disabled', true)
      .html(
        '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...',
      );

    $.ajax({
      url: base_url + '/survey/submit',
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      beforeSend: function () {
        $('#loadingOverlay').addClass('show');
      },
      complete: function () {
        $('#loadingOverlay').removeClass('show');
      },
      success: function (res) {
        if (res.status) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: res.message,
            confirmButtonText: 'Masuk ke Marketplace',
            confirmButtonColor: '#0d6efd',
          }).then(function () {
            window.location.href = base_url + '/home';
          });
        } else {
          btn
            .prop('disabled', false)
            .html('<i class="bi bi-send me-2"></i>Mulai');
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: res.message,
            confirmButtonText: 'Coba Lagi',
          });
        }
      },
      error: function () {
        btn
          .prop('disabled', false)
          .html('<i class="bi bi-send me-2"></i>Mulai');
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Terjadi kesalahan server',
          confirmButtonText: 'Coba Lagi',
        });
      },
    });
  });
});
