<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="health-page">
        <div class="page-header">
            <div class="page-title">
                <h1>Health Records</h1>
                <p>Track and manage your pets' health information</p>
            </div>
            <div class="page-actions">
                <button class="btn btn-outline" onclick="exportPDF()">
                    <i class="icon-download"></i>
                    Export PDF
                </button>
                <button class="btn btn-primary" onclick="addRecord()">
                    <i class="icon-plus"></i>
                    Add Record
                </button>
            </div>
        </div>
        
        <div class="pet-selector">
            <h3>Select Pet</h3>
            <div class="pet-tabs" id="petTabs">
                <!-- Pet tabs will be loaded here -->
            </div>
        </div>
        
        <div class="health-overview">
            <div class="health-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-records"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number" id="totalRecords">0</span>
                        <span class="stat-label">Total Records</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-vaccinations"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number" id="vaccinations">0</span>
                        <span class="stat-label">Vaccinations</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-checkups"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number" id="checkups">0</span>
                        <span class="stat-label">Check-ups</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="health-content">
            <div class="main-column">
                <div class="health-score-section">
                    <div class="health-score-card" id="healthScoreCard">
                        <div class="health-score-header">
                            <h2>Health Score</h2>
                        </div>
                        <div class="health-score-display">
                            <div class="score-circle">
                                <div class="score-fill" id="scoreCircle"></div>
                                <span class="score-value" id="scoreValue">70%</span>
                            </div>
                            <span class="score-label">Excellent Health</span>
                        </div>
                        <div class="score-details">
                            <div class="score-item">
                                <span class="score-label">Total Records:</span>
                                <span class="score-number" id="totalRecordsDetail">6</span>
                            </div>
                            <div class="score-item">
                                <span class="score-label">Last Check-up:</span>
                                <span class="score-date" id="lastCheckup">No records</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Health Records for <span id="selectedPetName">Chispa</span></h2>
                        <p>Complete medical history and health information</p>
                    </div>
                    
                    <div class="records-container" id="recordsContainer">
                        <!-- Records will be loaded here -->
                    </div>
                </div>
            </div>
            
            <div class="sidebar-column">
                <div class="section-card">
                    <div class="section-header">
                        <h2>Upcoming</h2>
                    </div>
                    
                    <div class="upcoming-appointments" id="upcomingAppointments">
                        <p class="no-data">No upcoming appointments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPetId = null;
        let pets = [];
        let healthRecords = [];
        
        // Load initial data
        async function loadHealthData() {
            try {
                const [petsResponse, recordsResponse] = await Promise.all([
                    fetch('/api/profile.php', { credentials: 'include' }),
                    fetch('/api/health.php', { credentials: 'include' })
                ]);
                
                pets = await petsResponse.json();
                healthRecords = await recordsResponse.json();
                
                renderPetTabs();
                if (pets.length > 0) {
                    selectPet(pets[0].id);
                }
            } catch (error) {
                console.error('Error loading health data:', error);
            }
        }
        
        function renderPetTabs() {
            const container = document.getElementById('petTabs');
            if (pets.length === 0) {
                container.innerHTML = '<p class="no-pets">No pets found. <a href="profile.php">Add a pet first</a></p>';
                return;
            }
            
            container.innerHTML = pets.map(pet => `
                <button class="pet-tab ${currentPetId === pet.id ? 'active' : ''}" 
                        onclick="selectPet('${pet.id}')">
                    <div class="pet-tab-avatar">
                        <img src="${pet.avatar_url || 'images/default-pet.png'}" alt="${pet.name}">
                    </div>
                    <span class="pet-tab-name">${pet.name}</span>
                </button>
            `).join('');
        }
        
        function selectPet(petId) {
            currentPetId = petId;
            const pet = pets.find(p => p.id === petId);
            
            if (pet) {
                document.getElementById('selectedPetName').textContent = pet.name;
                renderPetTabs(); // Re-render to update active state
                loadPetHealthRecords(petId);
                updateHealthScore(pet);
            }
        }
        
        async function loadPetHealthRecords(petId) {
            try {
                const response = await fetch(`/api/health.php?pet_id=${petId}`, { credentials: 'include' });
                const records = await response.json();
                renderHealthRecords(records);
                updateStats(records);
            } catch (error) {
                console.error('Error loading pet health records:', error);
            }
        }
        
        function renderHealthRecords(records) {
            const container = document.getElementById('recordsContainer');
            
            if (records.length === 0) {
                container.innerHTML = `
                    <div class="no-records">
                        <div class="no-records-icon">
                            <i class="icon-records"></i>
                        </div>
                        <h3>No Health Records</h3>
                        <p>Start tracking ${document.getElementById('selectedPetName').textContent}'s health by adding the first record</p>
                        <button class="btn btn-primary" onclick="addRecord()">Add First Record</button>
                    </div>
                `;
                return;
            }
            
            // Group records by date
            const groupedRecords = records.reduce((groups, record) => {
                const date = new Date(record.created_at).toDateString();
                if (!groups[date]) groups[date] = [];
                groups[date].push(record);
                return groups;
            }, {});
            
            container.innerHTML = Object.entries(groupedRecords).map(([date, dayRecords]) => `
                <div class="records-day">
                    <div class="day-header">
                        <h3>${formatDate(date)}</h3>
                    </div>
                    <div class="day-records">
                        ${dayRecords.map(record => `
                            <div class="record-item">
                                <div class="record-icon">
                                    <i class="icon-${record.type}"></i>
                                </div>
                                <div class="record-content">
                                    <h4>${record.title}</h4>
                                    <p>${record.description}</p>
                                    <div class="record-meta">
                                        <span class="record-type">${record.type}</span>
                                        <span class="record-time">${formatTime(record.created_at)}</span>
                                    </div>
                                </div>
                                <div class="record-actions">
                                    <button class="action-btn" onclick="editRecord('${record.id}')">
                                        <i class="icon-edit"></i>
                                    </button>
                                    <button class="action-btn" onclick="deleteRecord('${record.id}')">
                                        <i class="icon-delete"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        }
        
        function updateHealthScore(pet) {
            const score = pet.health_score || 70;
            const scoreCircle = document.getElementById('scoreCircle');
            const scoreValue = document.getElementById('scoreValue');
            
            scoreValue.textContent = score + '%';
            scoreCircle.style.background = `conic-gradient(#4ade80 ${score * 3.6}deg, #e5e7eb 0deg)`;
            
            // Update score label
            const scoreLabel = document.querySelector('.score-label');
            if (score >= 80) scoreLabel.textContent = 'Excellent Health';
            else if (score >= 60) scoreLabel.textContent = 'Good Health'; 
            else scoreLabel.textContent = 'Needs Attention';
        }
        
        function updateStats(records) {
            document.getElementById('totalRecords').textContent = records.length;
            document.getElementById('vaccinations').textContent = records.filter(r => r.type === 'vaccination').length;
            document.getElementById('checkups').textContent = records.filter(r => r.type === 'checkup').length;
            
            document.getElementById('totalRecordsDetail').textContent = records.length;
            
            const lastRecord = records.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
            document.getElementById('lastCheckup').textContent = lastRecord ? formatDate(lastRecord.created_at) : 'No records';
        }
        
        function addRecord() {
            if (!currentPetId) {
                alert('Please select a pet first');
                return;
            }
            // Implementation for adding health record
            console.log('Add health record for pet:', currentPetId);
        }
        
        function editRecord(recordId) {
            console.log('Edit record:', recordId);
        }
        
        function deleteRecord(recordId) {
            if (confirm('Are you sure you want to delete this record?')) {
                console.log('Delete record:', recordId);
            }
        }
        
        function exportPDF() {
            if (!currentPetId) {
                alert('Please select a pet first');
                return;
            }
            console.log('Export PDF for pet:', currentPetId);
        }
        
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        function formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', loadHealthData);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>