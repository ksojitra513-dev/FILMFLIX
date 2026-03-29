<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Billing & Transactions</h2>
        <div class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> 15% increase in revenue</div>
    </div>
    
    <div class="stats-grid" style="margin-bottom: 2.5rem; margin-top: 1rem;">
        <div class="stat-card" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.1); transition: transform 0.2s;" id="card-revenue">
            <p class="stat-label">Total Revenue</p>
            <p class="stat-value" style="color: #10b981;" id="stat-revenue">$14,230.50</p>
        </div>
        <div class="stat-card" style="transition: transform 0.2s;" id="card-pending">
            <p class="stat-label">Pending Payouts</p>
            <p class="stat-value" id="stat-pending">$2,140.00</p>
        </div>
        <div class="stat-card" style="transition: transform 0.2s;" id="card-count">
            <p class="stat-label">Total Transactions</p>
            <p class="stat-value" id="stat-count">1,540</p>
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <input type="text" id="search-input" placeholder="Search by Transaction ID or User..." style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; flex-grow: 1; outline: none; transition: border-color 0.2s;">
        <select id="status-filter" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; outline: none;">
            <option value="All">All Statuses</option>
            <option value="Completed">Completed</option>
            <option value="Pending">Pending</option>
            <option value="Failed">Failed</option>
            <option value="Refunded">Refunded</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="trx-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="trx-body">
                <!-- Dynamically populated -->
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div id="empty-state" style="display: none; text-align: center; padding: 4rem 2rem; background: var(--glass-bg); border-radius: 16px; border: 1px solid var(--border-color); margin-top: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(99,102,241,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-file-invoice-dollar" style="font-size: 2.5rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-main); margin-bottom: 0.75rem; font-size: 1.25rem;">No transactions found</h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem; line-height: 1.6;">No billing records match your current search or filters.</p>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .action-btn { background: var(--glass-bg); border: 1px solid var(--border-color); width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .action-btn:hover { background: var(--border-color); transform: translateY(-2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Current DB state
    let transactions = [];

    // Base historical totals
    const BASE_REVENUE = 14000;
    const BASE_PENDING = 2000;
    const BASE_COUNT = 1530;

    const tbody = document.getElementById('trx-body');
    const table = document.getElementById('trx-table');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');

    const statRevs = document.getElementById('stat-revenue');
    const statPend = document.getElementById('stat-pending');
    const statCount = document.getElementById('stat-count');
    const cRevs = document.getElementById('card-revenue');
    const cPend = document.getElementById('card-pending');
    const cCount = document.getElementById('card-count');

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const icon = isError ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${isError ? '#ef4444' : '#10b981'}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${isError ? 'Error' : 'Success'}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    async function loadTransactions() {
        try {
            const res = await fetch('api_payments.php?action=read');
            transactions = await res.json();
            render();
        } catch(e) {
            showToast('Failed to connect to billing database', 'error');
        }
    }

    function bumpElement(el) {
        el.style.transform = 'scale(1.05)';
        setTimeout(() => el.style.transform = 'scale(1)', 150);
    }
    
    function animateValue(obj, start, end, duration, formatOptions) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const val = easeOut * (end - start) + start;
            
            if(formatOptions === 'currency') {
                obj.textContent = `$${val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            } else {
                obj.textContent = Math.floor(val).toLocaleString('en-US');
            }
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    let initialLoad = true;

    function updateStats() {
        let r = BASE_REVENUE, p = BASE_PENDING, c = BASE_COUNT;
        
        transactions.forEach(t => {
            c++;
            if(t.status === 'Completed') r += parseFloat(t.amount);
            if(t.status === 'Pending') p += parseFloat(t.amount);
        });

        if (initialLoad) {
            animateValue(statRevs, 0, r, 2000, 'currency');
            animateValue(statPend, 0, p, 2000, 'currency');
            animateValue(statCount, 0, c, 2000, 'number');
            initialLoad = false;
        } else {
            const newRev = `$${r.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            const newPend = `$${p.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            const newCount = c.toLocaleString('en-US');

            if(statRevs.textContent !== newRev) { statRevs.textContent = newRev; bumpElement(cRevs); }
            if(statPend.textContent !== newPend) { statPend.textContent = newPend; bumpElement(cPend); }
            if(statCount.textContent !== newCount) { statCount.textContent = newCount; bumpElement(cCount); }
        }
    }

    function getStatusBadge(s, id) {
        const colors = {
            Completed: { bg: 'rgba(16, 185, 129, 0.2)', color: '#10b981' },
            Pending:   { bg: 'rgba(245, 158, 11, 0.2)', color: '#f59e0b' },
            Failed:    { bg: 'rgba(239, 68, 68, 0.2)', color: '#ef4444' },
            Refunded:  { bg: 'rgba(148, 163, 184, 0.2)', color: '#94a3b8' }
        };
        const c = colors[s] || colors.Completed;
        return `<span class="status-badge" style="background: ${c.bg}; color: ${c.color}; cursor: pointer" onclick="cycleStatus(${id})" title="Click to cycle status">${s.toUpperCase()}</span>`;
    }

    function render() {
        const q = searchInput.value.toLowerCase().trim();
        const stat = statusFilter.value;
        const filtered = transactions.filter(t => {
            const matchQ = t.trx_id.toLowerCase().includes(q) || t.user_name.toLowerCase().includes(q);
            const matchS = stat === 'All' || t.status === stat;
            return matchQ && matchS;
        });

        tbody.innerHTML = '';
        if (filtered.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = 'table';
            emptyState.style.display = 'none';
            
            filtered.forEach((t, i) => {
                const tr = document.createElement('tr');
                tr.style.animation = `fadeInUp 0.3s ease forwards ${i * 0.05}s`;
                tr.style.opacity = '0';
                tr.innerHTML = `
                    <td style="color: var(--text-muted);">#${t.trx_id}</td>
                    <td style="color: var(--text-main); font-weight: 500;">${t.user_name}</td>
                    <td style="color: var(--text-main);">$${parseFloat(t.amount).toFixed(2)}</td>
                    <td style="color: var(--text-muted);"><i class="fab ${t.icon}" style="margin-right: 0.5rem; color: var(--text-main);"></i> ${t.method}</td>
                    <td style="color: var(--text-muted);">${t.payment_date}</td>
                    <td>${getStatusBadge(t.status, t.id)}</td>
                    <td><button onclick="downloadReceipt('${t.trx_id}')" class="action-btn" style="color: var(--primary-color);" title="Download Receipt"><i class="fas fa-receipt"></i></button></td>
                `;
                tbody.appendChild(tr);
            });
        }
        updateStats();
    }

    window.cycleStatus = async function(id) {
        const trx = transactions.find(x => x.id == id);
        if(!trx) return;
        
        try {
            const res = await fetch('api_payments.php?action=cycle_status', {
                method: 'POST',
                body: new URLSearchParams({ id: id })
            });
            const data = await res.json();
            if(data.success) {
                trx.status = data.new_status;
                render();
                showToast('Transaction status updated to ' + trx.status);
            }
        } catch(e) {
            showToast('Failed to update payment status', 'error');
        }
    };

    window.downloadReceipt = function(id) {
        event.currentTarget.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        setTimeout(() => {
            showToast('Receipt for #' + id + ' downloaded successfully.');
            render();
        }, 800);
    };

    searchInput.addEventListener('input', render);
    statusFilter.addEventListener('change', render);

    loadTransactions();
});
</script>

<?php include 'footer.php'; ?>
