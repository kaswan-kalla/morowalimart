<div class="container py-4">
    <div class="text-center mb-3">
        <h5 class="fw-bold text-primary">
            <i class="bi bi-megaphone me-2"></i>Ayo dukung dakwah dengan berbelanja di Morowalimart
        </h5>
        <p class="text-muted mb-0">Mohon isi survey berikut:</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-clipboard-data fs-1 text-primary"></i>
                        <h4 class="fw-bold mt-2">Survey Pelanggan</h4>
                        <p class="text-muted">Sebelum memulai, mohon isi data diri Anda terlebih dahulu</p>
                    </div>

                    <form id="surveyForm">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="no_wa" class="form-control" placeholder="0824-2548-5842" value="08" required maxlength="15">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <select name="alamat" class="form-select" required>
                                <option value="">-- Pilih Desa --</option>
                                <option value="Desa Labota">Desa Labota</option>
                                <option value="Desa Keurea">Desa Keurea</option>
                                <option value="Desa Makarti">Desa Makarti</option>
                                <option value="Desa Baho Makmur">Desa Baho Makmur</option>
                                <option value="Desa Bahodopi">Desa Bahodopi</option>
                                <option value="Desa Fatufia">Desa Fatufia</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pengeluaran kebutuhan rutin bulanan <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengeluaran_perbulan" value="Dibawah 1jt" id="peng1" required>
                                        <label class="form-check-label" for="peng1">&lt; Rp 1.000.000</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengeluaran_perbulan" value="1jt - 2jt" id="peng2">
                                        <label class="form-check-label" for="peng2">Rp 1.000.000 - 2.000.000</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengeluaran_perbulan" value="2jt - 3jt" id="peng3">
                                        <label class="form-check-label" for="peng3">Rp 2.000.000 - 3.000.000</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pengeluaran_perbulan" value="Diatas 3jt" id="peng4">
                                        <label class="form-check-label" for="peng4">&gt; Rp 3.000.000</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status Menikah <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status_menikah" value="Menikah" id="status_menikah" checked>
                                    <label class="form-check-label" for="status_menikah">Menikah</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status_menikah" value="Belum Menikah" id="status_belum">
                                    <label class="form-check-label" for="status_belum">Belum Menikah</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" id="siap_member" name="siap_member" value="1">
                            <label class="form-check-label" for="siap_member">
                                <strong>Insya Allah</strong> siap berbelanja untuk dukung dakwah.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="btnMulai" disabled>
                            <i class="bi bi-send me-2"></i>Mulai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var wa = document.querySelector('input[name="no_wa"]');
        if (!wa) return;

        function formatWA(val) {
            var digits = val.replace(/\D/g, '');
            if (!digits) return '';
            if (!digits.startsWith('08')) digits = '08' + digits.replace(/^0*/, '');
            if (digits.length > 13) digits = digits.slice(0, 13);
            var parts = [];
            if (digits.length > 0) parts.push(digits.slice(0, 4));
            if (digits.length > 4) parts.push(digits.slice(4, 8));
            if (digits.length > 8) parts.push(digits.slice(8, 13));
            return parts.join('-');
        }

        wa.addEventListener('input', function() {
            var cursor = this.selectionStart;
            var prevLen = this.value.length;
            this.value = formatWA(this.value);
            var newLen = this.value.length;
            this.setSelectionRange(cursor + (newLen - prevLen), cursor + (newLen - prevLen));
        });
    })();
</script>