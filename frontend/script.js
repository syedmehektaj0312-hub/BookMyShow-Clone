const API = "../backend/api";

const movieGrid = document.getElementById("movieGrid");

let movies = [];

// Load movies from PHP API

async function loadMovies() {

    try {

        const response = await fetch(
            `${API}/movies.php`
        );

        const data = await response.json();

        if (!data.success) {
            throw new Error("Unable to load movies");
        }

        movies = data.movies;

        displayMovies(movies);

    } catch (error) {

        movieGrid.innerHTML =
            "<p>Unable to load movies. Check your server.</p>";

        console.error(error);

    }

}


// Display movie cards

function displayMovies(movieList) {

    movieGrid.innerHTML = "";

    if (movieList.length === 0) {

        movieGrid.innerHTML = "<p>No movies found.</p>";

        return;
    }

    movieList.forEach(movie => {

        const card = document.createElement("div");

        card.className = "movie-card";

        const image = document.createElement("img");
        image.src = movie.poster_url || "";
        image.alt = movie.title;

        image.onerror = () => {
            image.src = "https://placehold.co/400x600?text=Movie";
        };

        const info = document.createElement("div");
        info.className = "movie-info";

        const title = document.createElement("h3");
        title.textContent = movie.title;

        const details = document.createElement("p");
        details.textContent =
            `${movie.language} · ${movie.genre}`;

        const rating = document.createElement("p");
        rating.textContent = `⭐ ${movie.rating}/10`;

        const button = document.createElement("a");
        button.className = "book-btn";
        button.href = `movie.html?id=${encodeURIComponent(movie.id)}`;
        button.textContent = "Book Tickets";

        info.append(title, details, rating, button);

        card.append(image, info);

        movieGrid.appendChild(card);

    });

}


// Movie search

document.getElementById("search").addEventListener(
    "input",
    function () {

        const searchValue = this.value.toLowerCase();

        const filteredMovies = movies.filter(movie =>
            movie.title.toLowerCase().includes(searchValue)
        );

        displayMovies(filteredMovies);

    }
);


// Start application

loadMovies();