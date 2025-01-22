<?php
// Include dependencies and header
include_once "./header.php";
require_once "../model/company.php";
require_once "../model/booking.php";
require_once "../model/service.php";
require_once "../model/staff.php";
require_once "../model/user.php";

// Initialize objects
$booking = new Booking();
$company = new Company();
$service = new Service();
$staff = new Staff();
$user = new User();

// Placeholder: Add logic to fetch necessary data if required.

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/styles/index.css">
    <script src="js/scripts/index.js" type="module"></script>
</head>

<body>
    <div class="app">
        <div class="sidebar desktop-only">
            <div class="sidebar__logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar">
                    <path d="M8 2v4" />
                    <path d="M16 2v4" />
                    <rect width="18" height="18" x="3" y="4" rx="2" />
                    <path d="M3 10h18" />
                </svg>
                <span class="sidebar__title">Vanilla Calendar</span>
            </div>

            <button class="button button--primary button--lg" data-event-create-button>Create event</button>

            <div class="mini-calendar" data-mini-calendar>
                <div class="mini-calendar__header">
                    <time class="mini-calendar__date" data-mini-calendar-date></time>

                    <div class="mini-calendar__controls">
                        <button class="button button--icon button--secondary button--sm" data-mini-calendar-previous-button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                        </button>

                        <button class="button button--icon button--secondary button--sm" data-mini-calendar-next-button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mini-calendar__content">
                    <ul class="mini-calendar__day-of-week-list">
                        <li class="mini-calendar__day-of-week">S</li>
                        <li class="mini-calendar__day-of-week">M</li>
                        <li class="mini-calendar__day-of-week">T</li>
                        <li class="mini-calendar__day-of-week">W</li>
                        <li class="mini-calendar__day-of-week">T</li>
                        <li class="mini-calendar__day-of-week">F</li>
                        <li class="mini-calendar__day-of-week">S</li>
                    </ul>

                    <ul class="mini-calendar__day-list" data-mini-calendar-day-list></ul>
                </div>
            </div>
        </div>
        <main class="main">
            <div class="nav">
                <button class="button button--icon button--secondary mobile-only" data-hamburger-button>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                        <line x1="4" x2="20" y1="12" y2="12" />
                        <line x1="4" x2="20" y1="6" y2="6" />
                        <line x1="4" x2="20" y1="18" y2="18" />
                    </svg>
                </button>

                <div class="nav__date-info">
                    <div class="nav__controls">
                        <button class="button button--secondary desktop-only" data-nav-today-button>Today</button>
                        <div class="nav__arrows">
                            <button class="button button--icon button--secondary" data-nav-previous-button>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                    <path d="m15 18-6-6 6-6" />
                                </svg>
                            </button>

                            <button class="button button--icon button--secondary" data-nav-next-button>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="button__icon">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <time class="nav__date" data-nav-date></time>
                </div>

                <div class="select desktop-only">
                    <select class="select__select" data-view-select>
                        <option value="day">Day</option>
                        <option value="week">Week</option>
                        <option value="month" selected>Month</option>
                    </select>
                </div>
            </div>
            <div class="calendar" data-calendar>
            </div>
        </main>
    </div>
</body>
</html>
