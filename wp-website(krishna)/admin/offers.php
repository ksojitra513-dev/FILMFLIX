<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Coupon Codes & Seasonal Promotions</h2>
        <button id="add-coupon-btn" class="dynamic-cta-btn" style="background: var(--primary-color); border: 2px solid transparent; color: white; padding: 0.75rem 1.75rem; border-radius: 12px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" onmousedown="this.style.transform='scale(0.96)'" onmouseup="this.style.transform='translateY(-2px)'">
            <i class="fas fa-tag"></i> Create Coupon
        </button>
    </div>

    <!-- Empty State -->
    <div id="empty-state" style="display: none; text-align: center; padding: 4rem 2rem; background: var(--glass-bg); border-radius: 16px; border: 1px solid var(--border-color); margin-top: 2rem;">
        <div style="width: 80px; height: 80px; background: rgba(99,102,241,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i class="fas fa-ticket-alt" style="font-size: 2.5rem; color: var(--primary-color);"></i>
        </div>
        <h3 style="color: var(--text-main); margin-bottom: 0.75rem; font-size: 1.25rem;">No coupons available</h3>
        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 1.5rem; line-height: 1.6;">It looks like you haven't created any promotional codes yet.</p>
    </div>

    <div id="coupons-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Dynamically Populated -->
    </div>
</div>

