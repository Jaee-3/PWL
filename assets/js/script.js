
document.addEventListener("DOMContentLoaded", function () {

    // 1. Pencarian data mahasiswa
    const search = document.getElementById("search");

    if (search) {
        search.addEventListener("keyup", function () {

            let keyword = search.value.toLowerCase();

            let rows = document.querySelectorAll(
                "#dataMahasiswa tr"
            );

            rows.forEach(function (row) {
                let data = row.textContent.toLowerCase();

                row.style.display = data.includes(keyword)
                    ? ""
                    : "none";
            });

        });
    }

    // 2. Konfirmasi sebelum menghapus mahasiswa
    document.addEventListener("click", function (event) {

        const button = event.target.closest("[data-confirm]");

        if (button) {
            const message = button.dataset.confirm;

            if (!confirm(message)) {
                event.preventDefault();
            }
        }

    });

    // 3. Tampilkan atau sembunyikan password
    document.addEventListener("click", function (event) {

        const button = event.target.closest("[data-toggle-password]");

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

    console.log("Student Portal JavaScript berhasil dimuat.");

});
