
document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // 1. PENCARIAN DATA MAHASISWA
    // ==========================================

    const search = document.getElementById("search");
    const tableBody = document.getElementById("dataMahasiswa");
    const noResult = document.getElementById("noResult");

    if (search && tableBody) {

        search.addEventListener("input", function () {

            const keyword = search.value.toLowerCase().trim();

            // Mengambil baris data, tidak termasuk baris pesan
            const rows = tableBody.querySelectorAll(
                "tr:not(#noResult):not(#emptyData)"
            );

            let visibleCount = 0;

            rows.forEach(function (row) {

                const data = row.textContent.toLowerCase();
                const isMatch = data.includes(keyword);

                row.style.display = isMatch ? "" : "none";

                if (isMatch) {
                    visibleCount++;
                }

            });

            // Menampilkan pesan jika pencarian tidak menemukan data
            if (noResult) {
                noResult.style.display =
                    rows.length > 0 && visibleCount === 0
                        ? ""
                        : "none";
            }

        });

    }


    // ==========================================
    // 2. KONFIRMASI SEBELUM MENGHAPUS DATA
    // ==========================================

    document.addEventListener("click", function (event) {

        const button = event.target.closest("[data-confirm]");

        if (!button) return;

        const message = button.dataset.confirm;

        if (!confirm(message)) {
            event.preventDefault();
        }

    });


    // ==========================================
    // 3. TAMPIL / SEMBUNYIKAN PASSWORD
    // ==========================================

    document.addEventListener("click", function (event) {

        const button = event.target.closest(
            "[data-toggle-password]"
        );

        if (!button) return;

        const input = document.querySelector(
            button.dataset.togglePassword
        );

        if (input) {

            input.type = input.type === "password"
                ? "text"
                : "password";

        }

    });


    // ==========================================
    // 4. NOTIFIKASI JAVASCRIPT
    // ==========================================

    console.log(
        "Student Portal JavaScript berhasil dimuat."
    );

});