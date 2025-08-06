<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="groups-page">
        <div class="page-header">
            <div class="page-title">
                <h1>Groups</h1>
                <p>Connect with pet communities and breed-specific groups</p>
            </div>
        </div>
        
        <div class="groups-content">
            <div class="main-column">
                <div class="section-card">
                    <div class="section-header">
                        <h2>My Groups</h2>
                        <p>Groups you've joined</p>
                    </div>
                    
                    <div class="groups-grid" id="myGroups">
                        <!-- User's groups will be loaded here -->
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Discover Groups</h2>
                        <p>Find new communities to join</p>
                    </div>
                    
                    <div class="groups-grid" id="discoverGroups">
                        <!-- Suggested groups will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load groups data
        async function loadGroups() {
            try {
                const [myGroupsResponse, discoverResponse] = await Promise.all([
                    fetch('/api/groups.php?my=true', { credentials: 'include' }),
                    fetch('/api/groups.php?discover=true', { credentials: 'include' })
                ]);
                
                const myGroups = await myGroupsResponse.json();
                const discoverGroups = await discoverResponse.json();
                
                renderMyGroups(myGroups);
                renderDiscoverGroups(discoverGroups);
            } catch (error) {
                console.error('Error loading groups:', error);
            }
        }
        
        function renderMyGroups(groups) {
            const container = document.getElementById('myGroups');
            if (groups.length === 0) {
                container.innerHTML = '<p class="no-data">You haven\'t joined any groups yet.</p>';
                return;
            }
            
            container.innerHTML = groups.map(group => `
                <div class="group-card">
                    <div class="group-header">
                        <img src="${group.avatar_url || 'images/default-group.png'}" alt="${group.name}" class="group-avatar">
                    </div>
                    <div class="group-content">
                        <h3>${group.name}</h3>
                        <p>${group.description}</p>
                        <div class="group-stats">
                            <span class="stat-item">${group.members_count} members</span>
                            <span class="stat-item">${group.posts_count} posts</span>
                        </div>
                    </div>
                    <div class="group-actions">
                        <button class="btn btn-primary" onclick="viewGroup('${group.id}')">View Group</button>
                    </div>
                </div>
            `).join('');
        }
        
        function renderDiscoverGroups(groups) {
            const container = document.getElementById('discoverGroups');
            if (groups.length === 0) {
                container.innerHTML = '<p class="no-data">No groups to discover right now.</p>';
                return;
            }
            
            container.innerHTML = groups.map(group => `
                <div class="group-card">
                    <div class="group-header">
                        <img src="${group.avatar_url || 'images/default-group.png'}" alt="${group.name}" class="group-avatar">
                    </div>
                    <div class="group-content">
                        <h3>${group.name}</h3>
                        <p>${group.description}</p>
                        <div class="group-stats">
                            <span class="stat-item">${group.members_count} members</span>
                            <span class="stat-item">${group.posts_count} posts</span>
                        </div>
                    </div>
                    <div class="group-actions">
                        <button class="btn btn-outline" onclick="joinGroup('${group.id}')">Join Group</button>
                    </div>
                </div>
            `).join('');
        }
        
        async function joinGroup(groupId) {
            try {
                const response = await fetch('/api/groups.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        action: 'join',
                        group_id: groupId
                    })
                });
                
                if (response.ok) {
                    loadGroups(); // Reload groups
                }
            } catch (error) {
                console.error('Error joining group:', error);
            }
        }
        
        function viewGroup(groupId) {
            window.location.href = `group.php?id=${groupId}`;
        }
        
        // Load groups when page loads
        document.addEventListener('DOMContentLoaded', loadGroups);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>