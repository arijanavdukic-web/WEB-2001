
# NutriTrack

NutriTrack is a web-based calorie and nutrition tracking application designed to help users monitor their daily food intake and develop healthier eating habits.

## Project Goals

The goal of NutriTrack is to provide a simple and user-friendly interface for tracking calories, recording meals, reviewing nutritional history, and managing personal nutrition goals.

## Planned Features

- User registration and login
- Dashboard with a daily nutrition overview
- Food search with nutritional information
- Daily meal diary
- Nutrition history
- User profile and nutrition preferences
- Integration with an external food nutrition API

## Technologies

- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- Mermaid for the database ERD

## Project Structure

```text
NutriTrack/
├── backend/
│   ├── config/
│   ├── dao/
│   ├── logs/
│   ├── middleware/
│   ├── routes/
│   └── services/
├── frontend/
│   ├── css/
│   ├── js/
│   ├── static/
│   └── views/
├── docs/
│   ├── erd.mmd
│   └── erd.png
├── .gitignore
└── README.md
```

## Current Milestone

The current milestone focuses on the project structure, static frontend, single-page navigation, responsive design, and preliminary database planning.

The frontend includes the following views:

- Dashboard
- Food Search
- Meal Diary
- Nutrition History
- User Profile
- Login
- Registration

The pages currently use sample content. Authentication, database operations, and external API integration are planned for future development.

## Running the Frontend

1. Clone or download the project repository.
2. Open the project folder in Visual Studio Code.
3. Open `frontend/views/index.html` using a local development server, such as the Live Server extension.
4. Navigate between the pages using the application's navigation.

Opening the HTML file directly through the `file://` protocol may prevent page navigation from working correctly.

## Database Design

The preliminary database design contains six entities:

- User
- Food
- Meal
- MealEntry
- DailySummary
- NutritionGoal

The draft entity-relationship diagram is available in `docs/erd.png`. Its editable source is stored in `docs/erd.mmd`.

## Project Status

This project is under development. The current frontend is a static prototype and does not yet persist user data or communicate with a backend.
