<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Universal Watchlist Management</h2>
        <button id="add-watchlist-btn" style="background: var(--primary-color); border: 2px solid transparent; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 15px rgba(99,102,241,0.2);" onmouseover="this.style.boxShadow='0 0 0 4px rgba(99,102,241,0.3)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 4px 15px rgba(99,102,241,0.2)'; this.style.transform='translateY(0)'" onmousedown="this.style.transform='scale(0.96)'" onmouseup="this.style.transform='translateY(-2px)'">
            <i class="fas fa-plus-circle"></i> Create New entry
        </button>
    </div>
    
    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
        <input type="text" id="search-input" placeholder="Search by user or movie..." style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; flex-grow: 1; outline: none;">
        <select id="priority-filter" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; outline: none;">
            <option value="all">All Priorities</option>
            <option value="High">High Priority</option>
            <option value="Medium">Medium Priority</option>
            <option value="Low">Low Priority</option>
        </select>
        <select id="sort-filter" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; outline: none;">
            <option value="newest">Recently Added</option>
            <option value="oldest">Oldest First</option>
            <option value="title">Title (A-Z)</option>
        </select>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="watchlist-table">
            <thead>
                <tr>
                    <th>User Account</th>
                    <th>Content Title</th>
                    <th>Platform Type</th>
                    <th>Added Date</th>
                    <th>Priority Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="watchlist-body">
                <!-- Dynamically populated -->
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div id="empty-state" style="display: none; text-align: center; padding: 4rem 2rem; background: var(--glass-bg); border-radius: 16px; border: 1px solid var(--border-color); margin-top: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(99,102,241,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-bookmark" style="font-size: 2.5rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-main); margin-bottom: 0.75rem; font-size: 1.25rem;">No watchlist entries found</h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem; line-height: 1.6;">We couldn't find any entries matching your current filters. Adjust your search or add a new entry.</p>
            <button onclick="document.getElementById('add-watchlist-btn').click()" style="background: var(--primary-color); border: none; color: #fff; padding: 0.8rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s;">
                <i class="fas fa-plus"></i> Add Entry
            </button>
        </div>
    </div>
</div>

