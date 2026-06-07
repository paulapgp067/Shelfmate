const input = document.getElementById("buscador");
const resultados = document.getElementById("resultados");

const API_URL = "https://openlibrary.org/search.json";
const MAX_RESULTS = 12;

// 🔎 Obtener libros
async function buscarLibros(query) {
    const res = await fetch(`${API_URL}?q=${encodeURIComponent(query)}`);
    if (!res.ok) throw new Error("Error en la API");
    return await res.json();
}

// Crear tarjeta de libro
function crearCard(book) {

    const title = book.title || "Sin título";
    const author = book.author_name?.join(", ") || "Desconocido";
    const cover = book.cover_i
        ? `https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg`
        : "https://via.placeholder.com/200x300?text=Sin+portada";

    const card = document.createElement("div");
    card.classList.add("card");

    card.innerHTML = `
        <img src="${cover}" alt="${title}">
        <h3>${title}</h3>
        <small>${author}</small>

        <form method="POST" class="btns">
            <input type="hidden" name="api_id" value="${book.key}">
            <input type="hidden" name="titulo" value="${title}">
            <input type="hidden" name="autor" value="${author}">
            <input type="hidden" name="portada" value="${cover}">

            <button name="estado" value="quiero_leer" class="quiero">📌 Quiero leer</button>
            <button name="estado" value="leyendo" class="leyendo">📖 Leyendo</button>
            <button name="estado" value="leido" class="leido">✔️ Leído</button>
        </form>
    `;

    return card;
}

// resultados
function mostrarResultados(books) {
    resultados.innerHTML = "";

    books.slice(0, MAX_RESULTS).forEach(book => {
        resultados.appendChild(crearCard(book));
    });
}

// Evento principal
input.addEventListener("input", async () => {

    const q = input.value.trim();

    if (q.length < 3) {
        resultados.innerHTML = "";
        return;
    }

    try {
        const data = await buscarLibros(q);
        mostrarResultados(data.docs);
    } catch (error) {
        resultados.innerHTML = `
            <p style="text-align:center; padding:20px;">
                ❌ Error al buscar libros
            </p>
        `;
        console.error(error);
    }
});