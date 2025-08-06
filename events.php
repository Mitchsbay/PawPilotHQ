<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="events-page">
        <div class="page-header">
            <div class="page-title">
                <h1>Events</h1>
                <p>Manage pet appointments and community events</p>
            </div>
        </div>
        
        <div class="events-content">
            <div class="main-column">
                <div class="calendar-section">
                    <div class="calendar-header">
                        <h2>Event Calendar</h2>
                        <div class="calendar-nav">
                            <button class="btn btn-outline" onclick="previousMonth()">←</button>
                            <span id="currentMonth">December 2024</span>
                            <button class="btn btn-outline" onclick="nextMonth()">→</button>
                        </div>
                    </div>
                    
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let events = [];
        
        async function loadEvents() {
            try {
                const response = await fetch('/api/events.php', { credentials: 'include' });
                events = await response.json();
                renderCalendar();
            } catch (error) {
                console.error('Error loading events:', error);
            }
        }
        
        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Update month display
            document.getElementById('currentMonth').textContent = 
                currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            
            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            let calendarHTML = `
                <div class="calendar-header-row">
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>
                </div>
            `;
            
            // Add empty cells for days before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                calendarHTML += '<div class="calendar-day empty"></div>';
            }
            
            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayEvents = events.filter(event => {
                    const eventDate = new Date(event.date);
                    return eventDate.toDateString() === date.toDateString();
                });
                
                const isToday = date.toDateString() === new Date().toDateString();
                
                calendarHTML += `
                    <div class="calendar-day ${isToday ? 'today' : ''}" onclick="showDayEvents('${date.toISOString()}')">
                        <span class="day-number">${day}</span>
                        ${dayEvents.length > 0 ? `<div class="day-events">${dayEvents.length} event${dayEvents.length > 1 ? 's' : ''}</div>` : ''}
                    </div>
                `;
            }
            
            grid.innerHTML = calendarHTML;
        }
        
        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }
        
        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }
        
        function showDayEvents(dateString) {
            const date = new Date(dateString);
            const dayEvents = events.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate.toDateString() === date.toDateString();
            });
            
            if (dayEvents.length === 0) {
                if (confirm('No events on this day. Would you like to add one?')) {
                    addEvent(dateString);
                }
                return;
            }
            
            // Show events modal or navigate to day view
            window.location.href = `/events.php?date=${date.toISOString().split('T')[0]}`;
        }
        
        function addEvent(dateString = null) {
            const title = prompt('Event title:');
            const description = prompt('Description:');
            const eventDate = dateString || prompt('Date (YYYY-MM-DD):');
            const eventType = prompt('Type (vet_appointment, vaccination, grooming, etc.):') || 'other';
            
            if (title && eventDate) {
                fetch('/api/events.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        title: title,
                        description: description,
                        event_date: eventDate,
                        event_type: eventType
                    })
                }).then(() => {
                    loadEvents();
                });
            }
        }
        
        // Load events when page loads
        document.addEventListener('DOMContentLoaded', loadEvents);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>