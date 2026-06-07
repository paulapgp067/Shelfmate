const inputBusqueda = document.getElementById("busquedaLibro");
const resultados = document.getElementById("resultadosLibros");

const API_URL = "https://openlibrary.org/search.json";
const MAX_RESULTS = 6;

//  Buscar libros en API
async function buscarLibros(query) {
    const res = await fetch(`${API_URL}?q=${encodeURIComponent(query)}`);
    if (!res.ok) throw new Error("Error en la API");
    return await res.json();
}

//  Crear item de resultado
function crearItemLibro(book) {

    const titulo = book.title || "Sin título";
    const autor = book.author_name?.join(", ") || "Autor desconocido";

    const portada = book.cover_i
        ? `https://covers.openlibrary.org/b/id/${book.cover_i}-L.jpg`
        : "https://via.placeholder.com/128x192?text=Sin+portada";

    const item = document.createElement("div");
    item.classList.add("resultado-libro");

    item.innerHTML = `
        <img src="${portada}" alt="${titulo}">
        <div>
            <strong>${titulo}</strong><br>
            <small>${autor}</small>
        </div>
    `;

    item.addEventListener("click", () => seleccionarLibro(book, titulo, autor, portada));

    return item;
}

//  Seleccionar libro
function seleccionarLibro(book, titulo, autor, portada) {

    document.getElementById("api_Book_id").value = book.key || "";
    document.getElementById("titulo").value = titulo;
    document.getElementById("autor").value = autor;
    document.getElementById("anioPublicacion").value = book.first_publish_year || "";
    document.getElementById("portada").value = portada || "";

    inputBusqueda.value = titulo;
    resultados.innerHTML = "";
}

// resultados
function mostrarResultados(books) {

    resultados.innerHTML = "";

    if (!books || books.length === 0) {
        resultados.innerHTML = "<div>No hay resultados</div>";
        return;
    }

    books.slice(0, MAX_RESULTS).forEach(book => {
        resultados.appendChild(crearItemLibro(book));
    });
}

inputBusqueda.addEventListener("input", async () => {

    const query = inputBusqueda.value.trim();

    if (query.length < 3) {
        resultados.innerHTML = "";
        return;
    }

    try {
        const data = await buscarLibros(query);
        mostrarResultados(data.docs);

    } catch (error) {
        console.error(error);
        resultados.innerHTML = "<div>Error al buscar libros</div>";
    }
});