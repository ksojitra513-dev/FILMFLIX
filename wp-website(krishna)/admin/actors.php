<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Cast & Crew Database</h2>
        <button id="add-actor-btn" class="dynamic-cta-btn" style="background: var(--primary-color); border: 2px solid transparent; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 15px rgba(99,102,241,0.2);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" onmousedown="this.style.transform='scale(0.96)'" onmouseup="this.style.transform='translateY(-2px)'">
            <i class="fas fa-user-plus"></i> Add Profile
        </button>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <input type="text" id="search-input" placeholder="Search actors, directors, writers..." style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem; border-radius: 10px; flex-grow: 1; outline: none; transition: border-color 0.2s;">
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="actors-table">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Known For</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="actors-body">
                <!-- Dynamically populated -->
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div id="empty-state" style="display: none; text-align: center; padding: 4rem 2rem; background: var(--glass-bg); border-radius: 16px; border: 1px solid var(--border-color); margin-top: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(99,102,241,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-user-slash" style="font-size: 2.5rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-main); margin-bottom: 0.75rem; font-size: 1.25rem;">No profiles found</h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem; line-height: 1.6;">We couldn't find any cast or crew matching your search. Try a different name or add a new profile.</p>
        </div>
    </div>
</div>

<!-- ========== ADD/EDIT MODAL ========== -->
<div id="actor-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; animation: modalIn 0.3s ease;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); position: sticky; top: 0; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 24px 24px 0 0; z-index: 2;">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-user-plus" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Profile</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border-color)'"><i class="fas fa-times"></i></button>
        </div>

        <form id="actor-form" style="padding: 1.5rem 2rem;" novalidate>
            <input type="hidden" id="form-id">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>Full Name <span style="color: var(--danger);">*</span></span>
                        <span id="name-counter" style="font-size: 0.72rem;">0 / 50</span>
                    </label>
                    <input type="text" id="form-name" maxlength="50" placeholder="e.g. Leonardo DiCaprio" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                    <p class="field-err" id="err-name" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>Known For</span>
                        <span id="work-counter" style="font-size: 0.72rem;">0 / 100</span>
                    </label>
                    <input type="text" id="form-work" maxlength="100" placeholder="e.g. Inception, Titanic" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Primary Role <span style="color: var(--danger);">*</span></label>
                        <select id="form-role" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                            <option value="">— Select Role —</option>
                            <option value="Actor">Actor</option>
                            <option value="Director">Director</option>
                            <option value="Writer">Writer</option>
                            <option value="Producer">Producer</option>
                            <option value="Composer">Composer</option>
                        </select>
                        <p class="field-err" id="err-role" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Status</label>
                        <select id="form-status" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                            <option value="Active">🟢 Active</option>
                            <option value="Inactive">🟡 Inactive</option>
                            <option value="Retired">🔴 Retired</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Validation Summary -->
            <div id="validation-summary" style="display: none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 0.85rem 1.1rem; margin-top: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 0.85rem;"></i>
                    <span style="color: #ef4444; font-weight: 600; font-size: 0.85rem;">Please fix the following:</span>
                </div>
                <ul id="error-list" style="list-style: none; padding: 0; margin: 0; font-size: 0.8rem; color: var(--text-muted);"></ul>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s;">Cancel</button>
                <button type="submit" id="modal-save" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: all 0.2s;">
                    Save Profile
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
    .shake { animation: shake 0.3s ease-in-out; }
    input:focus, select:focus { border-color: var(--primary-color) !important; }
    .action-btn { background: var(--glass-bg); border: 1px solid var(--border-color); width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; margin-right: 0.4rem; }
    .action-btn:hover { background: var(--border-color); transform: translateY(-2px); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .field-err { animation: modalIn 0.2s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Shared Data
    let actors = [];

    const tbody = document.getElementById('actors-body');
    const table = document.getElementById('actors-table');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search-input');
    const modal = document.getElementById('actor-modal');
    const form = document.getElementById('actor-form');

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

    function getAvatar(name) {
        let clean = name.replace(/[^a-zA-Z\s]/g, '').trim().split(' ').join('+');
        return `https://ui-avatars.com/api/?name=${clean}&background=6366f1&color=fff`;
    }

    function getStatusBadge(s, id) {
        const colors = {
            Active: { bg: 'rgba(16, 185, 129, 0.2)', color: '#10b981' },
            Inactive: { bg: 'rgba(245, 158, 11, 0.2)', color: '#f59e0b' },
            Retired: { bg: 'rgba(239, 68, 68, 0.2)', color: '#ef4444' }
        };
        const c = colors[s] || colors.Active;
        return `<span class="status-badge" style="background: ${c.bg}; color: ${c.color}; cursor: pointer" onclick="cycleStatus(${id})" title="Click to change Status">${s.toUpperCase()}</span>`;
    }

    function renderDOM() {
        const q = searchInput.value.toLowerCase().trim();
        const filtered = actors.filter(a => a.name.toLowerCase().includes(q) || (a.knownFor && a.knownFor.toLowerCase().includes(q)) || a.role.toLowerCase().includes(q));

        tbody.innerHTML = '';
        if (filtered.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = 'table';
            emptyState.style.display = 'none';
            
            filtered.forEach((a, i) => {
                const tr = document.createElement('tr');
                tr.style.animation = `fadeInUp 0.3s ease forwards ${i * 0.05}s`;
                tr.style.opacity = '0';
                tr.innerHTML = `
                    <td><img src="${getAvatar(a.name)}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                    <td style="font-weight: 500; color: var(--text-main);">${a.name}</td>
                    <td style="color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${a.knownFor}">${a.knownFor || '-'}</td>
                    <td style="color: var(--text-muted);">${a.role}</td>
                    <td>${getStatusBadge(a.status, a.id)}</td>
                    <td style="white-space: nowrap;">
                        <button onclick="editActor(${a.id})" class="action-btn" style="color: var(--primary-color);" title="Edit"><i class="fas fa-pen"></i></button>
                        <button onclick="deleteActor(${a.id})" class="action-btn" style="color: var(--danger);" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    }

    async function loadActors() {
        try {
            const res = await fetch('api_actors.php?action=read');
            actors = await res.json();
            renderDOM();
        } catch(e) {
            showToast('Failed to load actors from database', 'error');
        }
    }

    window.cycleStatus = async function(id) {
        try {
            const res = await fetch(`api_actors.php?action=cycle_status&id=${id}`);
            const data = await res.json();
            if(data.success) {
                const ac = actors.find(a => a.id == id);
                ac.status = data.new_status;
                renderDOM();
                showToast('Status updated to ' + ac.status);
            } else {
                showToast(data.error || 'Update failed', 'error');
            }
        } catch(e) {
            showToast('Network error', 'error');
        }
    };

    window.editActor = function(id) {
        const ac = actors.find(a => a.id == id);
        if(!ac) return;
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-pen" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Edit Profile';
        document.getElementById('form-id').value = ac.id;
        document.getElementById('form-name').value = ac.name;
        document.getElementById('form-work').value = ac.knownFor;
        document.getElementById('form-role').value = ac.role;
        document.getElementById('form-status').value = ac.status;
        
        document.getElementById('name-counter').textContent = `${ac.name.length} / 50`;
        document.getElementById('work-counter').textContent = `${ac.knownFor.length} / 100`;
        
        modal.style.display = 'flex';
    };

    window.deleteActor = async function(id) {
        if(confirm('Are you sure you want to remove this profile permanently?')) {
            try {
                const res = await fetch(`api_actors.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) {
                    actors = actors.filter(a => a.id != id);
                    renderDOM();
                    showToast('Profile removed successfully');
                } else {
                    showToast(data.error || 'Failed to delete', 'error');
                }
            } catch(e) {
                showToast('Network error', 'error');
            }
        }
    };

    // Validation
    const rules = {
        'form-name': { validate: v => v.length >= 2, msg: 'Name must be at least 2 characters' },
        'form-role': { validate: v => v !== '', msg: 'Please select a primary role' }
    };

    function validateField(id) {
        const field = document.getElementById(id);
        const val = field.value.trim();
        const rule = rules[id];
        const errEl = document.getElementById('err-' + id.split('-')[1]);
        
        const isValid = rule.validate(val);
        
        if (!isValid && (val !== '' || document.getElementById('validation-summary').style.display === 'block')) {
            field.style.borderColor = '#ef4444';
            errEl.textContent = rule.msg;
            errEl.style.display = 'block';
            return rule.msg;
        } else if (isValid) {
            field.style.borderColor = '#10b981';
            errEl.style.display = 'none';
        } else {
            field.style.borderColor = 'var(--border-color)';
            errEl.style.display = 'none';
        }
        return null;
    }

    ['form-name', 'form-role'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('blur', () => validateField(id));
        el.addEventListener('input', () => {
            if(document.getElementById('validation-summary').style.display === 'block') validateField(id);
        });
    });

    ['name', 'work'].forEach(f => {
        const inpt = document.getElementById('form-' + f);
        inpt.addEventListener('input', (e) => {
            const len = e.target.value.length;
            const max = inpt.getAttribute('maxlength');
            const ctr = document.getElementById(f + '-counter');
            ctr.textContent = `${len} / ${max}`;
            ctr.style.color = len >= max ? '#ef4444' : len >= max * 0.8 ? '#f59e0b' : 'var(--text-muted)';
        });
    });

    const resetUI = () => {
        form.reset();
        document.getElementById('validation-summary').style.display = 'none';
        ['form-name', 'form-role'].forEach(id => {
            document.getElementById(id).style.borderColor = 'var(--border-color)';
            document.getElementById('err-' + id.split('-')[1]).style.display = 'none';
        });
        document.getElementById('name-counter').textContent = '0 / 50';
        document.getElementById('work-counter').textContent = '0 / 100';
        document.getElementById('name-counter').style.color = 'var(--text-muted)';
        document.getElementById('work-counter').style.color = 'var(--text-muted)';
    };

    document.getElementById('add-actor-btn').addEventListener('click', () => {
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-plus" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Profile';
        document.getElementById('form-id').value = '';
        resetUI();
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('form-name').focus(), 100);
    });

    const closeMod = () => { modal.style.display = 'none'; resetUI(); };
    document.getElementById('modal-close').addEventListener('click', closeMod);
    document.getElementById('modal-cancel').addEventListener('click', closeMod);
    document.getElementById('modal-overlay').addEventListener('click', closeMod);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let errors = [];
        let firstErr = null;

        ['form-name', 'form-role'].forEach(id => {
            document.getElementById('validation-summary').style.display = 'block';
            const err = validateField(id);
            if (err) {
                errors.push(`<li><i class="fas fa-circle" style="font-size:0.4rem; vertical-align:middle; margin-right:0.4rem;"></i>${err}</li>`);
                if (!firstErr) firstErr = document.getElementById(id);
            }
        });

        const sum = document.getElementById('validation-summary');
        const list = document.getElementById('error-list');

        if (errors.length > 0) {
            sum.style.display = 'block';
            list.innerHTML = errors.join('');
            form.classList.remove('shake'); void form.offsetWidth; form.classList.add('shake');
            if(firstErr) firstErr.focus();
            return;
        }

        sum.style.display = 'none';
        
        const btn = document.getElementById('modal-save');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;
        btn.style.opacity = '0.7';

        const id = document.getElementById('form-id').value;
        const payload = {
            id: id,
            name: document.getElementById('form-name').value.trim(),
            knownFor: document.getElementById('form-work').value.trim(),
            role: document.getElementById('form-role').value,
            status: document.getElementById('form-status').value
        };

        try {
            const res = await fetch('api_actors.php?action=' + (id ? 'update' : 'create'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if(data.success) {
                if(!id) payload.id = data.id;
                showToast(id ? 'Profile updated successfully' : 'New profile created');
                closeMod();
                await loadActors(); // refreshing from DB
            } else {
                showToast(data.error || 'Failed to save', 'error');
            }
        } catch(err) {
            showToast('Network error while saving', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    });

    searchInput.addEventListener('input', renderDOM);

    // Initial Database Fetch
    loadActors();
});
</script>

<?php include 'footer.php'; ?>
