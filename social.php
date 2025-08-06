<?php include 'partials/header.php'; ?>

<?php if (!$isAuthenticated): ?>
    <script>window.location.href = 'login.php';</script>
<?php else: ?>
    <div class="social-page">
        <div class="page-header">
            <div class="page-title">
                <h1>Social Feed</h1>
                <p>Share moments and connect with the pet community</p>
            </div>
            <button class="btn btn-primary" onclick="createPost()">
                <i class="icon-plus"></i>
                Create Post
            </button>
        </div>
        
        <div class="social-content">
            <div class="sidebar-column">
                <div class="section-card">
                    <div class="section-header">
                        <h2>Filter Posts</h2>
                    </div>
                    
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">
                            <i class="icon-all"></i>
                            All Posts
                        </button>
                        <button class="filter-btn" data-filter="friends">
                            <i class="icon-friends"></i>
                            Friends Only
                        </button>
                        <button class="filter-btn" data-filter="public">
                            <i class="icon-public"></i>
                            Public
                        </button>
                        <button class="filter-btn" data-filter="liked">
                            <i class="icon-heart"></i>
                            Liked Posts
                        </button>
                    </div>
                </div>
                
                <div class="section-card">
                    <div class="section-header">
                        <h2>Your Activity</h2>
                    </div>
                    
                    <div class="activity-stats">
                        <div class="stat-row">
                            <span class="stat-label">Posts:</span>
                            <span class="stat-value" id="userPosts">12</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Likes Received:</span>
                            <span class="stat-value" id="likesReceived">256</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Comments:</span>
                            <span class="stat-value" id="commentsCount">48</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Followers:</span>
                            <span class="stat-value" id="followersCount">89</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="main-column">
                <div class="posts-container" id="postsContainer">
                    <!-- Posts will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentFilter = 'all';
        let posts = [];
        
        // Load posts
        async function loadPosts() {
            try {
                const response = await fetch(`/api/posts.php?filter=${currentFilter}`, { credentials: 'include' });
                posts = await response.json();
                renderPosts();
                loadUserStats();
            } catch (error) {
                console.error('Error loading posts:', error);
                showEmptyState();
            }
        }
        
        function renderPosts() {
            const container = document.getElementById('postsContainer');
            
            if (posts.length === 0) {
                showEmptyState();
                return;
            }
            
            container.innerHTML = posts.map(post => `
                <div class="post-card">
                    <div class="post-header">
                        <div class="post-author">
                            <img src="${post.author_avatar || 'images/default-avatar.png'}" alt="${post.author_name}" class="author-avatar">
                            <div class="author-info">
                                <h4>${post.author_name}</h4>
                                <span class="post-time">${formatTimeAgo(post.created_at)}</span>
                            </div>
                        </div>
                        <div class="post-menu">
                            <button class="menu-btn" onclick="showPostMenu('${post.id}')">
                                <i class="icon-menu"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <p>${post.content}</p>
                        ${post.image_url ? `<img src="${post.image_url}" alt="Post image" class="post-image">` : ''}
                    </div>
                    
                    <div class="post-actions">
                        <button class="action-btn ${post.liked ? 'liked' : ''}" onclick="toggleLike('${post.id}')">
                            <i class="icon-heart"></i>
                            <span>${post.likes_count || 0}</span>
                        </button>
                        <button class="action-btn" onclick="showComments('${post.id}')">
                            <i class="icon-comment"></i>
                            <span>${post.comments_count || 0}</span>
                        </button>
                        <button class="action-btn" onclick="sharePost('${post.id}')">
                            <i class="icon-share"></i>
                        </button>
                    </div>
                    
                    <div class="post-comments" id="comments-${post.id}" style="display: none;">
                        <!-- Comments will be loaded here -->
                    </div>
                </div>
            `).join('');
        }
        
        function showEmptyState() {
            const container = document.getElementById('postsContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="icon-posts"></i>
                    </div>
                    <h3>No Posts Found</h3>
                    <p>Be the first to share something with the community!</p>
                    <button class="btn btn-primary" onclick="createPost()">Create Your First Post</button>
                </div>
            `;
        }
        
        async function loadUserStats() {
            try {
                const response = await fetch('/api/posts.php?stats=true', { credentials: 'include' });
                const stats = await response.json();
                
                document.getElementById('userPosts').textContent = stats.posts_count || 0;
                document.getElementById('likesReceived').textContent = stats.likes_received || 0;
                document.getElementById('commentsCount').textContent = stats.comments_count || 0;
                document.getElementById('followersCount').textContent = stats.followers_count || 0;
            } catch (error) {
                console.error('Error loading user stats:', error);
            }
        }
        
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                currentFilter = e.target.dataset.filter;
                loadPosts();
            });
        });
        
        async function toggleLike(postId) {
            try {
                const response = await fetch('/api/posts.php', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        action: 'toggle_like',
                        post_id: postId
                    })
                });
                
                if (response.ok) {
                    loadPosts(); // Reload to update like counts
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
        
        function showComments(postId) {
            const commentsDiv = document.getElementById(`comments-${postId}`);
            const isVisible = commentsDiv.style.display !== 'none';
            
            if (isVisible) {
                commentsDiv.style.display = 'none';
            } else {
                loadComments(postId);
                commentsDiv.style.display = 'block';
            }
        }
        
        async function loadComments(postId) {
            try {
                const response = await fetch(`/api/posts.php?comments=${postId}`, { credentials: 'include' });
                const comments = await response.json();
                
                const commentsDiv = document.getElementById(`comments-${postId}`);
                commentsDiv.innerHTML = `
                    <div class="comments-list">
                        ${comments.map(comment => `
                            <div class="comment-item">
                                <img src="${comment.author_avatar || 'images/default-avatar.png'}" alt="${comment.author_name}" class="comment-avatar">
                                <div class="comment-content">
                                    <strong>${comment.author_name}</strong>
                                    <p>${comment.content}</p>
                                    <span class="comment-time">${formatTimeAgo(comment.created_at)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="comment-form">
                        <input type="text" placeholder="Write a comment..." class="comment-input" 
                               onkeypress="handleCommentSubmit(event, '${postId}')">
                        <button class="btn btn-sm" onclick="submitComment('${postId}')">Post</button>
                    </div>
                `;
            } catch (error) {
                console.error('Error loading comments:', error);
            }
        }
        
        function handleCommentSubmit(event, postId) {
            if (event.key === 'Enter') {
                submitComment(postId);
            }
        }
        
        async function submitComment(postId) {
            const input = document.querySelector(`#comments-${postId} .comment-input`);
            const content = input.value.trim();
            
            if (!content) return;
            
            try {
                const response = await fetch('/api/posts.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        action: 'add_comment',
                        post_id: postId,
                        content: content
                    })
                });
                
                if (response.ok) {
                    input.value = '';
                    loadComments(postId); // Reload comments
                    loadPosts(); // Update comment counts
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
            }
        }
        
        function createPost() {
            const content = prompt('What would you like to share?');
            if (content) {
                fetch('/api/posts.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        content: content,
                        visibility: 'public'
                    })
                }).then(() => {
                    loadPosts(); // Reload posts
                });
            }
        }
        
        function showPostMenu(postId) {
            if (confirm('Delete this post?')) {
                fetch(`/api/posts.php?action=delete&id=${postId}`, {
                    method: 'DELETE',
                    credentials: 'include'
                }).then(() => {
                    loadPosts();
                });
            }
        }
        
        function sharePost(postId) {
            if (navigator.share) {
                navigator.share({
                    title: 'PawPilot HQ Post',
                    url: `${window.location.origin}/social.php?post=${postId}`
                });
            } else {
                navigator.clipboard.writeText(`${window.location.origin}/social.php?post=${postId}`);
                alert('Link copied to clipboard!');
            }
        }
        
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffHours < 1) return 'Just now';
            if (diffHours < 24) return `${diffHours} hours ago`;
            if (diffDays === 1) return '1 day ago';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
            return date.toLocaleDateString();
        }
        
        // Load posts when page loads
        document.addEventListener('DOMContentLoaded', loadPosts);
    </script>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>