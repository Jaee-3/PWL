
const search = document.getElementById("search");

search.addEventListener("keyup", function() {

    let keyword = search.value.toLowerCase();

    let rows = document.querySelectorAll("#dataMahasiswa tr");

    rows.forEach(function(row) {

        let data = row.textContent.toLowerCase();

        if (data.includes(keyword)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

});