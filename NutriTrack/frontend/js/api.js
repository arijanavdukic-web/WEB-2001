
const API_BASE_URL = "http://localhost/web2001/backend/public/api";

async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            "Content-Type": "application/json",
            ...options.headers
        }
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.error || "API request failed.");
    }

    return data;
}

const NutriTrackAPI = {
    async searchExternalFoods(query, pageSize = 10) {
        const params = new URLSearchParams({
            query: query,
            pageSize: pageSize
        });

        return apiRequest(
            `${API_BASE_URL}/external/foods?${params.toString()}`
        );
    },

    async getFoods() {
        return apiRequest(`${API_BASE_URL}/foods`);
    },

    async createFood(food) {
        return apiRequest(`${API_BASE_URL}/foods`, {
            method: "POST",
            body: JSON.stringify(food)
        });
    },

    async getMeals(userId = 1) {
        return apiRequest(
            `${API_BASE_URL}/meals?user_id=${userId}`
        );
    },

    async createMeal(meal) {
        return apiRequest(`${API_BASE_URL}/meals`, {
            method: "POST",
            body: JSON.stringify(meal)
        });
    },

    async getMealEntries(mealId) {
        return apiRequest(
            `${API_BASE_URL}/meal-entries?meal_id=${mealId}`
        );
    },

    async createMealEntry(entry) {
        return apiRequest(`${API_BASE_URL}/meal-entries`, {
            method: "POST",
            body: JSON.stringify(entry)
        });
    },

    async updateMealEntry(entryId, entry) {
        return apiRequest(`${API_BASE_URL}/meal-entries/${entryId}`, {
            method: "PUT",
            body: JSON.stringify(entry)
        });
    },

    async getUser(userId) {
        return apiRequest(`${API_BASE_URL}/users/${userId}`);
    },

    async updateUser(userId, userData) {
        return apiRequest(`${API_BASE_URL}/users/${userId}`, {
            method: "PUT",
            body: JSON.stringify(userData)
        });
    },

    async getNutritionGoals(userId) {
        return apiRequest(
            `${API_BASE_URL}/nutrition-goals?user_id=${userId}`
        );
    },

    async createNutritionGoal(goal) {
        return apiRequest(`${API_BASE_URL}/nutrition-goals`, {
            method: "POST",
            body: JSON.stringify(goal)
        });
    },

    async updateNutritionGoal(goalId, goalData) {
        return apiRequest(`${API_BASE_URL}/nutrition-goals/${goalId}`, {
            method: "PUT",
            body: JSON.stringify(goalData)
        });
    },

    async deleteNutritionGoal(goalId) {
        return apiRequest(`${API_BASE_URL}/nutrition-goals/${goalId}`, {
            method: "DELETE"
        });
    }


};
