document.addEventListener("DOMContentLoaded", () => {
    const username = localStorage.getItem("username") || "Unknown User";
    const games = JSON.parse(localStorage.getItem("games")) || [];
    let currentEditIndex = null;

    document.getElementById("username-display").textContent = username;

    function updateGamesList() {
        const gamesList = document.getElementById("games-list");
        const totalHoursDisplay = document.getElementById("total-hours");
        gamesList.innerHTML = "";
        let totalHours = 0;

        games.forEach((game, index) => {
            totalHours += parseInt(game.hours);
            const li = document.createElement("li");
            li.className = "game-item";
            li.innerHTML = `
                <div>
                    <strong>${game.title}</strong> - ${game.hours} Hours<br>
                    Rating: ${game.rating}/10<br>
                    Review: ${game.review}
                </div>
                <button class="edit-button" data-index="${index}">Edit</button>
            `;
            gamesList.appendChild(li);
        });

        document.getElementById("games-count").textContent = games.length;
        totalHoursDisplay.textContent = totalHours;

        document.querySelectorAll(".edit-button").forEach(button => {
            button.addEventListener("click", openEditModal);
        });
    }

    function openEditModal(event) {
        currentEditIndex = event.target.dataset.index;
        const game = games[currentEditIndex];

        document.getElementById("edit-title").value = game.title;
        document.getElementById("edit-hours").value = game.hours;
        document.getElementById("edit-rating").value = game.rating;
        document.getElementById("edit-review").value = game.review;

        document.getElementById("edit-modal").style.display = "flex";
    }

    document.getElementById("save-edit").addEventListener("click", () => {
        games[currentEditIndex] = {
            title: document.getElementById("edit-title").value,
            hours: document.getElementById("edit-hours").value,
            rating: document.getElementById("edit-rating").value,
            review: document.getElementById("edit-review").value
        };

        localStorage.setItem("games", JSON.stringify(games));
        document.getElementById("edit-modal").style.display = "none";
        updateGamesList();
    });

    document.getElementById("delete-game").addEventListener("click", () => {
        games.splice(currentEditIndex, 1);
        localStorage.setItem("games", JSON.stringify(games));
        document.getElementById("edit-modal").style.display = "none";
        updateGamesList();
    });

    document.getElementById("cancel-edit").addEventListener("click", () => {
        document.getElementById("edit-modal").style.display = "none";
    });

    updateGamesList();
    
    function openEditModal(game) {
        document.getElementById('edit-game-id').value = game.id;
        document.getElementById('edit-title').value = game.title;
        document.getElementById('edit-hours').value = game.hours;
        document.getElementById('edit-rating').value = game.rating;
        document.getElementById('edit-review').value = game.review;
        document.getElementById('edit-modal').style.display = 'block';
    }
    
    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }
});