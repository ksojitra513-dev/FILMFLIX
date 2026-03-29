<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-cogs" style="color: var(--primary-color); margin-right: 0.75rem;"></i>Site Config & API Keys</h2>
    </div>
    
    <style>
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        .input-error {
            border-color: #ef4444 !important;
            animation: shakeError 0.4s ease;
        }
    </style>
    
    <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 800px; margin-top: 1.5rem;">
        
        <!-- API Section -->
        <section style="background: var(--glass-bg); padding: 2rem; border-radius: 20px; border: 1px solid var(--border-color); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--primary-color);"></div>
            <h3 style="color: var(--text-main); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; font-size: 1.2rem;">
                <i class="fas fa-key" style="color: var(--primary-color); background: rgba(99,102,241,0.1); padding: 0.5rem; border-radius: 8px;"></i> API Configuration
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">TMDB API Key (v3 auth) <span style="color: var(--danger);">*</span></label>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-start;">
                        <div style="position: relative; flex-grow: 1; min-width: 200px;">
                            <input type="password" id="tmdb-key" value="849ff02bd0b938c4f92305a5c6e86481" style="width: 100%; height: 46px; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 2.5rem 0.8rem 1.25rem; border-radius: 12px; outline: none; font-family: monospace; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="if(!this.classList.contains('input-error')) this.style.borderColor='var(--border-color)'">
                            <i class="fas fa-eye" id="toggle-tmdb-vis" style="position: absolute; right: 1rem; top: 23px; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; transition: color 0.2s;" onclick="toggleVisibility('tmdb-key', this)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'"></i>
                            <p id="err-tmdb" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.4rem; display: none;"></p>
                        </div>
                        <button onclick="generateKey('tmdb-key')" style="height: 46px; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); color: #818cf8; padding: 0 1.25rem; border-radius: 12px; cursor: pointer; transition: all 0.2s;" title="Regenerate Key" onmouseover="this.style.background='rgba(99,102,241,0.2)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'"><i class="fas fa-redo"></i></button>
                        <button onclick="copyToClipboard('tmdb-key')" style="height: 46px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-muted); padding: 0 1.25rem; border-radius: 12px; cursor: pointer; transition: all 0.2s;" title="Copy to Clipboard" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                
                <div style="margin-top: 0.5rem; padding-top: 1.5rem; border-top: 1px dashed rgba(255,255,255,0.1);">
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">IMDb Official API Client Secret <span style="color: var(--danger);">*</span></label>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-start;">
                        <div style="position: relative; flex-grow: 1; min-width: 200px;">
                            <input type="text" id="imdb-secret" value="SK_LIVE_9042b94cc82110ea388" style="width: 100%; height: 46px; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none; font-family: monospace; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="if(!this.classList.contains('input-error')) this.style.borderColor='var(--border-color)'">
                            <p id="err-imdb" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.4rem; display: none;"></p>
                        </div>
                        <button onclick="copyToClipboard('imdb-secret')" style="height: 46px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-muted); padding: 0 1.25rem; border-radius: 12px; cursor: pointer; transition: all 0.2s;" title="Copy to Clipboard" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- General Section -->
        <section style="background: var(--glass-bg); padding: 2rem; border-radius: 20px; border: 1px solid var(--border-color); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--accent);"></div>
            <h3 style="color: var(--text-main); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; font-size: 1.2rem;">
                <i class="fas fa-tools" style="color: var(--accent); background: rgba(14,165,233,0.1); padding: 0.5rem; border-radius: 8px;"></i> General Site Settings
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">Site Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="site-name" value="FILMFLIX Premium" style="width: 100%; height: 46px; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="if(!this.classList.contains('input-error')) this.style.borderColor='var(--border-color)'">
                        <p id="err-name" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.4rem; display: none;"></p>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">Support Contact Email <span style="color: var(--danger);">*</span></label>
                        <input type="email" id="support-email" value="admin@filmflix.com" style="width: 100%; height: 46px; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="if(!this.classList.contains('input-error')) this.style.borderColor='var(--border-color)'">
                        <p id="err-email" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.4rem; display: none;"></p>
                    </div>
                </div>
                
                <div style="margin-top: 0.5rem; padding-top: 1.5rem; border-top: 1px dashed rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label style="display: block; margin-bottom: 0.25rem; color: var(--text-main); font-size: 0.95rem; font-weight: 600;">Maintenance Mode</label>
                            <p style="color: var(--text-muted); font-size: 0.8rem; margin: 0;">Temporarily disable public access to the site for updates.</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span id="maint-label" style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Off</span>
                            <div id="maint-toggle" style="width: 50px; height: 28px; background: rgba(0,0,0,0.4); border: 1px solid var(--border-color); border-radius: 14px; position: relative; cursor: pointer; transition: all 0.3s;" onclick="toggleMaintenance()">
                                <div id="maint-knob" style="width: 22px; height: 22px; background: #94a3b8; border-radius: 50%; position: absolute; left: 3px; top: 2px; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <button id="save-btn" onclick="saveSettings()" style="background: var(--primary-color); border: none; color: white; padding: 0.9rem 2.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex: 1; min-width: 150px;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-save"></i> Save Configuration
            </button>
            <button onclick="resetSettings()" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.9rem 2.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.2s; flex: 1; min-width: 150px;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                Discard Changes
            </button>
        </div>
    </div>
</div>

