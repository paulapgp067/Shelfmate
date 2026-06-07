const input = document.getElementById("buscador");
const resultados = document.getElementById("resultados");
const loading = document.getElementById("loading");

const mensajes = [
    "📚 ShelfMate está buscando nuevas lecturas...",
    "🔎 Explorando miles de libros para ti...",
    "📖 Consultando las estanterías virtuales...",
    "✨ Encontrando tu próxima aventura...",
    "☕ Preparando una buena recomendación..."
];

input.addEventListener("input", async () => {

    const q = input.value.trim();

    if (q.length < 3) {
        resultados.innerHTML = "";
        loading.style.display = "none";
        return;
    }

    loading.querySelector("p").textContent =
        mensajes[Math.floor(Math.random() * mensajes.length)];

    loading.style.display = "block";

    try {
        const res = await fetch(`https://openlibrary.org/search.json?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        resultados.innerHTML = "";

        data.docs.slice(0, 12).forEach(book => {

            const title = book.title || "Sin título";
            const author = book.author_name?.join(", ") || "Desconocido";
            const cover = book.cover_i
                ? `https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg`
                : "https://via.placeholder.com/200x300?text=Sin+portada";

            const div = document.createElement("div");
            div.classList.add("card");

            div.innerHTML = `
                <img src="${cover}">
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

            resultados.appendChild(div);
        });

    } catch (error) {
        resultados.innerHTML = `
            <div style="text-align:center; padding:20px;">
                ❌ Error al buscar libros
            </div>
        `;
    } finally {
        loading.style.display = "none";
    }
});