<!-- ========== ADD/EDIT MODAL ========== -->
<div id="watchlist-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; animation: modalIn 0.3s ease;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); position: sticky; top: 0; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 24px 24px 0 0; z-index: 2;">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-bookmark" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Watchlist Entry</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border-color)'"><i class="fas fa-times"></i></button>
        </div>

        <form id="watchlist-form" style="padding: 1.5rem 2rem;" novalidate>
            <input type="hidden" id="form-id">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <!-- User Name -->
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>User Name <span style="color: var(--danger);">*</span></span>
                        <span id="user-counter" style="font-size: 0.72rem;">0 / 50</span>
                    </label>
                    <input type="text" id="form-user" maxlength="50" placeholder="e.g. John Doe" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                    <p class="field-err" id="err-user" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>
                
                <!-- Title -->
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>Content Title <span style="color: var(--danger);">*</span></span>
                        <span id="title-counter" style="font-size: 0.72rem;">0 / 100</span>
                    </label>
                    <input type="text" id="form-title" maxlength="100" placeholder="e.g. The Matrix" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                    <p class="field-err" id="err-title" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <!-- Type -->
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Platform Type <span style="color: var(--danger);">*</span></label>
                        <select id="form-type" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                            <option value="">— Select Type —</option>
                            <option value="Movie">Movie</option>
                            <option value="TV Show">TV Show</option>
                        </select>
                        <p class="field-err" id="err-type" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                    </div>
                    <!-- Priority -->
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Priority</label>
                        <select id="form-priority" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                            <option value="High">🔴 High</option>
                            <option value="Medium">🟡 Medium</option>
                            <option value="Low">🟢 Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Validation Summary -->
            <div id="validation-summary" style="display: none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 0.85rem 1.1rem; margin-top: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 0.85rem;"></i>
                    <span style="color: #ef4444; font-weight: 600; font-size: 0.85rem;">Please fix the following errors:</span>
                </div>
                <ul id="error-list" style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--text-muted);"></ul>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s;">Cancel</button>
                <button type="submit" id="modal-save" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: all 0.2s;">
                    Save Entry
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .shake { animation: shake 0.3s ease-in-out; }
    input:focus, select:focus { border-color: var(--primary-color) !important; }
    .action-btn { background: var(--glass-bg); border: 1px solid var(--border-color); width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; margin-right: 0.4rem; }
    .action-btn:hover { background: var(--border-color); transform: translateY(-2px); }
    .field-err { animation: modalIn 0.2s ease; }
    .toast { position: fixed; bottom: 2rem; right: 2rem; background: #1e293b; border: 1px solid var(--border-color); padding: 1rem 1.75rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; transform: translateY(100px); opacity: 0; transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 9999; }
    .toast.show { transform: translateY(0); opacity: 1; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Current DB state
    let watchlist = [];

    async function loadWatchlist() {
        try {
            const res = await fetch('api_watchlist.php?action=read');
            watchlist = await res.json();
            render();
        } catch(e) {
            showToast('Failed to load watchlist', 'error');
        }
    }

    // Elements
    const tbody = document.getElementById('watchlist-body');
    const table = document.getElementById('watchlist-table');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search-input');
    const priorityFilter = document.getElementById('priority-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    const modal = document.getElementById('watchlist-modal');
    const form = document.getElementById('watchlist-form');

    // Toast functionality
    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const color = isError ? '#ef4444' : '#10b981';
        const icon = isError ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${isError ? 'Error' : 'Success'}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Priority Badge UI
    function getPriorityBadge(p, id) {
        const colors = {
            High: { bg: 'rgba(239, 68, 68, 0.2)', color: '#ef4444' },
            Medium: { bg: 'rgba(245, 158, 11, 0.2)', color: '#f59e0b' },
            Low: { bg: 'rgba(16, 185, 129, 0.2)', color: '#10b981' }
        };
        const c = colors[p] || colors.Medium;
        return `<span class="status-badge" style="background: ${c.bg}; color: ${c.color}; cursor: pointer" onclick="cyclePriority(${id})" title="Click to change Priority">${p}</span>`;
    }

    // Render Table
    function render() {
        const q = searchInput.value.toLowerCase().trim();
        const pFilter = priorityFilter.value;
        const sFilter = sortFilter.value;

        let filtered = watchlist.filter(w => {
            const matchSearch = w.user_name.toLowerCase().includes(q) || w.title.toLowerCase().includes(q);
            const matchPriority = pFilter === 'all' || w.priority === pFilter;
            return matchSearch && matchPriority;
        });

        // Sorting
        filtered.sort((a, b) => {
            if (sFilter === 'newest') return b.timestamp - a.timestamp;
            if (sFilter === 'oldest') return a.timestamp - b.timestamp;
            if (sFilter === 'title') return a.title.localeCompare(b.title);
            return 0;
        });

        tbody.innerHTML = '';
        if (filtered.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = 'table';
            emptyState.style.display = 'none';
            
            filtered.forEach((w, i) => {
                const tr = document.createElement('tr');
                tr.style.animation = `fadeInUp 0.3s ease forwards ${i * 0.05}s`;
                tr.style.opacity = '0';
                tr.innerHTML = `
                    <td style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="${w.avatar || 'https://i.pravatar.cc/150?u=' + w.id}" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div style="color: var(--text-main); font-weight: 500;">${w.user_name}</div>
                    </td>
                    <td style="font-weight: 500; color: var(--text-main);">${w.title}</td>
                    <td style="color: var(--text-muted);">${w.type}</td>
                    <td style="color: var(--text-muted);">${w.added_date}</td>
                    <td>${getPriorityBadge(w.priority, w.id)}</td>
                    <td style="white-space: nowrap;">
                        <button onclick="editEntry(${w.id})" class="action-btn" style="color: var(--primary-color);" title="Edit"><i class="fas fa-pen"></i></button>
                        <button onclick="deleteEntry(${w.id})" class="action-btn" style="color: var(--danger);" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    }

    // Interactive Functions
    window.cyclePriority = async function(id) {
        const entry = watchlist.find(w => w.id == id);
        if(!entry) return;
        
        try {
            const res = await fetch('api_watchlist.php?action=cycle_priority', {
                method: 'POST',
                body: new URLSearchParams({ id: id })
            });
            const data = await res.json();
            if(data.success) {
                entry.priority = data.new_priority;
                render();
                showToast('Priority updated to ' + entry.priority);
            }
        } catch(e) {
            showToast('Failed to update priority', 'error');
        }
    };

    window.editEntry = function(id) {
        const entry = watchlist.find(w => w.id == id);
        if(!entry) return;
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-pen" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Edit Entry';
        document.getElementById('form-id').value = entry.id;
        document.getElementById('form-user').value = entry.user_name;
        document.getElementById('form-title').value = entry.title;
        document.getElementById('form-type').value = entry.type;
        document.getElementById('form-priority').value = entry.priority;
        modal.style.display = 'flex';
    };

    window.deleteEntry = async function(id) {
        if(confirm('Are you sure you want to remove this from the watchlist?')) {
            try {
                const res = await fetch(`api_watchlist.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) {
                    showToast('Watchlist entry removed');
                    loadWatchlist();
                } else {
                    showToast(data.error || 'Failed to delete', 'error');
                }
            } catch(e) {
                showToast('Network error while deleting', 'error');
            }
        }
    };

    // Modal Control
    document.getElementById('add-watchlist-btn').addEventListener('click', () => {
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-bookmark" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Watchlist Entry';
        form.reset();
        document.getElementById('form-id').value = '';
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('form-user').focus(), 100);
    });

    const closeModal = () => { modal.style.display = 'none'; form.reset(); };
    document.getElementById('modal-close').addEventListener('click', closeModal);
    document.getElementById('modal-cancel').addEventListener('click', closeModal);
    document.getElementById('modal-overlay').addEventListener('click', closeModal);

    // Form Validation Rules
    const rules = {
        'form-user': {
            validate: val => val.length >= 2,
            msg: 'Name must be at least 2 characters'
        },
        'form-title': {
            validate: val => val.length >= 2,
            msg: 'Title must be at least 2 characters'
        },
        'form-type': {
            validate: val => val !== '',
            msg: 'Please select a platform type'
        }
    };

    function validateField(id) {
        const field = document.getElementById(id);
        const val = field.value.trim();
        const rule = rules[id];
        
        const isValid = rule.validate(val);
        const errEl = document.getElementById('err-' + id.split('-')[1]);
        
        if (!isValid && val !== '' || !isValid && document.getElementById('validation-summary').style.display === 'block') {
            field.style.borderColor = '#ef4444';
            errEl.textContent = rule.msg;
            errEl.style.display = 'block';
            return rule.msg;
        } else if (isValid) {
            field.style.borderColor = '#10b981';
            errEl.style.display = 'none';
            return null;
        } else {
            // initial empty state
            field.style.borderColor = 'var(--border-color)';
            errEl.style.display = 'none';
            return null;
        }
    }

    // Bind real-time validation
    ['form-user', 'form-title', 'form-type'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('blur', () => validateField(id));
        el.addEventListener('input', () => {
            if(document.getElementById('validation-summary').style.display === 'block') {
                validateField(id);
            }
        });
    });

    // Character counters
    ['user', 'title'].forEach(f => {
        const input = document.getElementById('form-' + f);
        const counter = document.getElementById(f + '-counter');
        const max = input.getAttribute('maxlength');
        input.addEventListener('input', () => {
            const len = input.value.length;
            counter.textContent = `${len} / ${max}`;
            counter.style.color = len >= max ? '#ef4444' : len >= max * 0.8 ? '#f59e0b' : 'var(--text-muted)';
        });
    });

    // Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        let errors = [];
        let firstErrField = null;

        ['form-user', 'form-title', 'form-type'].forEach(id => {
            document.getElementById('validation-summary').style.display = 'block'; // force validation UI
            const err = validateField(id);
            if (err) {
                errors.push(`<li><i class="fas fa-circle" style="font-size:0.4rem; vertical-align:middle; margin-right:0.4rem;"></i>${err}</li>`);
                if (!firstErrField) firstErrField = document.getElementById(id);
            }
        });

        const summary = document.getElementById('validation-summary');
        const list = document.getElementById('error-list');

        if (errors.length > 0) {
            summary.style.display = 'block';
            list.innerHTML = errors.join('');
            
            // Shake effect
            form.classList.remove('shake');
            void form.offsetWidth;
            form.classList.add('shake');
            
            if (firstErrField) firstErrField.focus();
            return;
        }

        summary.style.display = 'none';

        // Saving logic
        const saveBtn = document.getElementById('modal-save');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;
        saveBtn.style.opacity = '0.7';

        try {
            const id = document.getElementById('form-id').value;
            const payload = {
                id: id,
                user_name: document.getElementById('form-user').value.trim(),
                title: document.getElementById('form-title').value.trim(),
                type: document.getElementById('form-type').value,
                priority: document.getElementById('form-priority').value,
                avatar: `https://i.pravatar.cc/150?u=${Math.floor(Math.random() * 1000)}`,
                added_date: new Date().toISOString().split('T')[0],
                timestamp: Math.floor(Date.now() / 1000)
            };

            const action = id ? 'update' : 'create';
            const res = await fetch(`api_watchlist.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if(data.success) {
                showToast(id ? 'Entry updated successfully' : 'Entry added to watchlist');
                closeModal();
                loadWatchlist();
            } else {
                showToast(data.error || 'Failed to save', 'error');
            }
        } catch(e) {
            showToast('Network error while saving', 'error');
        } finally {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });

    const resetFormUI = () => {
        form.reset();
        document.getElementById('validation-summary').style.display = 'none';
        ['form-user', 'form-title', 'form-type'].forEach(id => {
            document.getElementById(id).style.borderColor = 'var(--border-color)';
            document.getElementById('err-' + id.split('-')[1]).style.display = 'none';
        });
        ['user', 'title'].forEach(f => {
            const ctr = document.getElementById(f + '-counter');
            ctr.textContent = `0 / ${document.getElementById('form-' + f).getAttribute('maxlength')}`;
            ctr.style.color = 'var(--text-muted)';
        });
    }

    const closeModal = () => { 
        modal.style.display = 'none'; 
        resetFormUI();
    };
    
    // Override 'Add' button to also reset UI
    document.getElementById('add-watchlist-btn').addEventListener('click', () => {
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-bookmark" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Watchlist Entry';
        document.getElementById('form-id').value = '';
        resetFormUI();
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('form-user').focus(), 100);
    });

    document.getElementById('modal-close').addEventListener('click', closeModal);
    document.getElementById('modal-cancel').addEventListener('click', closeModal);
    document.getElementById('modal-overlay').addEventListener('click', closeModal);

    // Event Listeners for Filters
    searchInput.addEventListener('input', render);
    priorityFilter.addEventListener('change', render);
    sortFilter.addEventListener('change', render);

    loadWatchlist();
});
</script>

<?php include 'footer.php'; ?>

