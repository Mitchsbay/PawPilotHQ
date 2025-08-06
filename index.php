<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <div class="landing-page">
        <div class="hero-section">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Your Pet's <span class="text-primary">Digital</span> Companion</h1>
                    <p>Track health records, connect with pet communities, and never lose sight of your beloved companions with PawPilot HQ.</p>
                    
                    <div class="stats-container">
                        <div class="stat-item">
                            <span class="stat-number">50K+</span>
                            <span class="stat-label">Happy Pets</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">25K+</span>
                            <span class="stat-label">Pet Parents</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">1K+</span>
                            <span class="stat-label">Reunions</span>
                        </div>
                    </div>
                    
                    <div class="hero-actions">
                        <a href="signup.php" class="btn btn-primary btn-large">Get Started Free</a>
                        <a href="login.php" class="btn btn-outline">Sign In</a>
                    </div>
                </div>
                
                <div class="hero-image">
                    <img src="https://images.pexels.com/photos/1108099/pexels-photo-1108099.jpeg" alt="Happy pets">
                </div>
            </div>
        </div>
        
        <div class="features-section">
            <div class="section-header">
                <h2>Everything Your Pet Needs</h2>
                <p>From health tracking to social connections, PawPilot HQ provides all the tools modern pet parents need.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-health"></i>
                    </div>
                    <h3>Health Tracking</h3>
                    <p>Monitor your pet's health with comprehensive records and vaccination reminders.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-community"></i>
                    </div>
                    <h3>Pet Community</h3>
                    <p>Connect with fellow pet owners and share experiences in our vibrant community.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-alert"></i>
                    </div>
                    <h3>Lost Pet Alerts</h3>
                    <p>Quickly report and find lost pets in your area with our alert system.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-profile"></i>
                    </div>
                    <h3>Pet Profiles</h3>
                    <p>Create detailed profiles for each of your beloved pets.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-group"></i>
                    </div>
                    <h3>Group Chats</h3>
                    <p>Join breed-specific groups and local pet owner communities.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="icon-secure"></i>
                    </div>
                    <h3>Safe & Secure</h3>
                    <p>Your pet's data is protected with enterprise-grade security.</p>
                </div>
            </div>
        </div>
        
        <div class="testimonials-section">
            <div class="section-header">
                <h2>Loved by Pet Parents</h2>
                <p>Join thousands of happy pet owners who trust PawPilot HQ.</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"PawPilot HQ has been amazing for keeping track of my dog's health records. The community is so helpful too!"</p>
                    <div class="testimonial-author">
                        <strong>Sarah Johnson</strong>
                        <span>Golden Retriever Parent</span>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"When my cat went missing, the lost pet alert feature helped me find her within 2 hours. Thank you PawPilot HQ!"</p>
                    <div class="testimonial-author">
                        <strong>Mike Chen</strong>
                        <span>Cat Dad</span>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"The health tracking feature is incredible. I love how easy it is to keep track of vaccinations and vet visits."</p>
                    <div class="testimonial-author">
                        <strong>Emma Davis</strong>
                        <span>Multi-Pet Owner</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="cta-section">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <p>Join the PawPilot HQ community and give your pets the care they deserve.</p>
                <a href="signup.php" class="btn btn-primary btn-large">Create Your Free Account</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Welcome back! 🐾</h1>
            <p>Here's what's happening with your pets today</p>
        </div>
        
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-pets"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" id="petCount">2</span>
                    <span class="stat-label">My Pets</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-health"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" id="healthRecords">45</span>
                    <span class="stat-label">Health Records</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-posts"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" id="postsShared">12</span>
                    <span class="stat-label">Posts Shared</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-groups"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" id="groupMembers">8</span>
                    <span class="stat-label">Group Members</span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="main-column">
                <div class="section-card">
                    <div class="section-header">
                        <h2>My Pets</h2>
                        <p>Check-ups and quick actions</p>
                        <button class="btn btn-primary" onclick="window.location.href='profile.php'">Add Pet</button>
                    </div>
                    
                    <div class="pets-list" id="petsList">
                        <!-- Pets will be loaded here -->
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Recent Activity</h2>
                        <p>Latest updates from your pet community</p>
                    </div>
                    
                    <div class="activity-feed" id="activityFeed">
                        <!-- Activity will be loaded here -->
                    </div>
                    
                    <button class="btn btn-outline" onclick="window.location.href='social.php'">View All Activity</button>
                </div>
            </div>
            
            <div class="sidebar-column">
                <div class="section-card">
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                        <p>Common tasks for pet care</p>
                    </div>
                    
                    <div class="quick-actions">
                        <button class="action-btn" onclick="logHealthRecord()">
                            <i class="icon-health"></i>
                            Log Health Record
                        </button>
                        <button class="action-btn" onclick="sharePhoto()">
                            <i class="icon-camera"></i>
                            Share Pet Photo
                        </button>
                        <button class="action-btn" onclick="reportLostPet()">
                            <i class="icon-alert"></i>
                            Lost Pet Alert
                        </button>
                        <button class="action-btn" onclick="findGroup()">
                            <i class="icon-groups"></i>
                            Find Groups
                        </button>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Upcoming Events</h2>
                        <p>Important dates for your pets</p>
                    </div>
                    
                    <div class="upcoming-events" id="upcomingEvents">
                        <!-- Events will be loaded here -->
                    </div>
                    
                    <button class="btn btn-outline" onclick="window.location.href='events.php'">View Calendar</button>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Community Highlights</h2>
                        <p>Popular content from pet parents</p>
                    </div>
                    
                    <div class="community-highlights" id="communityHighlights">
                        <!-- Highlights will be loaded here -->
                    </div>
                    
                    <button class="btn btn-outline" onclick="window.location.href='social.php'">Explore Community</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard data
        async function loadDashboardData() {
            try {
                const [petsResponse, activityResponse, eventsResponse, highlightsResponse] = await Promise.all([
                    fetch('/api/profile.php', { credentials: 'include' }),
                    fetch('/api/posts.php?feed=recent&limit=5', { credentials: 'include' }),
                    fetch('/api/events.php?upcoming=true&limit=3', { credentials: 'include' }),
                    fetch('/api/posts.php?highlights=true&limit=3', { credentials: 'include' })
                ]);
                
                const pets = await petsResponse.json();
                const activity = await activityResponse.json();
                const events = await eventsResponse.json();
                const highlights = await highlightsResponse.json();
                
                renderPets(pets);
                renderActivity(activity);
                renderEvents(events);
                renderHighlights(highlights);
                updateStats(pets, activity, events);
                
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }
        
        function renderPets(pets) {
            const container = document.getElementById('petsList');
            if (pets.length === 0) {
                container.innerHTML = '<p class="no-data">No pets added yet. <a href="profile.php">Add your first pet</a></p>';
                return;
            }
            
            container.innerHTML = pets.map(pet => `
                <div class="pet-card">
                    <div class="pet-avatar">
                        <img src="${pet.avatar_url || 'images/default-pet.png'}" alt="${pet.name}">
                    </div>
                    <div class="pet-info">
                        <h3>${pet.name}</h3>
                        <p>${pet.breed} • ${pet.age} years old</p>
                        <div class="pet-health">
                            <span class="health-score ${pet.health_score >= 80 ? 'good' : pet.health_score >= 60 ? 'okay' : 'poor'}">
                                ${pet.health_score}% Health Score
                            </span>
                        </div>
                        <div class="pet-actions">
                            <span class="next-checkup">Next vaccination: ${formatDate(pet.next_vaccination)}</span>
                            <button class="btn btn-sm" onclick="window.location.href='profile.php?pet=${pet.id}'">View Details</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function renderActivity(activities) {
            const container = document.getElementById('activityFeed');
            if (activities.length === 0) {
                container.innerHTML = '<p class="no-data">No recent activity</p>';
                return;
            }
            
            container.innerHTML = activities.map(activity => `
                <div class="activity-item">
                    <div class="activity-avatar">
                        <img src="${activity.user_avatar || 'images/default-avatar.png'}" alt="User">
                    </div>
                    <div class="activity-content">
                        <p><strong>${activity.user_name}</strong> ${activity.action}</p>
                        <span class="activity-time">${formatTimeAgo(activity.created_at)}</span>
                    </div>
                </div>
            `).join('');
        }
        
        function renderEvents(events) {
            const container = document.getElementById('upcomingEvents');
            if (events.length === 0) {
                container.innerHTML = '<p class="no-data">No upcoming events</p>';
                return;
            }
            
            container.innerHTML = events.map(event => `
                <div class="event-item">
                    <div class="event-date">
                        <span class="event-day">${new Date(event.date).getDate()}</span>
                        <span class="event-month">${new Date(event.date).toLocaleDateString('en', {month: 'short'})}</span>
                    </div>
                    <div class="event-details">
                        <h4>${event.title}</h4>
                        <p>${event.description}</p>
                        <span class="event-time">${event.location}</span>
                    </div>
                </div>
            `).join('');
        }
        
        function renderHighlights(highlights) {
            const container = document.getElementById('communityHighlights');
            if (highlights.length === 0) {
                container.innerHTML = '<p class="no-data">No highlights available</p>';
                return;
            }
            
            container.innerHTML = highlights.map(highlight => `
                <div class="highlight-item">
                    <div class="highlight-content">
                        <h4>${highlight.title}</h4>
                        <p>${highlight.excerpt}</p>
                        <span class="highlight-stats">${highlight.likes} likes • ${highlight.comments} comments</span>
                    </div>
                </div>
            `).join('');
        }
        
        function updateStats(pets, activity, events) {
            document.getElementById('petCount').textContent = pets.length;
            document.getElementById('healthRecords').textContent = pets.reduce((total, pet) => total + (pet.health_records_count || 0), 0);
            document.getElementById('postsShared').textContent = activity.filter(a => a.type === 'post').length;
        }
        
        // Quick action functions
        function logHealthRecord() {
            window.location.href = 'health.php?action=add';
        }
        
        function sharePhoto() {
            window.location.href = 'social.php?action=post';
        }
        
        function reportLostPet() {
            window.location.href = 'map.php?action=report';
        }
        
        function findGroup() {
            window.location.href = 'groups.php';
        }
        
        // Utility functions
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }
        
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) return '1 day ago';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
            return formatDate(dateString);
        }
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', loadDashboardData);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>