
const app = document.getElementById("app");

const DEMO_USER_ID = 1;

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
    if (!validPages.includes(page)) return;

    try {
        const response = await fetch(`${page}.html?v=${Date.now()}`, {
    cache: "no-store"
});

        if (!response.ok) {
            throw new Error(`Could not load ${page}.html`);
        }

        const html = await response.text();
        app.innerHTML = html;

        if (page === "food-search") {
            initializeFoodSearch();
        }
        if (page === "dashboard") {
            initializeDashboard();
        }

        if (page === "diary") {
            initializeDiary();
        }
        if (page === "history") {
        initializeHistory();
        }
        if (page === "profile") initializeProfile();

        app.classList.remove("page-transition");
        void app.offsetWidth;
        app.classList.add("page-transition");

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
                <p>Check your file names and local server.</p>
            </div>
        `;
    }
}

function initializeFoodSearch() {
    const searchButton = document.getElementById("searchFoodButton");
    const searchInput = document.getElementById("foodQuery");
    const resultsContainer = document.getElementById("foodResults");
    const messageContainer = document.getElementById("foodSearchMessage");

    if (!searchButton || !searchInput || !resultsContainer) return;

    async function searchFoods() {
        const query = searchInput.value.trim();

        if (!query) {
            messageContainer.innerHTML =
                '<div class="alert alert-warning">Please enter a food name.</div>';
            return;
        }

        searchButton.disabled = true;
        searchButton.textContent = "Searching...";
        messageContainer.innerHTML = "";
        resultsContainer.innerHTML = "Searching food database...";

        try {
            const data = await NutriTrackAPI.searchExternalFoods(query);
            const foods = Array.isArray(data)
                ? data
                : (data.foods || data.data || []);

            if (!foods.length) {
                resultsContainer.innerHTML =
                    '<div class="alert alert-info">No foods found.</div>';
                return;
            }

            resultsContainer.innerHTML = foods.map((food, index) => {
                const name = food.name || food.description || "Unknown food";
                const calories = Number(food.calories ?? 0);
                const protein = Number(food.protein ?? 0);
                const carbohydrates = Number(food.carbohydrates ?? 0);
                const fat = Number(food.fat ?? 0);

                return `
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm food-card">
                            <div class="card-body">
                                <h5 class="fw-bold">${escapeFoodText(name)}</h5>
                                <p class="text-secondary small">Nutrition per 100 g</p>

                                <div class="nutrition-value">
                                    ${calories.toFixed(1)} kcal
                                </div>

                                <hr>

                                <div class="small mb-3">
                                    <div>Protein: ${protein.toFixed(1)} g</div>
                                    <div>Carbohydrates: ${carbohydrates.toFixed(1)} g</div>
                                    <div>Fat: ${fat.toFixed(1)} g</div>
                                </div>

                                <button
                                    class="btn btn-success w-100"
                                    data-add-food="${index}">
                                    + Add to Diary
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join("");

            resultsContainer.querySelectorAll("[data-add-food]").forEach(button => {
                button.addEventListener("click", () => {
                    const food = foods[Number(button.dataset.addFood)];
                    showAddFoodForm(food);
                });
            });

        } catch (error) {
            console.error(error);
            messageContainer.innerHTML = `
                <div class="alert alert-danger">
                    ${escapeFoodText(error.message)}
                </div>
            `;
            resultsContainer.innerHTML = "";
        } finally {
            searchButton.disabled = false;
            searchButton.textContent = "Search";
        }
    }

    searchButton.addEventListener("click", searchFoods);

    searchInput.addEventListener("keydown", event => {
        if (event.key === "Enter") searchFoods();
    });
}