<!-- ========== ADD/EDIT MODAL ========== -->
<div id="coupon-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 450px; animation: modalIn 0.3s ease;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color);">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-tag" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Create Coupon</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border-color)'"><i class="fas fa-times"></i></button>
        </div>

        <form id="coupon-form" style="padding: 1.5rem 2rem;" novalidate>
            <input type="hidden" id="form-id">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>Coupon Code <span style="color: var(--danger);">*</span></span>
                    </label>
                    <input type="text" id="form-code" placeholder="e.g. SUMMER50" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; text-transform: uppercase; font-family: monospace; font-size: 1.1rem; letter-spacing: 1px;">
                    <p class="field-err" id="err-code" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Description <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="form-desc" placeholder="e.g. 50% Off Monthly Plan" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                    <p class="field-err" id="err-desc" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Status</label>
                        <select id="form-status" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                            <option value="Active">🟢 Active</option>
                            <option value="Expired">🔴 Expired</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Theme Color</label>
                        <select id="form-color" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                            <option value="primary">Purple (Primary)</option>
                            <option value="success">Green (Success)</option>
                            <option value="warning">Orange (Warning)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Expiration Date <span style="color: var(--danger);">*</span></label>
                    <input type="date" id="form-date" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; color-scheme: dark;">
                    <p class="field-err" id="err-date" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>
            </div>

            <!-- Validation Summary -->
            <div id="validation-summary" style="display: none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 0.85rem 1.1rem; margin-top: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 0.85rem;"></i>
                    <span style="color: #ef4444; font-weight: 600; font-size: 0.85rem;">Please fix errors before saving.</span>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s;">Cancel</button>
                <button type="submit" id="modal-save" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: all 0.2s;">
                    Save Coupon
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes pulse-success { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }
    .shake { animation: shake 0.3s ease-in-out; }
    .field-err { animation: modalIn 0.2s ease; }
    
    .c-btn {
        padding: 0.45rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; font-weight: 500;
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem;
    }
    .c-btn-edit { background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); color: #818cf8; }
    .c-btn-edit:hover { background: rgba(99,102,241,0.2); transform: translateY(-2px); }
    
    .c-btn-deact { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; }
    .c-btn-deact:hover { background: rgba(239,68,68,0.2); transform: translateY(-2px); }
    
    .c-btn-copy { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: #fbbf24; }
    .c-btn-copy:hover { background: rgba(245,158,11,0.2); transform: translateY(-2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let coupons = [];

    const grid = document.getElementById('coupons-grid');
    const emptyState = document.getElementById('empty-state');
    const modal = document.getElementById('coupon-modal');
    const form = document.getElementById('coupon-form');

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const color = isError ? '#ef4444' : '#10b981';
        const icon = isError ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${color}; font-size: 1.25rem; ${!isError ? 'animation: pulse-success 2s infinite;' : ''}"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${isError ? 'Error' : 'Success'}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const d = new Date(dateString);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function getTheme(color, status) {
        if(status === 'Expired') return { color: 'var(--text-muted)', rgb: '148,163,184', hex: '#94a3b8' };
        if(color === 'success') return { color: '#10b981', rgb: '16,185,129', hex: '#10b981' };
        if(color === 'warning') return { color: '#f59e0b', rgb: '245,158,11', hex: '#f59e0b' };
        return { color: 'var(--primary-color)', rgb: '99,102,241', hex: '#818cf8' };
    }

    function renderDOM() {
        grid.innerHTML = '';
        if (coupons.length === 0) {
            grid.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        grid.style.display = 'grid';
        emptyState.style.display = 'none';

        coupons.forEach((c, i) => {
            const isExp = c.status === 'Expired';
            const theme = getTheme(c.color, c.status);
            
            const card = document.createElement('div');
            card.style.animation = `fadeInUp 0.3s ease forwards ${i * 0.05}s`;
            card.style.opacity = '0';
            
            let html = '';
            if(isExp) {
                card.style.cssText += `border: 1px solid var(--border-color); padding: 1.75rem; border-radius: 20px; background: rgba(30, 41, 59, 0.3); opacity: 0.65; position: relative; overflow: hidden;`;
                html = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="color: var(--text-muted); font-size: 1.6rem; letter-spacing: 2px; font-weight: 800;">${c.code}</h3>
                            <p style="margin-top: 0.5rem; font-size: 1rem; color: var(--text-muted);">${c.desc}</p>
                        </div>
                        <span class="status-badge" style="background: rgba(148, 163, 184, 0.2); color: #94a3b8;">Expired</span>
                    </div>
                    <div style="display: flex; gap: 2rem; margin: 1.25rem 0; font-size: 0.85rem; color: var(--text-muted);">
                        <div><i class="fas fa-users" style="margin-right: 0.4rem;"></i> Used: <strong>${(c.used || 0).toLocaleString()}</strong></div>
                        <div><i class="fas fa-calendar-alt" style="margin-right: 0.4rem;"></i> Expired: <strong>${formatDate(c.exp)}</strong></div>
                    </div>
                    <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                        <button onclick="toggleStatus(${c.id})" class="c-btn c-btn-edit" style="color: var(--primary-color);"><i class="fas fa-redo"></i> Reactivate</button>
                        <button onclick="deleteCoupon(${c.id})" class="c-btn c-btn-deact"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                `;
            } else {
                card.style.cssText += `border: 2px dashed rgba(${theme.rgb},0.4); padding: 1.75rem; border-radius: 20px; background: linear-gradient(135deg, rgba(${theme.rgb},0.08), rgba(${theme.rgb},0.02)); position: relative; overflow: hidden; transition: transform 0.2s;`;
                card.onmouseover = () => card.style.transform = 'translateY(-3px)';
                card.onmouseout = () => card.style.transform = 'translateY(0)';
                
                html = `
                    <div style="position: absolute; top: -15px; right: -15px; width: 80px; height: 80px; background: rgba(${theme.rgb},0.08); border-radius: 50%;"></div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="color: ${theme.hex}; font-size: 1.6rem; letter-spacing: 2px; font-weight: 800;">${c.code}</h3>
                            <p style="margin-top: 0.5rem; font-size: 1rem; color: var(--text-main);">${c.desc}</p>
                        </div>
                        <span class="status-badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Active</span>
                    </div>
                    <div style="display: flex; gap: 2rem; margin: 1.25rem 0; font-size: 0.85rem; color: var(--text-muted);">
                        <div><i class="fas fa-users" style="margin-right: 0.4rem;"></i> Used: <strong style="color: var(--text-main);">${(c.used || 0).toLocaleString()}</strong></div>
                        <div><i class="fas fa-calendar-alt" style="margin-right: 0.4rem;"></i> Expires: <strong style="color: var(--text-main);">${formatDate(c.exp)}</strong></div>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem;">
                        <button onclick="editCoupon(${c.id})" class="c-btn c-btn-edit"><i class="fas fa-edit"></i> Edit</button>
                        <button onclick="toggleStatus(${c.id})" class="c-btn c-btn-deact"><i class="fas fa-ban"></i> Deactivate</button>
                        <button onclick="copyCode('${c.code}', this)" class="c-btn c-btn-copy"><i class="fas fa-copy"></i> Copy</button>
                    </div>
                `;
            }
            card.innerHTML = html;
            grid.appendChild(card);
        });
    }

    async function loadOffers() {
        try {
            const res = await fetch('api_offers.php?action=read');
            coupons = await res.json();
            renderDOM();
        } catch(e) {
            showToast('Failed to load coupons from database', 'error');
        }
    }

    window.copyCode = function(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            const orgText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            showToast(`Code ${code} copied to clipboard`);
            setTimeout(() => btn.innerHTML = orgText, 2000);
        });
    };

    window.toggleStatus = async function(id) {
        try {
            const res = await fetch(`api_offers.php?action=toggle_status&id=${id}`);
            const data = await res.json();
            if(data.success) {
                const c = coupons.find(x => x.id == id);
                c.status = data.new_status;
                renderDOM();
                showToast(`Coupon ${c.code} is now ${c.status}`);
            } else {
                showToast(data.error || 'Update failed', 'error');
            }
        } catch(e) {
            showToast('Network error', 'error');
        }
    };

    window.deleteCoupon = async function(id) {
        if(confirm('Delete this coupon permanently?')) {
            try {
                const res = await fetch(`api_offers.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) {
                    coupons = coupons.filter(x => x.id != id);
                    renderDOM();
                    showToast('Coupon deleted securely');
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
        'form-code': { validate: v => v.length >= 3, msg: 'Code must be at least 3 characters' },
        'form-desc': { validate: v => v.length >= 5, msg: 'Description must be at least 5 chars' },
        'form-date': { validate: v => v !== '', msg: 'Expiration date is required' }
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
            return false;
        } else if (isValid) {
            field.style.borderColor = '#10b981';
            errEl.style.display = 'none';
        } else {
            field.style.borderColor = 'var(--border-color)';
            errEl.style.display = 'none';
        }
        return true;
    }

    ['form-code', 'form-desc', 'form-date'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('blur', () => validateField(id));
        el.addEventListener('input', () => {
            if(id==='form-code') el.value = el.value.toUpperCase().replace(/\s/g, '');
            if(document.getElementById('validation-summary').style.display === 'block') validateField(id);
        });
    });

    const resetUI = () => {
        form.reset();
        document.getElementById('validation-summary').style.display = 'none';
        ['form-code', 'form-desc', 'form-date'].forEach(id => {
            document.getElementById(id).style.borderColor = 'var(--border-color)';
            document.getElementById('err-' + id.split('-')[1]).style.display = 'none';
        });
    };

    document.getElementById('add-coupon-btn').addEventListener('click', () => {
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-tag" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Create Coupon';
        document.getElementById('form-id').value = '';
        resetUI();
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('form-code').focus(), 100);
    });

    window.editCoupon = function(id) {
        const c = coupons.find(x => x.id == id);
        if(!c) return;
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-edit" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Edit Coupon';
        resetUI();
        document.getElementById('form-id').value = c.id;
        document.getElementById('form-code').value = c.code;
        document.getElementById('form-desc').value = c.desc;
        document.getElementById('form-status').value = c.status;
        document.getElementById('form-color').value = c.color;
        document.getElementById('form-date').value = c.exp;
        
        modal.style.display = 'flex';
    };

    const closeMod = () => { modal.style.display = 'none'; resetUI(); };
    document.getElementById('modal-close').addEventListener('click', closeMod);
    document.getElementById('modal-cancel').addEventListener('click', closeMod);
    document.getElementById('modal-overlay').addEventListener('click', closeMod);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        let firstErr = null;

        ['form-code', 'form-desc', 'form-date'].forEach(id => {
            document.getElementById('validation-summary').style.display = 'block';
            if (!validateField(id)) {
                isValid = false;
                if (!firstErr) firstErr = document.getElementById(id);
            }
        });

        if (!isValid) {
            form.classList.remove('shake'); void form.offsetWidth; form.classList.add('shake');
            if(firstErr) firstErr.focus();
            return;
        }

        document.getElementById('validation-summary').style.display = 'none';
        
        const btn = document.getElementById('modal-save');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;
        btn.style.opacity = '0.7';

        const id = document.getElementById('form-id').value;
        const payload = {
            id: id,
            code: document.getElementById('form-code').value.toUpperCase(),
            desc: document.getElementById('form-desc').value.trim(),
            status: document.getElementById('form-status').value,
            color: document.getElementById('form-color').value,
            exp: document.getElementById('form-date').value
        };

        try {
            const res = await fetch('api_offers.php?action=' + (id ? 'update' : 'create'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if(data.success) {
                if(!id) payload.id = data.id;
                showToast(id ? 'Coupon updated successfully' : 'New coupon created');
                closeMod();
                await loadOffers(); // refreshing from DB
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

    loadOffers();
});
</script>

<?php include 'footer.php'; ?>
