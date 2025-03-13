//kies een item uit de lijst
function selectConsole(consoleName) {
    document.getElementById("dropdownLabel").innerText = consoleName;
    document.getElementById("selectedConsole").value = consoleName;
    document.getElementById("errorMessage").style.display = "none";
    closeDropdown();
}

//Als de keuze fout is->geef error
function validateSelection() {
    const selectedConsole = document.getElementById("selectedConsole").value;
    if (!selectedConsole) {
        document.getElementById("errorMessage").style.display = "block";
        return false;
    }
    return true;
}

//Pak alle mogelijkheden van de dropdown, en kies daarna de style 
function toggleDropdown() {
    const dropdownContent = document.getElementById("dropdownContent");
    dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
}

//Close dropdown als je iets selecteerd
function closeDropdown() {
    document.getElementById("dropdownContent").style.display = "none";
}

//item uit de lijst clicked->close dropdown 
window.onclick = function (event) {
    if (!event.target.matches('.dropbtn')) {
        closeDropdown();
    }
};

//redirect naar library
document.addEventListener("DOMContentLoaded", () => {
    const homeRedirectButton = document.getElementById("libraryButton");
    homeRedirectButton.addEventListener("click", () => {
        window.location.href = "http://localhost/oefenopdracht2/index.php";
    });
});


document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("loader");
    const content = document.getElementById("content");
    const animatedElements = document.querySelectorAll(".animated-element");
    const sidebar = document.querySelector(".sidebar");
    const sidebarItems = document.querySelectorAll(".hidden-sidebar-item");
    const gameGridItems = document.querySelectorAll(".hidden-game-grid-item");

    // paginas waar de loader wordt overgeslagen
    const skipLoaderPages = ["index.php", "game_details.php"];
    const currentPage = window.location.pathname.split("/").pop();
    const lastVisitedPage = sessionStorage.getItem("lastVisitedPage");

    // controleer of de loader moet worden weergegeven
    if (!skipLoaderPages.includes(currentPage) || !skipLoaderPages.includes(lastVisitedPage)) {
        setTimeout(() => {
            loader.style.display = "none"; // Verberg de loader
            content.classList.remove("hidden"); // Toon de inhoud

            // animeren van algemene elementen
            animatedElements.forEach((element, index) => {
                setTimeout(() => {
                    element.classList.add("slide-in"); // Voeg de animatieklasse toe
                }, index * 100); // vertraag animaties per element
            });

            // Animeren van de game-grid items
            gameGridItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add("content-loaded"); // voeg de animatieklasse toe
                }, index * 50); // snellere volgorde voor game-grid
            });

            // animeren van de zijbalk items
            sidebarItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add("content-loaded"); // voeg de animatieklasse toe
                }, index * 50); // vertraagde volgorde voor de zijbalk
            });

            // maak de zijbalk zichtbaar
            setTimeout(() => {
                sidebar.classList.add("content-loaded"); // Zijbalk animatie
            }, 100); // Vertraging voor zijbalk
        }, 1000); // Vertraging voor loader voordat inhoud wordt getoond
    } else {
        loader.style.display = "none"; // Vvrberg loader 
        content.classList.remove("hidden"); // toon inhoud 

        // animeren van algemene delen 
        animatedElements.forEach((element) => {
            element.classList.add("slide-in");
        });

        // animeren van game items 
        gameGridItems.forEach((item) => {
            item.classList.add("content-loaded");
        });

        // animeren van zijbalk items 
        sidebarItems.forEach((item) => {
            item.classList.add("content-loaded");
        });

        // toon de zijbalk onmiddellijk
        sidebar.classList.add("content-loaded");
    }

    // voeg klikanimatie toe aan geanimeerde elementen
    animatedElements.forEach((element) => {
        element.addEventListener("click", () => {
            element.classList.add("clicked-animation"); // Voeg de klik-animatieklasse toe
            setTimeout(() => {
                element.classList.remove("clicked-animation"); // Verwijder de klasse na animatie
            }, 2000); // Duur van de animatie in milliseconden
        });
    });

    // Sla de huidige pagina op voor toekomstige referentie
    sessionStorage.setItem("lastVisitedPage", currentPage);
});