function showAddFoodForm(food) {
    const name = food.name || food.description || "Unknown food";

    const calories = Number(food.calories ?? 0);
    const protein = Number(food.protein ?? 0);
    const carbohydrates = Number(food.carbohydrates ?? 0);
    const fat = Number(food.fat ?? 0);

    const modal = document.createElement("div");

    modal.className = "card shadow-lg mt-3";
    modal.id = "addFoodPanel";

    modal.innerHTML = `
        <div class="card-body">
            <h5 class="fw-bold">Add ${escapeFoodText(name)}</h5>

            <div class="mb-3">
                <label class="form-label" for="servingGrams">
                    Serving size (grams)
                </label>
                <input
                    type="number"
                    class="form-control"
                    id="servingGrams"
                    min="1"
                    max="5000"
                    step="1"
                    value="100">
            </div>

            <div class="mb-3">
                <label class="form-label" for="mealType">
                    Select meal
                </label>
                <select class="form-select" id="mealType">
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="snack">Snack</option>
                </select>
            </div>

            <div id="addFoodMessage" class="small mb-2"></div>

            <button class="btn btn-success" id="confirmAddFood">
                Save to Diary
            </button>

            <button class="btn btn-outline-secondary" id="cancelAddFood">
                Cancel
            </button>
        </div>
    `;

    document.getElementById("addFoodPanel")?.remove();
    document.getElementById("foodResults").after(modal);
    modal.scrollIntoView({
    behavior: "smooth",
    block: "center"
});

    document.getElementById("cancelAddFood").addEventListener("click", () => {
        modal.remove();
    });

    document.getElementById("confirmAddFood").addEventListener("click", async () => {
        const grams = Number(document.getElementById("servingGrams").value);
        const mealType = document.getElementById("mealType").value;
        const message = document.getElementById("addFoodMessage");
        const saveButton = document.getElementById("confirmAddFood");

        if (!Number.isFinite(grams) || grams <= 0 || grams > 5000) {
            message.innerHTML =
                '<span class="text-danger">Enter a valid serving size.</span>';
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = "Saving...";

        try {
            const foodData = {
                name: String(name).substring(0, 150),
                calories_per_100g: calories,
                protein_per_100g: protein,
                carbohydrates_per_100g: carbohydrates,
                fat_per_100g: fat
            };

            // Save the food in the local database.
            const createdFood = await NutriTrackAPI.createFood(foodData);

            // Check if a meal already exists for the selected date and type.
            const today = new Date().toLocaleDateString("en-CA");
            const meals = await NutriTrackAPI.getMeals(DEMO_USER_ID);

            let meal = meals.find(item =>
                item.meal_date === today &&
                item.meal_type === mealType
            );

            // Create the meal if it does not exist.
            if (!meal) {
                const createdMeal = await NutriTrackAPI.createMeal({
                    user_id: DEMO_USER_ID,
                    meal_date: today,
                    meal_type: mealType
                });

                meal = { meal_id: createdMeal.meal_id };
            }

            // Add the selected food to the meal.
            await NutriTrackAPI.createMealEntry({
                meal_id: Number(meal.meal_id),
                food_id: Number(createdFood.food_id),
                serving_grams: grams
            });

            message.innerHTML =
                '<span class="text-success">Food added successfully!</span>';

            setTimeout(() => {
                modal.remove();
            }, 900);

        } catch (error) {
            console.error(error);
            message.innerHTML = `
                <span class="text-danger">${escapeFoodText(error.message)}</span>
            `;
            saveButton.disabled = false;
            saveButton.textContent = "Save to Diary";
        }
    });
}

async function initializeDashboard() {
    const message = document.getElementById("dashboardMessage");

    try {
        const today = new Date().toLocaleDateString("en-CA");

        const meals = await NutriTrackAPI.getMeals(DEMO_USER_ID);

        const dayMeals = meals.filter(meal =>
            meal.meal_date === today
        );

        let totalCalories = 0;
        let totalProtein = 0;
        let totalCarbs = 0;
        let totalFat = 0;
        let totalFoods = 0;

        const mealTypes = ["breakfast", "lunch", "dinner", "snack"];

        for (const type of mealTypes) {
            const container = document.getElementById(`dashboard-${type}`);

            const meal = dayMeals.find(item =>
                item.meal_type === type
            );

            if (!meal) {
                container.innerHTML =
                    '<p class="text-secondary small mb-0">No foods added yet.</p>';
                continue;
            }

            const entries = await NutriTrackAPI.getMealEntries(meal.meal_id);

            if (!entries.length) {
                container.innerHTML =
                    '<p class="text-secondary small mb-0">No foods added yet.</p>';
                continue;
            }

            totalFoods += entries.length;

            let mealCalories = 0;

            container.innerHTML = entries.map(entry => {
                const grams = Number(entry.serving_grams);
                const calories = Number(entry.calories_per_100g) * grams / 100;
                const protein = Number(entry.protein_per_100g) * grams / 100;
                const carbs = Number(entry.carbohydrates_per_100g) * grams / 100;
                const fat = Number(entry.fat_per_100g) * grams / 100;

                totalCalories += calories;
                totalProtein += protein;
                totalCarbs += carbs;
                totalFat += fat;
                mealCalories += calories;

                return `
                    <div class="border-top py-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">
                                ${escapeFoodText(entry.food_name)}
                            </span>
                            <span class="fw-semibold">
                                ${calories.toFixed(0)} kcal
                            </span>
                        </div>

                        <div class="text-secondary small">
                            ${grams.toFixed(0)} g
                        </div>
                    </div>
                `;
            }).join("");

            container.innerHTML += `
                <div class="border-top pt-2 mt-1 fw-semibold text-success">
                    Meal total: ${mealCalories.toFixed(0)} kcal
                </div>
            `;
        }

        document.getElementById("dashboardCalories").textContent =
            `${totalCalories.toFixed(0)} kcal`;

        document.getElementById("dashboardMeals").textContent =
            dayMeals.length;

        document.getElementById("dashboardFoods").textContent =
            totalFoods;

        document.getElementById("dashboardProtein").textContent =
            `${totalProtein.toFixed(1)} g`;

        document.getElementById("dashboardCarbs").textContent =
            `${totalCarbs.toFixed(1)} g`;

        document.getElementById("dashboardFat").textContent =
            `${totalFat.toFixed(1)} g`;

    } catch (error) {
        console.error("Dashboard loading error:", error);

        if (message) {
            message.textContent = error.message;
            message.className = "text-danger small mb-3";
        }
    }
}

async function initializeHistory() {
    const message = document.getElementById("historyMessage");
    const filter = document.getElementById("historyFilter");
    const tableBody = document.getElementById("historyTableBody");

    async function loadHistory() {
        try {
            message.textContent = "";
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        Loading history...
                    </td>
                </tr>
            `;

            const meals = await NutriTrackAPI.getMeals(DEMO_USER_ID);

            // Group meals and food entries by date.
            const history = {};

            for (const meal of meals) {
                const date = meal.meal_date;

                if (!date) continue;

                if (!history[date]) {
                    history[date] = {
                        meals: 0,
                        foods: 0,
                        calories: 0
                    };
                }

                history[date].meals++;

                const entries = await NutriTrackAPI.getMealEntries(
                    meal.meal_id
                );

                history[date].foods += entries.length;

                for (const entry of entries) {
                    const grams = Number(entry.serving_grams) || 0;
                    const caloriesPer100g =
                        Number(entry.calories_per_100g) || 0;

                    history[date].calories +=
                        caloriesPer100g * grams / 100;
                }
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const selectedPeriod = filter.value;

            let dates = Object.keys(history).sort((a, b) =>
                b.localeCompare(a)
            );

            if (selectedPeriod !== "all") {
                const days = Number(selectedPeriod);
                const startDate = new Date(today);

                startDate.setDate(startDate.getDate() - (days - 1));

                const startString = [
                    startDate.getFullYear(),
                    String(startDate.getMonth() + 1).padStart(2, "0"),
                    String(startDate.getDate()).padStart(2, "0")
                ].join("-");

                const todayString = [
                    today.getFullYear(),
                    String(today.getMonth() + 1).padStart(2, "0"),
                    String(today.getDate()).padStart(2, "0")
                ].join("-");

                dates = dates.filter(date =>
                    date >= startString && date <= todayString
                );
            }

            const periodLabels = {
                "7": "Last 7 days",
                "30": "Last 30 days",
                "all": "All time"
            };

            document.getElementById("historyPeriod").textContent =
                periodLabels[selectedPeriod];

            document.getElementById("historyPeriodDescription").textContent =
                selectedPeriod === "all"
                    ? "All days with recorded meals."
                    : `Showing recorded days from the ${periodLabels[selectedPeriod].toLowerCase()}.`;

            document.getElementById("historyStatus").textContent =
                `${dates.length} recorded ${dates.length === 1 ? "day" : "days"}`;

            document.getElementById("historyStatusDescription").textContent =
                dates.length > 0
                    ? "Your nutrition records are available below."
                    : "No meals recorded during this period.";

            if (dates.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-icon">▤</div>
                            <h6 class="fw-bold mt-3">Nothing to show yet</h6>
                            <p class="text-secondary small mb-0">
                                Add meals to your diary to build your history.
                            </p>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = dates.map(date => {
                const day = history[date];

                const formattedDate = new Date(
                    `${date}T00:00:00`
                ).toLocaleDateString(undefined, {
                    day: "numeric",
                    month: "short",
                    year: "numeric"
                });

                return `
                    <tr>
                        <td class="fw-semibold">
                            ${formattedDate}
                        </td>
                        <td>${day.meals}</td>
                        <td>${day.foods}</td>
                        <td class="fw-semibold">
                            ${day.calories.toFixed(0)} kcal
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success">
                                Recorded
                            </span>
                        </td>
                    </tr>
                `;
            }).join("");

        } catch (error) {
            console.error("History loading error:", error);

            message.textContent = error.message;
            message.className = "text-danger small mb-3";

            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        Could not load nutrition history.
                    </td>
                </tr>
            `;
        }
    }

    filter.addEventListener("change", loadHistory);

    await loadHistory();
}

async function initializeProfile() {
    const form = document.getElementById("profileForm");
    const message = document.getElementById("profileMessage");
    const saveButton = document.getElementById("saveProfileButton");

    if (!form) return;

    let existingGoals = [];

    function showMessage(text, type = "success") {
        message.textContent = text;
        message.className = `alert alert-${type} small mb-3`;
    }

    function updateProfileCard(name, email) {
    const displayName = name?.trim() || "User";
    const displayEmail = email?.trim() || "";

    document.getElementById("profileDisplayName").textContent = displayName;
    document.getElementById("profileDisplayEmail").textContent = displayEmail;

    document.getElementById("profileAvatar").textContent =
        displayName.charAt(0).toUpperCase();
}

    try {
        const [userResponse, goalsResponse] = await Promise.all([
            NutriTrackAPI.getUser(DEMO_USER_ID),
            NutriTrackAPI.getNutritionGoals(DEMO_USER_ID)
        ]);

        const user = userResponse.user;
        existingGoals = Array.isArray(goalsResponse)
            ? goalsResponse
            : [];

        if (!user) {
            throw new Error("User profile could not be found.");
        }

        document.getElementById("username").value = user.name || "";
        document.getElementById("email").value = user.email || "";

        const calorieGoal = existingGoals.find(
            goal => goal.goal_type === "daily_calories"
        );

        const proteinGoal = existingGoals.find(
            goal => goal.goal_type === "daily_protein"
        );

        document.getElementById("calorieGoal").value =
            calorieGoal ? Number(calorieGoal.target_value) : "";

        document.getElementById("proteinGoal").value =
            proteinGoal ? Number(proteinGoal.target_value) : "";

        updateProfileCard(user.name || "User", user.email || "");

    } catch (error) {
        console.error("Profile loading error:", error);
        showMessage(error.message, "danger");
        form.querySelectorAll("input, button").forEach(element => {
            element.disabled = true;
        });
        return;
    }

    form.addEventListener("submit", async event => {
        event.preventDefault();

        const name = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();

        const calorieValue =
            document.getElementById("calorieGoal").value.trim();

        const proteinValue =
            document.getElementById("proteinGoal").value.trim();

        if (!name || !email) {
            showMessage("Name and email are required.", "warning");
            return;
        }

        if (name.length > 100 || email.length > 150) {
            showMessage("Name or email is too long.", "warning");
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showMessage("Please enter a valid email address.", "warning");
            return;
        }

        const calorieTarget = calorieValue === ""
            ? null
            : Number(calorieValue);

        const proteinTarget = proteinValue === ""
            ? null
            : Number(proteinValue);

        if (
            calorieTarget !== null &&
            (!Number.isFinite(calorieTarget) ||
                calorieTarget <= 0 ||
                calorieTarget > 100000)
        ) {
            showMessage("Enter a valid calorie target.", "warning");
            return;
        }

        if (
            proteinTarget !== null &&
            (!Number.isFinite(proteinTarget) ||
                proteinTarget <= 0 ||
                proteinTarget > 10000)
        ) {
            showMessage("Enter a valid protein target.", "warning");
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = "Saving...";

        try {
            // Update personal information.
            await NutriTrackAPI.updateUser(DEMO_USER_ID, {
                name: name,
                email: email
            });

            // Save or update each nutrition goal.
            const goalValues = [
                {
                    type: "daily_calories",
                    value: calorieTarget
                },
                {
                    type: "daily_protein",
                    value: proteinTarget
                }
            ];

            for (const item of goalValues) {
                const existingGoal = existingGoals.find(
                    goal => goal.goal_type === item.type
                );

                // Empty input means delete an existing target.
                if (item.value === null) {
                    if (existingGoal) {
                        await NutriTrackAPI.deleteNutritionGoal(existingGoal.goal_id);
                    }
                    continue;
                }

                if (existingGoal) {
                    await NutriTrackAPI.updateNutritionGoal(
                        existingGoal.goal_id,
                        { target_value: item.value }
                    );
                } else {
                    await NutriTrackAPI.createNutritionGoal({
                        user_id: DEMO_USER_ID,
                        goal_type: item.type,
                        target_value: item.value
                    });
                }
            }

            // Refresh the saved goals.
            const refreshedGoals =
                await NutriTrackAPI.getNutritionGoals(DEMO_USER_ID);

            existingGoals = Array.isArray(refreshedGoals)
                ? refreshedGoals
                : [];

            updateProfileCard(name, email);

            showMessage("Profile and nutrition goals saved successfully.");

        } catch (error) {
            console.error("Profile saving error:", error);
            showMessage(error.message, "danger");
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = "Save changes";
        }
    });

    form.addEventListener("reset", () => {
        setTimeout(() => {
            const name = document.getElementById("username").value;
            const email = document.getElementById("email").value;
            updateProfileCard(name || "User", email || "");
            message.textContent = "";
            message.className = "small mb-3";
        }, 0);
    });
}

async function initializeDiary() {
    const dateInput = document.getElementById("diaryDate");
    if (!dateInput) return;

    const today = new Date().toLocaleDateString("en-CA");
    dateInput.value = today;

    async function loadDiary() {
        const selectedDate = dateInput.value;

        try {
            const meals = await NutriTrackAPI.getMeals(DEMO_USER_ID);
            const dayMeals = meals.filter(meal =>
                meal.meal_date === selectedDate
            );

            const mealTypes = ["breakfast", "lunch", "dinner", "snack"];

            let totalFoods = 0;
            let totalCalories = 0;
            let totalProtein = 0;
            let totalCarbs = 0;
            let totalFat = 0;

            for (const type of mealTypes) {
                const meal = dayMeals.find(item => item.meal_type === type);
                const container = document.getElementById(`diary-${type}`);

                if (!container) continue;

                if (!meal) {
                    container.innerHTML =
                        '<p class="text-secondary small mb-0">No foods added yet.</p>';
                    continue;
                }

                const entries = await NutriTrackAPI.getMealEntries(meal.meal_id);

                if (!entries.length) {
                    container.innerHTML =
                        '<p class="text-secondary small mb-0">No foods added yet.</p>';
                    continue;
                }

                totalFoods += entries.length;

                container.innerHTML = entries.map(entry => {
                const grams = Number(entry.serving_grams);
                const caloriesPer100g = Number(entry.calories_per_100g);

                const entryCalories = caloriesPer100g * grams / 100;

                const entryProtein =
                    Number(entry.protein_per_100g) * grams / 100;

                const entryCarbs =
                    Number(entry.carbohydrates_per_100g) * grams / 100;

                const entryFat =
                    Number(entry.fat_per_100g) * grams / 100;

                totalCalories += entryCalories;
                totalProtein += entryProtein;
                totalCarbs += entryCarbs;
                totalFat += entryFat;

                return `
                    <div class="d-flex justify-content-between align-items-center border-top py-2">
                        <div>
                            <div class="fw-semibold">${escapeFoodText(entry.food_name)}</div>
                            <div class="text-secondary small">${grams.toFixed(0)} g</div>

                            <button
                                class="btn btn-sm btn-outline-primary mt-1"
                                data-edit-entry="${entry.entry_id}"
                                data-current-grams="${grams}">
                                Edit
                            </button>
                        </div>

                        <div class="text-end">
                            <div class="fw-semibold">
                                ${entryCalories.toFixed(1)} kcal
                            </div>

                            <div class="text-secondary small">
                                P: ${entryProtein.toFixed(1)} g |
                                C: ${entryCarbs.toFixed(1)} g |
                                F: ${entryFat.toFixed(1)} g
                            </div>

                            <button
                                class="btn btn-sm btn-outline-danger mt-1"
                                data-delete-entry="${entry.entry_id}">
                                Remove
                            </button>
                        </div>
                    </div>
                `;
            }).join("");

                container.querySelectorAll("[data-edit-entry]").forEach(button => {
                    button.addEventListener("click", async () => {
                        const entryId = Number(button.dataset.editEntry);
                        const currentGrams = Number(button.dataset.currentGrams);

                        const newGrams = prompt(
                            "Enter the new serving size in grams:",
                            currentGrams
                        );

                        if (newGrams === null) {
                            return;
                        }

                        const grams = Number(newGrams);

                        if (!Number.isFinite(grams) || grams <= 0 || grams > 5000) {
                            alert("Please enter a value between 1 and 5000 grams.");
                            return;
                        }

                        button.disabled = true;
                        button.textContent = "Saving...";

                        try {
                            await NutriTrackAPI.updateMealEntry(entryId, {
                                serving_grams: grams
                            });

                            await loadDiary();

                        } catch (error) {
                            console.error("Update food error:", error);
                            alert(error.message);

                            button.disabled = false;
                            button.textContent = "Edit";
                        }
                    });
                });
            }

            const stats = document.querySelectorAll(".diary-stat-value");

            if (stats[0]) stats[0].textContent = dayMeals.length;
            if (stats[1]) stats[1].textContent = totalFoods;
            if (stats[2]) {
                stats[2].textContent = `${totalCalories.toFixed(0)} kcal`;
            }
            const macroStats = document.getElementById("diaryMacroStats");

if (macroStats) {
    macroStats.innerHTML = `
        <div class="row g-3 mt-2">
            <div class="col-4">
                <div class="border rounded p-3 text-center">
                    <div class="text-secondary small">Protein</div>
                    <div class="fw-bold">${totalProtein.toFixed(1)} g</div>
                </div>
            </div>

            <div class="col-4">
                <div class="border rounded p-3 text-center">
                    <div class="text-secondary small">Carbs</div>
                    <div class="fw-bold">${totalCarbs.toFixed(1)} g</div>
                </div>
            </div>

            <div class="col-4">
                <div class="border rounded p-3 text-center">
                    <div class="text-secondary small">Fat</div>
                    <div class="fw-bold">${totalFat.toFixed(1)} g</div>
                </div>
            </div>
        </div>
    `;
}

        } catch (error) {
            console.error("Diary loading error:", error);

            const message = document.getElementById("diaryMessage");
            if (message) {
                message.textContent = error.message;
            }
        }
    }

    dateInput.addEventListener("change", loadDiary);
    await loadDiary();
}

function escapeFoodText(value) {
    const element = document.createElement("div");
    element.textContent = String(value);
    return element.innerHTML;
}

// SPA navigation
document.addEventListener("click", event => {
    const button = event.target.closest("[data-page]");
    if (!button) return;

    event.preventDefault();
    loadPage(button.dataset.page);
});

// Initial page
loadPage("dashboard");
