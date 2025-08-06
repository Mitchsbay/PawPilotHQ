<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="profile-page">
        <div class="page-header">
            <div class="page-title">
                <h1>My Pets</h1>
                <p>Manage your beloved companions' profiles and health information</p>
            </div>
            <button class="btn btn-primary" onclick="showAddPetModal()">
                <i class="icon-plus"></i>
                Add New Pet
            </button>
        </div>
        
        <div class="pets-overview">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number" id="totalPets">1</span>
                    <span class="stat-label">Total Pets</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="healthRecords">0</span>
                    <span class="stat-label">Health Records</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="dueVaccinations">0</span>
                    <span class="stat-label">Due Vaccinations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="activeVaccinations">0</span>
                    <span class="stat-label">Active Vaccinations</span>
                </div>
            </div>
        </div>
        
        <div class="pets-grid" id="petsGrid">
            <!-- Pets will be loaded here -->
        </div>
        
        <div class="add-pet-section">
            <div class="add-pet-card">
                <div class="add-pet-icon">
                    <i class="icon-plus"></i>
                </div>
                <div class="add-pet-content">
                    <h2>Add New Pet</h2>
                    <p>Create a profile for your new companion</p>
                    <button class="btn btn-primary" onclick="showAddPetModal()">Add Pet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Pet Modal -->
    <div id="addPetModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Pet</h2>
                <button class="modal-close" onclick="hideAddPetModal()">&times;</button>
            </div>
            
            <form id="addPetForm" class="pet-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="petName">Pet Name *</label>
                        <input type="text" id="petName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="petBreed">Breed</label>
                        <input type="text" id="petBreed" name="breed">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="petAge">Age</label>
                        <input type="number" id="petAge" name="age" min="0">
                    </div>
                    <div class="form-group">
                        <label for="petGender">Gender</label>
                        <select id="petGender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="petDescription">Description</label>
                    <textarea id="petDescription" name="description" placeholder="Tell us about your pet..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="petAvatar">Pet Photo</label>
                    <input type="file" id="petAvatar" name="avatar" accept="image/*">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" onclick="hideAddPetModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Pet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let pets = [];
        
        // Load pets data
        async function loadPets() {
            try {
                const response = await fetch('/api/profile.php', { credentials: 'include' });
                const data = await response.json();
                pets = data;
                renderPets();
                updateStats();
            } catch (error) {
                console.error('Error loading pets:', error);
            }
        }
        
        function renderPets() {
            const container = document.getElementById('petsGrid');
            if (pets.length === 0) {
                container.innerHTML = '<p class="no-pets">No pets added yet. <a href="/profile.php?action=add">Add your first pet</a> to get started!</p>';
                return;
            }
            
            container.innerHTML = pets.map(pet => `
                <div class="pet-card">
                    <div class="pet-card-header">
                        <div class="pet-avatar">
                            <img src="${pet.avatar_url || 'images/default-pet.png'}" alt="${pet.name}">
                            <div class="pet-status ${pet.health_score >= 80 ? 'good' : pet.health_score >= 60 ? 'okay' : 'poor'}"></div>
                        </div>
                        <div class="pet-actions">
                            <button class="action-btn" onclick="editPet('${pet.id}')">
                                <i class="icon-edit"></i>
                            </button>
                            <button class="action-btn" onclick="deletePet('${pet.id}')">
                                <i class="icon-delete"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="pet-card-body">
                        <h3>${pet.name}</h3>
                        <div class="pet-details">
                            <span class="pet-breed">${pet.breed || 'Unknown'}</span>
                            <span class="pet-age">${pet.age ? pet.age + ' years old' : 'Age unknown'}</span>
                        </div>
                        
                        <div class="pet-health">
                            <div class="health-bar">
                                <div class="health-fill" style="width: ${pet.health_score || 0}%"></div>
                            </div>
                            <span class="health-text">${pet.health_score || 0}% Health Score</span>
                        </div>
                        
                        <div class="pet-info">
                            <div class="info-item">
                                <span class="info-label">Next Vaccination:</span>
                                <span class="info-value">${formatDate(pet.next_vaccination) || 'Not scheduled'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pet-card-footer">
                        <button class="btn btn-outline" onclick="window.location.href='/health.php?pet=${pet.id}'">
                            <i class="icon-health"></i>
                            Health
                        </button>
                        <button class="btn btn-primary" onclick="viewPetDetails('${pet.id}')">View Details</button>
                    </div>
                </div>
            `).join('');
        }
        
        function updateStats() {
            document.getElementById('totalPets').textContent = pets.length;
            document.getElementById('healthRecords').textContent = pets.reduce((total, pet) => total + (pet.health_records_count || 0), 0);
            
            const now = new Date();
            const dueVaccinations = pets.filter(pet => {
                if (!pet.next_vaccination) return false;
                const vaccDate = new Date(pet.next_vaccination);
                return vaccDate <= now;
            }).length;
            
            document.getElementById('dueVaccinations').textContent = dueVaccinations;
            document.getElementById('activeVaccinations').textContent = pets.filter(pet => pet.next_vaccination).length;
        }
        
        function showAddPetModal() {
            document.getElementById('addPetModal').style.display = 'flex';
        }
        
        function hideAddPetModal() {
            document.getElementById('addPetModal').style.display = 'none';
            document.getElementById('addPetForm').reset();
        }
        
        // Handle add pet form submission
        document.getElementById('addPetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('/api/profile.php?action=add', {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                });
                
                if (response.ok) {
                    hideAddPetModal();
                    loadPets(); // Reload pets list
                } else {
                    const error = await response.json();
                    alert('Error adding pet: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error adding pet:', error);
                alert('Network error. Please try again.');
            }
        });
        
        function editPet(petId) {
            window.location.href = `/profile.php?action=edit&id=${petId}`;
        }
        
        function deletePet(petId) {
            if (confirm('Are you sure you want to delete this pet?')) {
                fetch(`/api/profile.php?action=delete&id=${petId}`, {
                    method: 'DELETE',
                    credentials: 'include'
                }).then(() => {
                    loadPets(); // Reload pets list
                });
            }
        }
        
        function viewPetDetails(petId) {
            window.location.href = `/profile.php?pet=${petId}`;
        }
        
        function formatDate(dateString) {
            if (!dateString) return null;
            return new Date(dateString).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }
        
        // Load pets when page loads
        document.addEventListener('DOMContentLoaded', loadPets);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>