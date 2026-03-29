<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-images" style="color: var(--primary-color); margin-right: 0.75rem;"></i>Hero & Ad Banners</h2>
        <button id="add-banner-btn" class="dynamic-cta-btn" style="background: var(--primary-color); border: none; color: #fff; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-plus-circle"></i> New Banner
        </button>
    </div>
    
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <p style="color: var(--text-muted); font-size: 0.9rem;">Manage sliders and promotional display ads across the platform.</p>
        <div style="display: flex; gap: 0.75rem;">
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" style="display: none; text-align: center; padding: 5rem 2rem; background: var(--glass-bg); border-radius: 20px; border: 1px solid var(--border-color); animation: modalIn 0.4s ease;">
        <div style="width: 100px; height: 100px; background: rgba(99,102,240,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i class="fas fa-layer-group" style="font-size: 2.5rem; color: var(--primary-color);"></i>
        </div>
        <h3 style="color: var(--text-main); margin-bottom: 0.75rem;">No banners found</h3>
        <p style="color: var(--text-muted);">Try adjusting your filters or upload a new creative banner.</p>
    </div>

    <div id="banners-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <!-- Dynamically Populated -->
    </div>
</div>

<!-- ========== BANNER MODAL ========== -->
<div id="banner-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 500px; animation: modalIn 0.3s ease; overflow: hidden;">
        
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;">Upload New Banner</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 34px; height: 34px; border-radius: 10px; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <form id="banner-form" style="padding: 1.5rem 2rem;" novalidate>
            <input type="hidden" id="form-id">
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Display Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="form-title" placeholder="e.g. Summer Spectacular" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s;">
                    <p class="field-err" id="err-title" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; display: none;"></p>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Subtitle</label>
                    <input type="text" id="form-subtitle" placeholder="e.g. Experience the thriller of the year." style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Asset Source (Image)</label>
                        <select id="form-asset" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.75rem 1rem; border-radius: 10px; outline: none;">
                            <option value="1">Lala Wallpaper</option>
                            <option value="2">URI Poster</option>
                            <option value="3">Satyamev Jayte</option>
                            <option value="4">War Poster</option>
                            <option value="5">Saiyaara Wallpaper</option>
                        </select>
                    </div>
                </div>

                <div id="image-preview-container" style="background: rgba(0,0,0,0.2); border: 2px dashed var(--border-color); border-radius: 12px; height: 120px; display: flex; align-items: center; justify-content: center; margin-top: 0.5rem;">
                    <span style="color: var(--text-muted); font-size: 0.8rem;"><i class="fas fa-image" style="margin-right: 0.5rem;"></i> Image Preview Enabled</span>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 10px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.85rem; border-radius: 10px; cursor: pointer; font-weight: 700; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                    Save Banner
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes slideOutFade { to { opacity: 0; transform: scale(0.9); } }
    .banner-card {
        padding: 0; overflow: hidden; display: flex; flex-direction: column;
        background: var(--glass-bg); border: 1px solid var(--border-color);
        border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .banner-card:hover { transform: translateY(-5px); border-color: rgba(99,102,241,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .action-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; border: 1px solid rgba(255,255,255,0.05); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let banners = [];

    const grid = document.getElementById('banners-grid');
    const emptyState = document.getElementById('empty-state');
    const modal = document.getElementById('banner-modal');
    const form = document.getElementById('banner-form');
    const statusFilter = document.getElementById('status-filter');

    async function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const color = isError ? '#ef4444' : '#10b981';
        toast.innerHTML = `
            <i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}" style="color: ${color}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${isError ? 'Error' : 'Success'}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // Status badge is no longer in this specific schema
    function getBadgeStyle(status) { return ''; }

    async function loadBanners() {
        try {
            const resp = await fetch('api_banners.php?action=read');
            banners = await resp.json();
            render();
        } catch(e) {
            showToast('Unable to connect to database', 'error');
        }
    }

    function render() {
        grid.innerHTML = '';
        const filtered = banners; // No status filtering in this schema

        if (filtered.length === 0) {
            grid.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        grid.style.display = 'grid';
        emptyState.style.display = 'none';

        filtered.forEach((b, i) => {
            const card = document.createElement('div');
            card.className = 'banner-card';
            card.style.animation = `fadeInUp 0.4s ease forwards ${i * 0.05}s`;
            card.style.opacity = '0';
            
            card.innerHTML = `
                <div style="height: 180px; position: relative; background: #000;">
                    <img src="${b.imagurl}" style="width: 100%; height: 100%; object-fit: cover; transition: all 0.3s;">
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${b.title}</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5; min-height: 2.5rem;">${b.subtitle || 'No subtitle'}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.25rem;">
                        <div style="display: flex; gap: 0.6rem;">
                            <button onclick="editBanner(${b.id})" class="action-btn" style="background: rgba(99,102,241,0.1); color: #818cf8;" title="Edit Banner"><i class="fas fa-edit"></i></button>
                            <button onclick="window.open('${b.image_url}', '_blank')" class="action-btn" style="background: rgba(255,255,255,0.05); color: #fff;" title="View Image"><i class="fas fa-eye"></i></button>
                        </div>
                        <button onclick="removeBanner(${b.id}, this)" class="action-btn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.1);" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    window.removeBanner = async function(id, btn) {
        if(confirm('Permanently delete this banner? This action cannot be undone.')) {
            const card = btn.closest('.banner-card');
            card.style.animation = 'slideOutFade 0.3s ease forwards';
            
            try {
                const resp = await fetch('api_banners.php?action=delete', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                });
                const result = await resp.json();
                if(result.success) {
                    banners = banners.filter(b => b.id != id);
                    setTimeout(() => render(), 300);
                    showToast('Banner removed securely');
                } else {
                    card.style.animation = ''; // revert
                    showToast(result.error || 'Deletion failed', 'error');
                }
            } catch(e) {
                card.style.animation = ''; // revert
                showToast('Network error while deleting', 'error');
            }
        }
    };

    window.editBanner = function(id) {
        const b = banners.find(x => x.id == id);
        if(!b) return;
        document.getElementById('modal-title').textContent = 'Modify Banner';
        document.getElementById('form-id').value = b.id;
        document.getElementById('form-title').value = b.title;
        document.getElementById('form-subtitle').value = b.subtitle;
        modal.style.display = 'flex';
    };

    document.getElementById('add-banner-btn').addEventListener('click', () => {
        document.getElementById('modal-title').textContent = 'Upload New Creative';
        document.getElementById('form-id').value = '';
        form.reset();
        modal.style.display = 'flex';
    });

    // Validation helper
    const validateField = (id) => {
        const field = document.getElementById(id);
        const val = field.value.trim();
        const errEl = document.getElementById('err-' + id.split('-')[1]);
        
        let isValid = true;
        let msg = '';

        if (id === 'form-title') {
            if (!val) { msg = 'Headline is required'; isValid = false; }
            else if (val.length < 3) { msg = 'Title is too short'; isValid = false; }
        }

        if (!isValid) {
            field.style.borderColor = '#ef4444';
            errEl.textContent = msg;
            errEl.style.display = 'block';
        } else {
            field.style.borderColor = 'var(--border-color)';
            errEl.style.display = 'none';
        }
        return isValid;
    };

    const resetValidation = () => {
        ['form-title'].forEach(id => {
            const field = document.getElementById(id);
            const errEl = document.getElementById('err-' + id.split('-')[1]);
            field.style.borderColor = 'var(--border-color)';
            errEl.style.display = 'none';
        });
    };

    ['form-title'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => validateField(id));
        document.getElementById(id).addEventListener('blur', () => validateField(id));
    });

    const closeMod = () => {
        modal.style.display = 'none';
        resetValidation();
    };

    document.getElementById('modal-close').addEventListener('click', closeMod);
    document.getElementById('modal-cancel').addEventListener('click', closeMod);
    document.getElementById('modal-overlay').addEventListener('click', closeMod);
    statusFilter.addEventListener('change', render);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Comprehensive Validation
        const isTitleValid = validateField('form-title');
        
        if (!isTitleValid) {
            form.classList.remove('shake');
            void form.offsetWidth; // Trigger reflow
            form.classList.add('shake');
            document.getElementById('form-title').focus();
            return;
        }

        const id = document.getElementById('form-id').value;
        const title = document.getElementById('form-title').value.trim();
        const caption = document.getElementById('form-caption').value.trim();
        const status = document.getElementById('form-status').value;
        const assetIndex = document.getElementById('form-asset').value;

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;

        const assets = [
            'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=2070',
            'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=2070',
            'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2050'
        ];

        const subtitle = document.getElementById('form-subtitle').value.trim();
        const assetIndex = document.getElementById('form-asset').value;

        const assets = [
            'Laalo-wallpaper.jpg',
            'uri.webp',
            'satyamevjayte.webp',
            'war 11.jpg',
            'Saiyaara.jpg'
        ];

        const payload = {
            id: id,
            title: title,
            subtitle: subtitle,
            imagurl: assets[assetIndex-1]
        };

        try {
            const action = id ? 'update' : 'create';
            const resp = await fetch(`api_banners.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await resp.json();
            
            if(result.success) {
                showToast(id ? 'Creative updated' : 'Creative live now');
                closeMod();
                loadBanners();
            } else {
                showToast(result.error || 'Operation failed', 'error');
            }
        } catch(e) {
            showToast('Network error while saving', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // Initial load
    loadBanners();
});
</script>
</div>

<?php include 'footer.php'; ?>
