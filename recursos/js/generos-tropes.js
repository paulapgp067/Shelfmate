const tropesPorGenero = {
    romance: [
        "Enemies to Lovers",
        "Friends to Lovers",
        "Fake Dating",
        "Slow Burn",
        "Only One Bed",
        "Grumpy x Sunshine",
        "Opposites Attract"
    ],
    thriller: [
        "Unreliable Narrator",
        "Plot Twist",
        "Weird Stranger",
        "Cliffhanger",
        "Race Against Time",
        "Locked Room",
        "Cold Case Reopened"
    ],
    fantasia: [
        "The Chosen One",
        "World Building",
        "Magic System",
        "Quest",
        "Hidden Heir",
        "Dark Prophecy",
        "Lost Kingdom"
    ],
    otros: [
        "Coming of age",
        "Found Family",
        "Morally Grey Character",
        "Redemption Arc",
        "Misunderstood Villain"
    ]
};

const selectGenero = document.getElementById("genero_principal");
const selectTropes = document.getElementById("tropes");

// 🔧 Utilidad: limpiar select
function limpiarSelect(select) {
    select.innerHTML = "";
}

// 🔧 Utilidad: crear option
function crearOption(texto) {
    const option = document.createElement("option");
    option.value = texto.toLowerCase().replace(/\s+/g, "_");
    option.textContent = texto;
    return option;
}

// 🎯 Render de tropes
function actualizarTropes(genero) {

    limpiarSelect(selectTropes);

    const tropes = tropesPorGenero[genero];

    if (!tropes) {
        selectTropes.appendChild(
            crearOption("Selecciona un género primero")
        );
        return;
    }

    tropes.forEach(trope => {
        selectTropes.appendChild(crearOption(trope));
    });
}

// 🚀 Evento principal
selectGenero.addEventListener("change", (e) => {
    actualizarTropes(e.target.value);
});