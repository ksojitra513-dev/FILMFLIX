<?php 
include 'header.php'; 

// Fetch real stats
$totalMovies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status='Completed'")->fetchColumn() ?? 0;
$pendingComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status='Pending'")->fetchColumn();

// Format revenue
$formattedRevenue = '$' . number_format($totalRevenue, 2);
?>
<script>
    // Inject DB stats into JS
    const dbStats = {
        movies: '<?php echo number_format($totalMovies); ?>',
        users: '<?php echo number_format($totalUsers); ?>',
        revenue: '<?php echo $formattedRevenue; ?>',
        alerts: '<?php echo $pendingComments; ?>'
    };
</script>

<!-- Stat Cards -->
<div id="stats-grid-container" class="stats-grid" style="animation: fadeIn 0.4s ease forwards;">
    <!-- Dynamically Populated Stats -->
</div>

<!-- Site health Overview & Recent Activity -->
<div class="dashboard-grid">
    <div class="content-panel">
        <div class="panel-header">
            <h2 style="color: var(--text-main);"><i class="fas fa-heartbeat" style="color: var(--primary-color); margin-right: 0.5rem;"></i>Site Health Overview</h2>
            <button onclick="exportDashboardData()" class="action-btn" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; width: auto; height: auto; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='var(--glass-bg)'">
                <i class="fas fa-download"></i> Export Data
            </button>
        </div>
        <div class="chart-container" style="height: 300px; position: relative; margin-top: 1rem;">
            <canvas id="siteHealthChart"></canvas>
        </div>
    </div>
    
    <div class="content-panel">
        <div class="panel-header">
            <h2 style="color: var(--text-main);"><i class="fas fa-list-ul" style="color: var(--accent); margin-right: 0.5rem;"></i>Recent Activity</h2>
            <button onclick="refreshActivity()" class="action-btn" style="background: transparent; border: 1px solid transparent; color: var(--text-muted); cursor: pointer;" title="Refresh Activity">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
        </div>
        <div id="activity-feed" class="activity-list" style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: 1rem;">
            <!-- Dynamically Populated Feed -->
        </div>
    </div>
</div>

<style>
    @keyframes spinSync { 100% { transform: rotate(360deg); } }
    .syncing { animation: spinSync 1s linear infinite; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Dynamic State
    const statsData = [
        { title: 'Total Movies', value: dbStats.movies, trend: 'up', perc: '12%', icon: 'fa-film', color: 'var(--primary-color)' },
        { title: 'Active Users', value: dbStats.users, trend: 'up', perc: '8%', icon: 'fa-users-cog', color: 'var(--accent)' },
        { title: 'Total Revenue', value: dbStats.revenue, trend: 'up', perc: '15%', icon: 'fa-shopping-cart', color: '#f59e0b' },
        { title: 'Pending Comments', value: dbStats.alerts, trend: 'down', perc: '2%', icon: 'fa-exclamation-triangle', color: 'var(--danger)' }
    ];

    let activities = [
        { title: 'New subscription by <strong>Alex Morgan</strong>', time: '2 minutes ago', icon: 'fa-user-plus', color: 'var(--primary-color)', bg: 'rgba(99, 102, 241, 0.1)' },
        { title: '<strong>Inception</strong> movie uploaded', time: '45 minutes ago', icon: 'fa-upload', color: 'var(--accent)', bg: 'rgba(16, 185, 129, 0.1)' },
        { title: 'Comment flagged for moderation', time: '1 hour ago', icon: 'fa-ban', color: 'var(--danger)', bg: 'rgba(239, 68, 68, 0.1)' }
    ];

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const color = isError ? '#ef4444' : (type === 'info' ? '#3b82f6' : '#10b981');
        const icon = isError ? 'fa-exclamation-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-check-circle');
        toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // Render Stats
    const statsContainer = document.getElementById('stats-grid-container');
    statsData.forEach(s => {
        const trendClass = s.trend === 'up' ? 'trend-up' : 'trend-down';
        const arrowClass = s.trend === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
        statsContainer.innerHTML += `
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="color: ${s.color};">
                        <i class="fas ${s.icon}"></i>
                    </div>
                    <span class="stat-trend ${trendClass}"><i class="fas ${arrowClass}"></i> ${s.perc}</span>
                </div>
                <p class="stat-value">${s.value}</p>
                <p class="stat-label">${s.title}</p>
            </div>
        `;
    });

    // Render Activity
    function renderActivity() {
        const feed = document.getElementById('activity-feed');
        feed.innerHTML = '';
        activities.forEach((act, i) => {
            const el = document.createElement('div');
            el.className = 'activity-item';
            el.style.display = 'flex';
            el.style.gap = '1rem';
            el.style.animation = `fadeInUp 0.3s ease forwards ${i * 0.1}s`;
            el.style.opacity = '0';
            el.innerHTML = `
                <div style="width: 40px; height: 40px; border-radius: 10px; background: ${act.bg}; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: ${act.color};">
                    <i class="fas ${act.icon}"></i>
                </div>
                <div>
                    <p style="font-size: 0.9rem;">${act.title}</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted);">${act.time}</p>
                </div>
            `;
            feed.appendChild(el);
        });
    }
    renderActivity();

    // Chart.js Setup
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
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    // Global Handlers
    window.refreshActivity = function() {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('syncing');
        setTimeout(() => {
            activities.unshift({ title: 'System cache cleared successfully', time: 'Just now', icon: 'fa-bolt', color: '#f59e0b', bg: 'rgba(245, 158, 11, 0.1)' });
            activities.pop(); // keep it to 3 items
            renderActivity();
            icon.classList.remove('syncing');
            showToast('Activity feed updated', 'success');
        }, 800);
    };

    window.exportDashboardData = function() {
        showToast('Compiling analytical data...', 'info');
        setTimeout(() => {
            showToast('Dashboard report downloaded (CSV)', 'success');
        }, 1500);
    };
});
</script>

<?php include 'footer.php'; ?>
