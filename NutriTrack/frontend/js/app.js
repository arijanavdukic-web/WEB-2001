
const app = document.getElementById("app");

const validPages = [
    "dashboard",
    "food-search",
    "diary",
    "history",
    "profile",
    "login",
    "register"
];
async function loadPage(page) {
    if (!validPages.includes(page)) {
        return;
    }

    try {
        const response = await fetch(`${page}.html`);

        if (!response.ok) {
            throw new Error(`Could not load ${page}.html`);
        }

        const html = await response.text();

        app.innerHTML = html;

// Restart the page entrance animation
app.classList.remove("page-transition");

// Force the browser to register the class removal
void app.offsetWidth;

app.classList.add("page-transition");

        // Update the active sidebar button
        document.querySelectorAll(".nav-link-custom").forEach(button => {
            button.classList.toggle(
                "active",
                button.dataset.page === page
            );
        });

    } catch (error) {
        console.error("Navigation error:", error);

        app.innerHTML = `
            <div class="alert alert-warning">
                <h5>Unable to load this page</h5>
                <p class="mb-0">
                    Check your file names and run the project
                    using a local web server.
                </p>
            </div>
        `;
    }
}

// One listener handles navigation buttons everywhere,
// including buttons loaded later inside the page.
document.addEventListener("click", function (event) {
    const button = event.target.closest("[data-page]");

    if (!button) {
        return;
    }

    event.preventDefault();
    loadPage(button.dataset.page);
});

// Display the dashboard when the app opens
loadPage("dashboard");
