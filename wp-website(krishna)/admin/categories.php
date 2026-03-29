<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Content Categories</h2>
        <button id="add-category-btn" style="background: var(--primary-color); border: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-plus"></i> New Category
        </button>
    </div>

    <div id="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Dynamically rendered -->
    </div>
</div>

<!-- ========== ADD/EDIT MODAL ========== -->
<div id="category-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 450px; max-height: 90vh; overflow-y: auto; animation: modalIn 0.3s ease;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); position: sticky; top: 0; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 24px 24px 0 0; z-index: 2;">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-layer-group" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Category</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border-color)'"><i class="fas fa-times"></i></button>
        </div>

        <form id="category-form" style="padding: 1.5rem 2rem;" novalidate>
            <input type="hidden" id="form-id">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <!-- Name -->
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <span>Category Name <span style="color: var(--danger);">*</span></span>
                        <span id="name-counter" style="font-size: 0.72rem;">0 / 30</span>
                    </label>
                    <input type="text" id="form-name" maxlength="30" placeholder="e.g. Science Fiction" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease;">
                    <p class="field-err" id="err-name" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <!-- Icon -->
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Icon (FontAwesome) <span style="color: var(--danger);">*</span></label>
                        <select id="form-icon" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-family: 'Font Awesome 6 Free', Inter, sans-serif; font-weight: 900;">
                            <option value="">— Select Icon —</option>
                            <option value="fa-fist-raised">&#xf6de; Action/Fist</option>
                            <option value="fa-rocket">&#xf135; Rocket</option>
                            <option value="fa-ghost">&#xf6e2; Ghost</option>
                            <option value="fa-masks-theater">&#xf630; Drama Masks</option>
                            <option value="fa-face-laugh">&#xf599; Comedy/Laugh</option>
                            <option value="fa-user-secret">&#xf21b; Secret/Thriller</option>
                            <option value="fa-wand-magic-sparkles">&#xfe2b; Magic/Fantasy</option>
                            <option value="fa-camera">&#xf083; Camera/Doc</option>
                            <option value="fa-heart">&#xf004; Heart/Romance</option>
                            <option value="fa-globe">&#xf0ac; Globe</option>
                        </select>
                        <p class="field-err" id="err-icon" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                    </div>
                    <!-- Color -->
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Theme Color</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="form-color" value="#6366f1" style="width: 100%; height: 46px; background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; padding: 0.2rem;">
                        </div>
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
                    Save Category
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
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
    .shake { animation: shake 0.3s ease-in-out; }
    input:focus, select:focus { border-color: var(--primary-color) !important; }
    .cat-btn { background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s; padding: 0.3rem 0.75rem; display: flex; align-items: center; gap: 0.3rem; }
    .cat-btn:hover { background: var(--border-color); transform: translateY(-2px); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initial Data
    let categories = [];

    async function loadCategories() {
        try {
            const res = await fetch('api_categories.php?action=read');
            categories = await res.json();
            render();
        } catch(e) {
            showToast('Failed to load categories', 'error');
        }
    }

    const grid = document.getElementById('categories-grid');
    const modal = document.getElementById('category-modal');
    const form = document.getElementById('category-form');

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

    function render() {
        grid.innerHTML = '';
        categories.forEach((cat, i) => {
            const rgb = hexToRgbA(cat.color, 0.15);
            const borderRgb = hexToRgbA(cat.color, 0.3);
            
            const div = document.createElement('div');
            div.className = 'category-card';
            div.style.cssText = `background: var(--card-bg); backdrop-filter: blur(var(--blur)); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border-color); text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; opacity: 0; transform: translateY(20px); animation: fadeInUp 0.4s ease forwards ${i * 0.05}s;`;
            
            div.onmouseover = () => { div.style.transform = 'translateY(-5px)'; div.style.boxShadow = '0 12px 30px rgba(0,0,0,0.3)'; };
            div.onmouseout = () => { div.style.transform = 'translateY(0)'; div.style.boxShadow = 'none'; };

            div.innerHTML = `
                <div style="width: 55px; height: 55px; border-radius: 15px; background: ${rgb}; border: 1px solid ${borderRgb}; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.4rem; color: ${cat.color};">
                    <i class="fas ${cat.icon}"></i>
                </div>
                <h3 style="margin-bottom: 0.35rem; color: var(--text-main); font-size: 1.05rem;">${cat.name}</h3>
                <p style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 1.25rem;">${cat.count || 0} Titles</p>
                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <button onclick="editCategory(${cat.id})" class="cat-btn" style="color: var(--primary-color);">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button onclick="deleteCategory(${cat.id})" class="cat-btn" style="color: var(--danger);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            grid.appendChild(div);
        });
    }

    // Helpers
    function hexToRgbA(hex, alpha){
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c= hex.substring(1).split('');
            if(c.length== 3){
                c= [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c= '0x'+c.join('');
            return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+alpha+')';
        }
        return 'rgba(255,255,255,0.1)';
    }

    // CRUD Ops
    window.editCategory = function(id) {
        event.stopPropagation();
        const cat = categories.find(c => c.id == id);
        if(!cat) return;
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-edit" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Edit Category';
        document.getElementById('form-id').value = cat.id;
        document.getElementById('form-name').value = cat.name;
        document.getElementById('form-icon').value = cat.icon;
        document.getElementById('form-color').value = cat.color;
        
        let counter = document.getElementById('name-counter');
        counter.textContent = `${cat.name.length} / 30`;
        counter.style.color = 'var(--text-muted)';
        
        modal.style.display = 'flex';
    };

    window.deleteCategory = async function(id) {
        event.stopPropagation();
        if(confirm('Are you sure you want to delete this category? This might orphan some titles.')) {
            try {
                const res = await fetch(`api_categories.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) {
                    showToast('Category deleted successfully');
                    loadCategories();
                } else {
                    showToast(data.error || 'Failed to delete', 'error');
                }
            } catch(e) {
                showToast('Network error while deleting', 'error');
            }
        }
    };

    // Validation
    const rules = {
        'form-name': { validate: v => v.length >= 2, msg: 'Name must be at least 2 characters' },
        'form-icon': { validate: v => v !== '', msg: 'Please select an icon' }
    };

    function validateField(id) {
        const field = document.getElementById(id);
        const val = field.value.trim();
        const rule = rules[id];
        const isValid = rule.validate(val);
        const errEl = document.getElementById('err-' + id.split('-')[1]);
        
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

    ['form-name', 'form-icon'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('blur', () => validateField(id));
        el.addEventListener('input', () => {
            if(document.getElementById('validation-summary').style.display === 'block') validateField(id);
        });
    });

    document.getElementById('form-name').addEventListener('input', (e) => {
        const len = e.target.value.length;
        const ctr = document.getElementById('name-counter');
        ctr.textContent = `${len} / 30`;
        ctr.style.color = len >= 30 ? '#ef4444' : len >= 24 ? '#f59e0b' : 'var(--text-muted)';
    });

    // Control
    const resetUI = () => {
        form.reset();
        document.getElementById('form-color').value = '#6366f1';
        document.getElementById('validation-summary').style.display = 'none';
        ['form-name', 'form-icon'].forEach(id => {
            document.getElementById(id).style.borderColor = 'var(--border-color)';
            document.getElementById('err-' + id.split('-')[1]).style.display = 'none';
        });
        document.getElementById('name-counter').textContent = '0 / 30';
        document.getElementById('name-counter').style.color = 'var(--text-muted)';
    };

    document.getElementById('add-category-btn').addEventListener('click', () => {
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-layer-group" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add Category';
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

        ['form-name', 'form-icon'].forEach(id => {
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

        try {
            const id = document.getElementById('form-id').value;
            const payload = {
                id: id,
                name: document.getElementById('form-name').value.trim(),
                icon: document.getElementById('form-icon').value,
                color: document.getElementById('form-color').value
            };
            const action = id ? 'update' : 'create';
            const res = await fetch(`api_categories.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if(data.success) {
                showToast(id ? 'Category updated' : 'New category created');
                closeMod();
                loadCategories();
            } else {
                showToast(data.error || 'Failed to save', 'error');
            }
        } catch(e) {
            showToast('Network error while saving', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    loadCategories();
});
</script>

<?php include 'footer.php'; ?>
