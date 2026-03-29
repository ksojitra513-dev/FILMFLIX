document.addEventListener('DOMContentLoaded', () => {
    // Navigation Items
    const navItems = document.querySelectorAll('.nav-item');
    const dynamicContent = document.getElementById('dynamic-content');
    
    // Initial content setup (Dashboard is default)
    populateDashboard();
    initChart();

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const section = item.dataset.section;
            if (section === 'website') {
                window.open('https://filmflix-website.com', '_blank'); // Redirection example
                return;
            }
            // Update active state
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            // Handle section change
            renderSection(section);
        });
    });

    function renderSection(section) {
        dynamicContent.innerHTML = '';
        dynamicContent.classList.remove('fade-in');
        void dynamicContent.offsetWidth; // Trigger reflow for animation
        dynamicContent.classList.add('fade-in');

        switch(section) {
            case 'dashboard':
                populateDashboard();
                initChart();
                break;
            case 'movies':
                renderMoviesSection();
                break;
            case 'users':
                renderUsersSection();
                break;
            case 'categories':
                renderCategoriesSection();
                break;
            case 'actors':
                renderActorsSection();
                break;
            case 'payments':
                renderPaymentsSection();
                break;
            case 'offers':
                renderOffersSection();
                break;
            case 'comments':
                renderCommentsSection();
                break;
            case 'settings':
                renderSettingsSection();
                break;
            default:
                renderPlaceholder(section);
        }
    }

    function populateDashboard() {
        dynamicContent.innerHTML = `
            <div class="stats-grid">
                ${generateStatCard('Total Views', '42.5K', 'fa-eye', '12', true, '#6366f1')}
                ${generateStatCard('Active Users', '1,280', 'fa-users-cog', '8', true, '#10b981')}
                ${generateStatCard('Revenue', '$14,230', 'fa-shopping-cart', '15', true, '#f59e0b')}
                ${generateStatCard('System Alerts', '24', 'fa-exclamation-triangle', '2', false, '#ef4444')}
            </div>
            <div class="dashboard-grid">
                <div class="content-panel">
                    <div class="panel-header"><h2>Site Health Overview</h2></div>
                    <div style="height: 300px;"><canvas id="siteHealthChart"></canvas></div>
                </div>
                <div class="content-panel">
                    <div class="panel-header"><h2>Recent Activity</h2></div>
                    <div class="activity-list">${getRecentActivityHTML()}</div>
                </div>
            </div>
            <div class="content-panel" style="margin-top: 1.5rem;">
                <div class="panel-header"><h2>Recent Movies/TV Shows</h2></div>
                <table class="data-table">
                    <thead>
                        <tr><th>Poster</th><th>Title</th><th>Category</th><th>Release Date</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>${getRecentMoviesRows()}</tbody>
                </table>
            </div>
        `;
    }

    function generateStatCard(label, value, icon, trend, isUp, iconColor) {
        return `
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="color: ${iconColor};"><i class="fas ${icon}"></i></div>
                    <span class="stat-trend ${isUp ? 'trend-up' : 'trend-down'}">
                        <i class="fas ${isUp ? 'fa-arrow-up' : 'fa-arrow-down'}"></i> ${trend}%
                    </span>
                </div>
                <p class="stat-value">${value}</p>
                <p class="stat-label">${label}</p>
            </div>
        `;
    }

    function getRecentActivityHTML() {
        const activities = [
            { text: 'New subscription by <strong>Alex Morgan</strong>', time: '2m ago', icon: 'fa-user-plus', color: '#6366f1', bg: 'rgba(99,98,241,0.1)' },
            { text: '<strong>Inception</strong> uploaded', time: '45m ago', icon: 'fa-upload', color: '#10b981', bg: 'rgba(16,185,129,0.1)' },
            { text: 'Comment flagged for moderation', time: '1h ago', icon: 'fa-ban', color: '#ef4444', bg: 'rgba(239,68,68,0.1)' }
        ];
        return activities.map(a => `
            <div style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: ${a.bg}; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: ${a.color};"><i class="fas ${a.icon}"></i></div>
                <div><p style="font-size: 0.9rem;">${a.text}</p><p style="font-size: 0.75rem; color: var(--text-muted);">${a.time}</p></div>
            </div>
        `).join('');
    }

    function getRecentMoviesRows() {
        const movies = [
            { title: 'The Dark Knight', cat: 'Action, Thriller', date: '2023-11-15', status: 'Published', img: 'https://image.tmdb.org/t/p/w200/qJ2tW6WMUDp9QEQvTlvvSpsem9u.jpg' },
            { title: 'Interstellar', cat: 'Sci-Fi, Adventure', date: '2023-10-22', status: 'Pending', img: 'https://image.tmdb.org/t/p/w200/gEU2QniE6EwfVDxCzs25vubp2FA.jpg' },
            { title: 'Stranger Things', cat: 'Sci-Fi, Horror', date: '2023-12-01', status: 'Scheduled', img: 'https://image.tmdb.org/t/p/w200/x2LSRm2RAtvYezSTqv0kgY734s7.jpg' }
        ];
        return movies.map(m => `
            <tr>
                <td><img src="${m.img}" style="width: 40px; height: 55px; border-radius: 6px; object-fit: cover;"></td>
                <td style="font-weight: 500;">${m.title}</td>
                <td>${m.cat}</td>
                <td>${m.date}</td>
                <td><span class="status-badge" style="background: ${getStatusColor(m.status)}20; color: ${getStatusColor(m.status)};">${m.status}</span></td>
                <td><i class="fas fa-ellipsis-h" style="cursor: pointer; color: var(--text-muted);"></i></td>
            </tr>
        `).join('');
    }

    function getStatusColor(status) {
        if (status === 'Published') return '#10b981';
        if (status === 'Pending') return '#f59e0b';
        return '#6366f1';
    }

    function initChart() {
        const ctx = document.getElementById('siteHealthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Website Uptime %',
                    data: [99.8, 99.7, 99.9, 99.5, 99.9, 100, 99.9],
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false }, ticks: { color: '#94a3b8' }, title: { display: true, text: 'Health %', color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    }

    function renderMoviesSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header">
                    <h2>Library Management</h2>
                    <button id="add-movie-btn" style="background: var(--primary-color); border: none; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-plus"></i> Add New Content
                    </button>
                </div>
                <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                    <input type="text" placeholder="Filter movies..." style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; flex-grow: 1;">
                    <select style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px;">
                        <option>All Types</option>
                        <option>Movies Only</option>
                        <option>TV Shows Only</option>
                    </select>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>Poster</th><th>Title</th><th>Category</th><th>Cast</th><th>Release Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>${getRecentMoviesRows()}</tbody>
                </table>
            </div>
        `;
    }

    function renderUsersSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Member Lists & Permissions</h2></div>
                <table class="data-table">
                    <thead>
                        <tr><th>User</th><th>Role</th><th>Subscription</th><th>Join Date</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 0.75rem;">
                                <img src="https://i.pravatar.cc/150?u=1" style="width: 32px; height: 32px; border-radius: 50%;">
                                Jordan Smith
                            </td>
                            <td>VIP User</td>
                            <td>Monthly ($14.99)</td>
                            <td>Mar 15, 2023</td>
                            <td><span class="status-badge" style="background: #10b98120; color: #10b981;">Active</span></td>
                            <td><i class="fas fa-edit"></i></td>
                        </tr>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 0.75rem;">
                                <img src="https://i.pravatar.cc/150?u=2" style="width: 32px; height: 32px; border-radius: 50%;">
                                Sarah Connor
                            </td>
                            <td>User</td>
                            <td>Free Tier</td>
                            <td>Jan 02, 2023</td>
                            <td><span class="status-badge" style="background: #ef444420; color: #ef4444;">Suspended</span></td>
                            <td><i class="fas fa-edit"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderCategoriesSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Content Categories</h2><button class="add-btn" style="background: var(--primary-color); border: none; color: #fff; padding: 0.7rem 1.5rem; border-radius: 12px; cursor: pointer;">+ New Category</button></div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    ${['Action', 'Sci-Fi', 'Horror', 'Drama', 'Comedy', 'Thriller', 'Animation', 'Documentary'].map(c => `
                        <div style="background: var(--glass-bg); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); text-align: center;">
                            <h3 style="margin-bottom: 0.5rem;">${c}</h3>
                            <p style="color: var(--text-muted); font-size: 0.8rem;">142 Titles</p>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function renderActorsSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Cast & Crew Database</h2></div>
                <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <input type="text" placeholder="Search actors..." style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; flex-grow: 1;">
                </div>
                <table class="data-table">
                    <thead><tr><th>Cast Bio</th><th>Known For</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr><td>Leonardo DiCaprio</td><td>Inception, Titanic</td><td>Actor</td><td><span class="status-badge" style="background: #10b98120; color: #10b981;">Active</span></td><td><i class="fas fa-edit"></i></td></tr>
                        <tr><td>Christopher Nolan</td><td>Oppenheimer, Dark Knight</td><td>Director</td><td><span class="status-badge" style="background: #10b98120; color: #10b981;">Active</span></td><td><i class="fas fa-edit"></i></td></tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderPaymentsSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Billing & Transactions</h2></div>
                <div class="stats-grid" style="margin-bottom: 2rem;">
                    ${generateStatCard('Total Revenue', '$14,230', 'fa-dollar-sign', '15', true, '#10b981')}
                    ${generateStatCard('Pending Payouts', '$2,140', 'fa-clock', '3', false, '#f59e0b')}
                </div>
                <table class="data-table">
                    <thead><tr><th>Transaction ID</th><th>User</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr><td>#TRX-9482</td><td>Jordan Smith</td><td>$14.99</td><td>Card</td><td>Mar 26, 2024</td><td><span class="status-badge" style="background: #10b98120; color: #10b981;">Completed</span></td></tr>
                        <tr><td>#TRX-9481</td><td>Sarah Connor</td><td>$14.99</td><td>PayPal</td><td>Mar 25, 2024</td><td><span class="status-badge" style="background: #ef444420; color: #ef4444;">Failed</span></td></tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderOffersSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Coupon Codes & Seasonal Promos</h2><button class="add-btn" style="background: var(--primary-color); border: none; color: #fff; padding: 0.7rem 1.5rem; border-radius: 12px; cursor: pointer;">+ Create Coupon</button></div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div style="border: 2px dashed var(--border-color); padding: 1.5rem; border-radius: 15px; background: rgba(99, 102, 241, 0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="color: var(--primary-color);">SUMMER50</h3>
                            <span class="status-badge" style="background: #10b98120; color: #10b981;">Active</span>
                        </div>
                        <p style="margin: 0.5rem 0; font-size: 0.9rem;">50% Off Monthly Sub</p>
                        <p style="color: var(--text-muted); font-size: 0.75rem;">Expires: Aug 30, 2024</p>
                    </div>
                </div>
            </div>
        `;
    }

    function renderCommentsSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Moderation Queue</h2></div>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="background: var(--glass-bg); padding: 1.25rem; border-radius: 15px; border-left: 4px solid var(--danger);">
                        <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">"This movie was terrible, don't watch it!"</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">By @Hater123 on <strong>Batman</strong> • 5m ago</span>
                            <div style="display: flex; gap: 0.5rem;">
                                <button style="background: #10b98120; color: #10b981; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">Approve</button>
                                <button style="background: #ef444420; color: #ef4444; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderSettingsSection() {
        dynamicContent.innerHTML = `
            <div class="content-panel">
                <div class="panel-header"><h2>Site Config & API Keys</h2></div>
                <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 600px;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">TMDB API Key</label>
                        <input type="password" value="•••••••••••••••••••••" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem; border-radius: 10px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Contact Email</label>
                        <input type="text" value="admin@filmflix.com" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem; border-radius: 10px;">
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button style="background: var(--primary-color); border: none; color: white; padding: 0.8rem 2rem; border-radius: 10px; cursor: pointer; font-weight: 600;">Save Changes</button>
                        <button style="background: transparent; border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 2rem; border-radius: 10px; cursor: pointer;">Reset to Default</button>
                    </div>
                </div>
            </div>
        `;
    }

    function renderPlaceholder(section) {
        dynamicContent.innerHTML = `
            <div class="content-panel" style="text-align: center; padding: 5rem 2rem;">
                <div style="width: 80px; height: 80px; background: var(--glass-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; color: var(--primary-color);">
                    <i class="fas fa-tools"></i>
                </div>
                <h2>${section.charAt(0).toUpperCase() + section.slice(1)} Module</h2>
                <p style="color: var(--text-muted); margin-top: 1rem;">This component is currently under development.</p>
            </div>
        `;
    }
});