<script>
    // Initial State
    const originalState = {
        tmdb: '849ff02bd0b938c4f92305a5c6e86481',
        imdb: 'SK_LIVE_9042b94cc82110ea388',
        name: 'FILMFLIX Premium',
        email: 'admin@filmflix.com',
        maintenance: false
    };

    let currentState = { ...originalState };

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const color = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#6366f1');
        const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
        toast.innerHTML = \`
            <i class="fas \${icon}" style="color: \${color}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">\${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">\${msg}</span>
            </div>
        \`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function toggleVisibility(inputId, icon) {
        const input = document.getElementById(inputId);
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function copyToClipboard(inputId) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);
        try {
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            showToast('Copied to clipboard!', 'info');
        } catch(err) {
            showToast('Unable to copy', 'error');
        }
    }

    function generateKey(inputId) {
        // Generating a fake key for demonstration
        const chars = 'abcdef0123456789';
        let newKey = '';
        for(let i=0; i<32; i++) newKey += chars.charAt(Math.floor(Math.random() * chars.length));
        
        document.getElementById(inputId).value = newKey;
        const input = document.getElementById(inputId);
        if(input.type === 'password') {
            toggleVisibility(inputId, document.getElementById('toggle-tmdb-vis'));
        }
        showToast('New API key generated. Make sure to save.', 'info');
    }

    function updateMaintenanceUI() {
        const knob = document.getElementById('maint-knob');
        const toggle = document.getElementById('maint-toggle');
        const label = document.getElementById('maint-label');

        if(currentState.maintenance) {
            knob.style.transform = 'translateX(20px)';
            knob.style.background = '#fff';
            toggle.style.background = '#f59e0b';
            toggle.style.borderColor = '#f59e0b';
            label.textContent = 'Active';
            label.style.color = '#f59e0b';
        } else {
            knob.style.transform = 'translateX(0)';
            knob.style.background = '#94a3b8';
            toggle.style.background = 'rgba(0,0,0,0.4)';
            toggle.style.borderColor = 'var(--border-color)';
            label.textContent = 'Off';
            label.style.color = '#94a3b8';
        }
    }

    function toggleMaintenance() {
        currentState.maintenance = !currentState.maintenance;
        updateMaintenanceUI();
    }

    function resetSettings() {
        if(confirm('Are you sure you want to discard unsaved changes?')) {
            document.getElementById('tmdb-key').value = originalState.tmdb;
            document.getElementById('imdb-secret').value = originalState.imdb;
            document.getElementById('site-name').value = originalState.name;
            document.getElementById('support-email').value = originalState.email;
            currentState.maintenance = originalState.maintenance;
            updateMaintenanceUI();
            
            const tmdbInput = document.getElementById('tmdb-key');
            if(tmdbInput.type === 'text') {
                toggleVisibility('tmdb-key', document.getElementById('toggle-tmdb-vis'));
            }
            
            showToast('Changes discarded', 'info');
        }
    }

    function clearError(inputId, errId) {
        const input = document.getElementById(inputId);
        const err = document.getElementById(errId);
        input.classList.remove('input-error');
        input.style.borderColor = 'var(--border-color)';
        err.style.display = 'none';
        err.textContent = '';
    }

    function showError(inputId, errId, message) {
        const input = document.getElementById(inputId);
        const err = document.getElementById(errId);
        
        // Reset animation
        input.style.animation = 'none';
        input.offsetHeight; // trigger reflow
        
        input.classList.add('input-error');
        err.textContent = message;
        err.style.display = 'block';
        
        // Attach listener to clear error on input
        input.addEventListener('input', function onInput() {
            clearError(inputId, errId);
            input.removeEventListener('input', onInput);
        });
    }

    function saveSettings() {
        const btn = document.getElementById('save-btn');
        const originalContent = btn.innerHTML;
        
        // Gather values
        const tmdb = document.getElementById('tmdb-key').value.trim();
        const imdb = document.getElementById('imdb-secret').value.trim();
        const name = document.getElementById('site-name').value.trim();
        const email = document.getElementById('support-email').value.trim();
        
        // Reset previous errors manually just in case
        clearError('tmdb-key', 'err-tmdb');
        clearError('imdb-secret', 'err-imdb');
        clearError('site-name', 'err-name');
        clearError('support-email', 'err-email');
        
        let isValid = true;
        
        // Validation Logic
        if(!tmdb || tmdb.length < 20) {
            showError('tmdb-key', 'err-tmdb', 'Valid TMDB API Key is required (min 20 chars).');
            isValid = false;
        }
        
        if(!imdb || imdb.length < 15) {
            showError('imdb-secret', 'err-imdb', 'Valid IMDb Secret is required.');
            isValid = false;
        }
        
        if(!name || name.length < 3) {
            showError('site-name', 'err-name', 'Site Name must be at least 3 characters long.');
            isValid = false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!email || !emailRegex.test(email)) {
            showError('support-email', 'err-email', 'Please enter a valid email address.');
            isValid = false;
        }
        
        if(!isValid) {
            showToast('Please fix the highlighted errors.', 'error');
            return;
        }

        // Loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';

        setTimeout(() => {
            // Update originalState to match new saved state
            originalState.tmdb = tmdb;
            originalState.imdb = imdb;
            originalState.name = name;
            originalState.email = email;
            originalState.maintenance = currentState.maintenance;
            
            // Restore btn
            btn.innerHTML = originalContent;
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            showToast('Configuration saved successfully!');
            
            if(originalState.maintenance) {
                setTimeout(() => showToast('Warning: Site is offline inside Maintenance Mode', 'info'), 500);
            }
        }, 800);
    }
</script>

<?php include 'footer.php'; ?>
