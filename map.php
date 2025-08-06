<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="map-page">
        <div class="page-header">
            <div class="page-title">
                <h1>Lost Pet Map</h1>
                <p>Help find lost pets in your area</p>
            </div>
        </div>
        
        <div class="map-content">
            <div class="map-container">
                <div id="petMap" class="pet-map">
                    <!-- Map will be loaded here -->
                    <div class="map-placeholder">
                        <p>Interactive map showing lost pets in your area</p>
                    </div>
                </div>
            </div>
            
            <div class="map-sidebar">
                <div class="section-card">
                    <div class="section-header">
                        <h2>Recent Lost Pets</h2>
                    </div>
                    
                    <div class="lost-pets-list" id="lostPetsList">
                        <!-- Lost pets will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadLostPets() {
            try {
                const response = await fetch('/api/map.php', { credentials: 'include' });
                const lostPets = await response.json();
                renderLostPets(lostPets);
            } catch (error) {
                console.error('Error loading lost pets:', error);
            }
        }
        
        function renderLostPets(pets) {
            const container = document.getElementById('lostPetsList');
            if (pets.length === 0) {
                container.innerHTML = '<p class="no-data">No lost pets reported in your area</p>';
                return;
            }
            
            container.innerHTML = pets.map(pet => `
                <div class="lost-pet-item">
                    <img src="${pet.photo_url || 'images/default-pet.png'}" alt="${pet.name}" class="lost-pet-photo">
                    <div class="lost-pet-info">
                        <h4>${pet.name}</h4>
                        <p>${pet.breed} • ${pet.color}</p>
                        <span class="lost-date">Lost: ${formatDate(pet.lost_date)}</span>
                        <span class="lost-location">${pet.location}</span>
                    </div>
                    <button class="btn btn-sm" onclick="viewLostPet('${pet.id}')">View Details</button>
                </div>
            `).join('');
        }
        
        function viewLostPet(petId) {
            console.log('View lost pet details:', petId);
        }
        
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }
        
        // Load lost pets when page loads
        document.addEventListener('DOMContentLoaded', loadLostPets);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>