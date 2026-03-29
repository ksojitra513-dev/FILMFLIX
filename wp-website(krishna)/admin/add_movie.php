<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-plus-circle" style="color: var(--primary-color); margin-right: 0.75rem;"></i> Add New Content</h2>
        <a href="movies.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Back to Library</a>
    </div>
    
    <!-- Progress Steps -->
    <div id="form-progress" style="display: flex; align-items: center; gap: 0.5rem; margin: 2rem 0 2.5rem; flex-wrap: wrap;">
        <div class="step-item active" data-step="1" style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-color); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; transition: all 0.3s ease;">1</div>
            <span style="font-size: 0.85rem; color: var(--text-main); font-weight: 500;">Basic Info</span>
        </div>
        <div style="flex-grow: 0; width: 40px; height: 2px; background: var(--border-color); border-radius: 2px;"></div>
        <div class="step-item" data-step="2" style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; transition: all 0.3s ease;">2</div>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Media & Meta</span>
        </div>
        <div style="flex-grow: 0; width: 40px; height: 2px; background: var(--border-color); border-radius: 2px;"></div>
        <div class="step-item" data-step="3" style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; transition: all 0.3s ease;">3</div>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Preview & Save</span>
        </div>
    </div>

    <form id="add-content-form" action="#" method="POST" class="add-movie-form" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; max-width: 1100px;">
        
        <!-- ============ STEP 1: Basic Info ============ -->
        <div id="step-1-left" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Movie/TV Show Title <span style="color: var(--danger);">*</span></label>
                <input type="text" id="movie-title" placeholder="e.g. Inception" required style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none; transition: border-color 0.2s ease;">
                <p id="title-error" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.4rem; display: none;"></p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Content Type</label>
                    <select id="content-type" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none;">
                        <option value="Movie">Movie</option>
                        <option value="TV Show">TV Show</option>
                        <option value="Documentary">Documentary</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Release Year</label>
                    <input type="number" id="release-year" placeholder="2026" min="1900" max="2030" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none;">
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Categories <span style="color: var(--danger);">*</span></label>
                <div id="category-tags-container" style="background: var(--glass-bg); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; display: flex; flex-wrap: wrap; gap: 0.5rem; min-height: 50px;">
                    <!-- Dynamically Loaded -->
                    <div class="loader-small" style="color: var(--text-muted); font-size: 0.8rem; padding: 0.5rem;"><i class="fas fa-circle-notch fa-spin"></i> Loading categories...</div>
                </div>
                <p id="cat-error" style="color: var(--danger); font-size: 0.75rem; margin-top: 0.4rem; display: none;">Select at least one category</p>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <label style="color: var(--text-muted); font-size: 0.9rem;">Full Description</label>
                    <span id="char-count" style="font-size: 0.75rem; color: var(--text-muted);">0 / 500</span>
                </div>
                <textarea id="movie-desc" rows="6" maxlength="500" placeholder="Write plot summary here..." style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 1rem 1.25rem; border-radius: 12px; outline: none; resize: none;"></textarea>
            </div>
        </div>
        
        <!-- ============ STEP 1: Right - Poster & Live Preview Card ============ -->
        <div id="step-1-right" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Upload Poster -->
            <div>
                <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Upload Poster (High Res)</label>
                <div id="drop-zone" style="border: 2px dashed var(--border-color); border-radius: 15px; padding: 3rem 1.5rem; text-align: center; background: rgba(30, 41, 59, 0.3); cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden;">
                    <input type="file" id="poster-input" accept="image/*" style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;">
                    <div id="drop-zone-content">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-main); font-size: 0.95rem;">Drag & drop poster image or <strong style="color: var(--primary-color);">browse</strong></p>
                        <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.5rem;">PNG, JPG up to 10MB (2:3 aspect ratio)</p>
                    </div>
                    <div id="poster-preview" style="display: none; position: relative;">
                        <img id="poster-img" src="" alt="Poster Preview" style="max-height: 220px; border-radius: 10px; object-fit: cover; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);">
                        <button type="button" id="remove-poster" style="position: absolute; top: -8px; right: -8px; background: var(--danger); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 0.8rem; z-index: 3; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.5);">
                            <i class="fas fa-times"></i>
                        </button>
                        <p id="file-name" style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.75rem;"></p>
                    </div>
                </div>
            </div>

            <!-- Live Preview Card -->
            <div>
                <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;"><i class="fas fa-eye" style="margin-right: 0.4rem;"></i> Live Preview</label>
                <div id="live-preview-card" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.9)); border: 1px solid var(--border-color); border-radius: 20px; padding: 1.5rem; display: flex; gap: 1.25rem; overflow: hidden; transition: all 0.3s ease;">
                    <div id="preview-poster-wrap" style="width: 90px; height: 130px; flex-shrink: 0; background: var(--glass-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color);">
                        <i id="preview-poster-placeholder" class="fas fa-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                        <img id="preview-poster-img" src="" style="width: 100%; height: 100%; object-fit: cover; display: none; border-radius: 12px;">
                    </div>
                    <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <span id="preview-type" class="status-badge" style="background: rgba(99,102,241,0.2); color: var(--primary-color); font-size: 0.65rem;">Movie</span>
                            <span id="preview-year" style="color: var(--text-muted); font-size: 0.75rem;"></span>
                        </div>
                        <h3 id="preview-title" style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 0.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Untitled Content</h3>
                        <p id="preview-desc" style="color: var(--text-muted); font-size: 0.75rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">No description yet...</p>
                        <div id="preview-cats" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.6rem;"></div>
                    </div>
                </div>
            </div>

            <!-- Trailer & Rating -->
            <div>
                <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Trailer URL (YouTube/Vimeo)</label>
                <input type="url" id="trailer-url" placeholder="https://www.youtube.com/watch?v=..." style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">IMDb Rating</label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="range" id="rating-slider" min="0" max="10" step="0.1" value="0" style="flex-grow: 1; accent-color: var(--primary-color);">
                        <span id="rating-value" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); min-width: 30px; text-align: right;">0</span>
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">Content Status</label>
                    <select id="content-status" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.8rem 1.25rem; border-radius: 12px; outline: none;">
                        <option value="Published">Published</option>
                        <option value="Pending" selected>Pending Approval</option>
                        <option value="Draft">Draft</option>
                    </select>
                </div>
            </div>
            
            <!-- Submit -->
            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                <button type="button" id="reset-form-btn" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 1rem; border-radius: 15px; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease;">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button type="submit" id="submit-btn" style="flex: 2; background: var(--primary-color); border: none; color: white; padding: 1rem; border-radius: 15px; cursor: pointer; font-weight: 700; font-size: 1.05rem; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); transition: all 0.2s ease; position: relative; overflow: hidden;">
                    <span id="submit-text">Save Content</span>
                    <span id="submit-loader" style="display: none;"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Toast Notification -->
<div id="toast" style="position: fixed; bottom: 2rem; right: 2rem; background: linear-gradient(135deg, #10b981, #059669); color: #fff; padding: 1rem 1.75rem; border-radius: 15px; font-weight: 600; font-size: 0.9rem; display: none; align-items: center; gap: 0.75rem; z-index: 9999; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4); animation: slideUp 0.4s ease;">
    <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
    <span id="toast-msg">Content saved successfully!</span>
</div>

<style>
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
    }
    .cat-tag.selected {
        background: rgba(99, 102, 241, 0.25) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
        font-weight: 600;
    }
    #drop-zone.drag-over {
        border-color: var(--primary-color) !important;
        background: rgba(99, 102, 241, 0.08) !important;
    }
    #submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45);
    }
    #reset-form-btn:hover {
        border-color: var(--text-muted);
        color: var(--text-main);
    }
    .field-error {
        border-color: var(--danger) !important;
    }
    .field-success {
        border-color: var(--accent) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== ELEMENTS =====
    const titleInput     = document.getElementById('movie-title');
    const typeSelect     = document.getElementById('content-type');
    const yearInput      = document.getElementById('release-year');
    const descInput      = document.getElementById('movie-desc');
    const charCount      = document.getElementById('char-count');
    const posterInput    = document.getElementById('poster-input');
    const dropZone       = document.getElementById('drop-zone');
    const dropContent    = document.getElementById('drop-zone-content');
    const posterPreview  = document.getElementById('poster-preview');
    const posterImg      = document.getElementById('poster-img');
    const removeBtn      = document.getElementById('remove-poster');
    const fileName       = document.getElementById('file-name');
    const ratingSlider   = document.getElementById('rating-slider');
    const ratingValue    = document.getElementById('rating-value');
    const trailerInput   = document.getElementById('trailer-url');
    const statusSelect   = document.getElementById('content-status');
    const form           = document.getElementById('add-content-form');
    const submitBtn      = document.getElementById('submit-btn');
    const submitText     = document.getElementById('submit-text');
    const submitLoader   = document.getElementById('submit-loader');
    const resetBtn       = document.getElementById('reset-form-btn');
    const toast          = document.getElementById('toast');
    const toastMsg       = document.getElementById('toast-msg');

    // Preview elements
    const prevTitle   = document.getElementById('preview-title');
    const prevDesc    = document.getElementById('preview-desc');
    const prevType    = document.getElementById('preview-type');
    const prevYear    = document.getElementById('preview-year');
    const prevCats    = document.getElementById('preview-cats');
    const prevPoster  = document.getElementById('preview-poster-img');
    const prevPosterPH = document.getElementById('preview-poster-placeholder');

    let selectedCategories = [];
    let posterFile = null;

    // Load Categories from Database
    async function fetchCategories() {
        const container = document.getElementById('category-tags-container');
        try {
            const res = await fetch('api_categories.php?action=read');
            const data = await res.json();
            
            container.innerHTML = '';
            if (!data || data.length === 0) {
                container.innerHTML = '<p style="font-size: 0.8rem; color: var(--text-muted);">No categories defined yet.</p>';
                return;
            }

            data.forEach(cat => {
                const tag = document.createElement('div');
                tag.className = 'cat-tag';
                tag.dataset.cat = cat.name;
                tag.style.cssText = `display: flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.08); padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; cursor: pointer; border: 1px solid transparent; transition: all 0.2s ease; user-select: none; color: var(--text-muted);`;
                tag.innerHTML = `${cat.icon ? `<i class="fas ${cat.icon}"></i> ` : ''}${cat.name}`;
                
                tag.addEventListener('click', () => {
                    tag.classList.toggle('selected');
                    if (tag.classList.contains('selected')) {
                        selectedCategories.push(cat.name);
                    } else {
                        selectedCategories = selectedCategories.filter(c => c !== cat.name);
                    }
                    updatePreviewCats();
                    if (selectedCategories.length > 0) {
                        document.getElementById('cat-error').style.display = 'none';
                    }
                    checkFields();
                });
                container.appendChild(tag);
            });
        } catch(e) {
            container.innerHTML = '<p style="color: var(--danger); font-size: 0.8rem;">Failed to load categories.</p>';
        }
    }
    fetchCategories();

    // ===== LIVE PREVIEW: Title =====
    titleInput.addEventListener('input', () => {
        prevTitle.textContent = titleInput.value.trim() || 'Untitled Content';
        if (titleInput.value.trim().length >= 2) {
            titleInput.classList.remove('field-error');
            titleInput.classList.add('field-success');
            document.getElementById('title-error').style.display = 'none';
        }
    });

    // ===== LIVE PREVIEW: Type =====
    typeSelect.addEventListener('change', () => {
        prevType.textContent = typeSelect.value;
    });

    // ===== LIVE PREVIEW: Year =====
    yearInput.addEventListener('input', () => {
        prevYear.textContent = yearInput.value ? `(${yearInput.value})` : '';
    });

    // ===== LIVE PREVIEW: Description + Char Count =====
    descInput.addEventListener('input', () => {
        const len = descInput.value.length;
        charCount.textContent = `${len} / 500`;
        charCount.style.color = len > 450 ? '#ef4444' : len > 350 ? '#f59e0b' : 'var(--text-muted)';
        prevDesc.textContent = descInput.value.trim() || 'No description yet...';
    });

    // ===== CATEGORY TAGS - Toggle =====
    catTags.forEach(tag => {
        tag.addEventListener('click', () => {
            const cat = tag.dataset.cat;
            tag.classList.toggle('selected');
            if (tag.classList.contains('selected')) {
                selectedCategories.push(cat);
            } else {
                selectedCategories = selectedCategories.filter(c => c !== cat);
            }
            updatePreviewCats();
            if (selectedCategories.length > 0) {
                document.getElementById('cat-error').style.display = 'none';
            }
        });
    });

    function updatePreviewCats() {
        prevCats.innerHTML = selectedCategories.map(c =>
            `<span style="background: rgba(99,102,241,0.15); color: var(--primary-color); padding: 0.15rem 0.5rem; border-radius: 5px; font-size: 0.65rem; font-weight: 600;">${c}</span>`
        ).join('');
    }

    // ===== DRAG & DROP + FILE SELECT =====
    ['dragenter', 'dragover'].forEach(e => {
        dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(e => {
        dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.remove('drag-over'); });
    });

    dropZone.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) handleFile(file);
    });

    posterInput.addEventListener('change', () => {
        if (posterInput.files[0]) handleFile(posterInput.files[0]);
    });

    function handleFile(file) {
        if (file.size > 10 * 1024 * 1024) {
            showToast('File too large! Max 10MB.', true);
            return;
        }
        posterFile = file; // Save for FormData
        const reader = new FileReader();
        reader.onload = (e) => {
            posterImg.src = e.target.result;
            dropContent.style.display = 'none';
            posterPreview.style.display = 'flex';
            posterPreview.style.flexDirection = 'column';
            posterPreview.style.alignItems = 'center';
            fileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;

            // Update live preview card
            prevPoster.src = e.target.result;
            prevPoster.style.display = 'block';
            prevPosterPH.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        posterFile = null;
        posterImg.src = '';
        posterInput.value = '';
        dropContent.style.display = 'block';
        posterPreview.style.display = 'none';
        prevPoster.style.display = 'none';
        prevPosterPH.style.display = 'block';
    });

    // ===== RATING SLIDER =====
    ratingSlider.addEventListener('input', () => {
        const val = parseFloat(ratingSlider.value).toFixed(1);
        ratingValue.textContent = val;
        if (val >= 7) ratingValue.style.color = '#10b981';
        else if (val >= 5) ratingValue.style.color = '#f59e0b';
        else if (val > 0) ratingValue.style.color = '#ef4444';
        else ratingValue.style.color = 'var(--text-main)';
    });

    // ===== FORM SUBMISSION =====
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let valid = true;

        // Validate title
        if (titleInput.value.trim().length < 2) {
            titleInput.classList.add('field-error');
            document.getElementById('title-error').textContent = 'Title must be at least 2 characters';
            document.getElementById('title-error').style.display = 'block';
            valid = false;
        }

        // Validate categories
        if (selectedCategories.length === 0) {
            document.getElementById('cat-error').style.display = 'block';
            valid = false;
        }

        if (!valid) {
            showToast('Please fix the highlighted errors.', true);
            return;
        }

        // Show loading
        submitText.style.display = 'none';
        submitLoader.style.display = 'inline';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';

        // Prepare FormData for multipart upload
        const formData = new FormData();
        formData.append('title', titleInput.value.trim());
        formData.append('category', selectedCategories.join(', '));
        formData.append('year', yearInput.value || new Date().getFullYear());
        formData.append('description', descInput.value.trim());
        formData.append('rating', ratingSlider.value);
        formData.append('trailer_url', trailerInput.value.trim());
        formData.append('status', statusSelect.value);
        formData.append('type', typeSelect.value);
        
        if (posterFile) {
            formData.append('poster', posterFile);
        }

        try {
            const response = await fetch('api_movies.php?action=create', {
                method: 'POST',
                body: formData // Sends as multipart/form-data
            });
            const result = await response.json();

            if (result.success) {
                showToast(`"${titleInput.value.trim()}" added to library successfully!`);
                updateProgressComplete();
                setTimeout(() => { window.location.href = 'movies.php'; }, 2000);
            } else {
                showToast(result.error || 'Server error', true);
            }
        } catch (err) {
            showToast('Failed to save content: ' + err.message, true);
        } finally {
            submitText.style.display = 'inline';
            submitLoader.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    });

    // ===== RESET FORM =====
    resetBtn.addEventListener('click', () => {
        form.reset();
        titleInput.classList.remove('field-error', 'field-success');
        prevTitle.textContent = 'Untitled Content';
        prevDesc.textContent = 'No description yet...';
        prevType.textContent = 'Movie';
        prevYear.textContent = '';
        prevCats.innerHTML = '';
        charCount.textContent = '0 / 500';
        charCount.style.color = 'var(--text-muted)';
        ratingValue.textContent = '0';
        ratingValue.style.color = 'var(--text-main)';
        selectedCategories = [];
        document.querySelectorAll('.cat-tag').forEach(t => t.classList.remove('selected'));
        removeBtn.click();
        document.getElementById('title-error').style.display = 'none';
        document.getElementById('cat-error').style.display = 'none';
        resetProgress();
        showToast('Form has been reset.', false);
    });

    // ===== TOAST =====
    function showToast(msg, isError = false) {
        toastMsg.textContent = msg;
        toast.style.display = 'flex';
        toast.style.background = isError
            ? 'linear-gradient(135deg, #ef4444, #dc2626)'
            : 'linear-gradient(135deg, #10b981, #059669)';
        toast.querySelector('i').className = isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        // Reset animation
        toast.style.animation = 'none';
        void toast.offsetWidth;
        toast.style.animation = 'slideUp 0.4s ease';

        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    }

    // ===== PROGRESS STEPPER =====
    function updateProgressComplete() {
        document.querySelectorAll('.step-item').forEach(s => {
            const circle = s.querySelector('.step-circle');
            circle.style.background = 'var(--accent)';
            circle.style.color = '#fff';
            circle.style.border = 'none';
            circle.innerHTML = '<i class="fas fa-check" style="font-size: 0.7rem;"></i>';
            s.querySelector('span').style.color = 'var(--accent)';
        });
    }

    function resetProgress() {
        const steps = document.querySelectorAll('.step-item');
        steps.forEach((s, i) => {
            const circle = s.querySelector('.step-circle');
            if (i === 0) {
                circle.style.background = 'var(--primary-color)';
                circle.style.color = '#fff';
                circle.style.border = 'none';
            } else {
                circle.style.background = 'var(--glass-bg)';
                circle.style.color = 'var(--text-muted)';
                circle.style.border = '1px solid var(--border-color)';
            }
            circle.textContent = i + 1;
            s.querySelector('span').style.color = i === 0 ? 'var(--text-main)' : 'var(--text-muted)';
        });
    }

    // ===== AUTO STEP TRACKING =====
    function checkFields() {
        const steps = document.querySelectorAll('.step-item');
        const step1 = titleInput.value.trim().length >= 2 && selectedCategories.length > 0;
        const step2 = posterImg.src && posterImg.src !== '' && ratingSlider.value > 0;

        // Step 1
        if (step1) {
            steps[0].querySelector('.step-circle').style.background = 'var(--accent)';
            steps[0].querySelector('.step-circle').innerHTML = '<i class="fas fa-check" style="font-size: 0.7rem;"></i>';
            steps[0].querySelector('span').style.color = 'var(--accent)';
            // Activate step 2
            steps[1].querySelector('.step-circle').style.background = 'var(--primary-color)';
            steps[1].querySelector('.step-circle').style.color = '#fff';
            steps[1].querySelector('.step-circle').style.border = 'none';
            steps[1].querySelector('span').style.color = 'var(--text-main)';
        }
        if (step1 && step2) {
            steps[1].querySelector('.step-circle').style.background = 'var(--accent)';
            steps[1].querySelector('.step-circle').innerHTML = '<i class="fas fa-check" style="font-size: 0.7rem;"></i>';
            steps[1].querySelector('span').style.color = 'var(--accent)';
            steps[2].querySelector('.step-circle').style.background = 'var(--primary-color)';
            steps[2].querySelector('.step-circle').style.color = '#fff';
            steps[2].querySelector('.step-circle').style.border = 'none';
            steps[2].querySelector('.step-circle').textContent = '3';
            steps[2].querySelector('span').style.color = 'var(--text-main)';
            submitBtn.style.animation = 'pulse 2s infinite';
        } else {
            submitBtn.style.animation = 'none';
        }
    }

    // Bind field checks
    [titleInput, yearInput, descInput, trailerInput].forEach(el => el.addEventListener('input', checkFields));
    [typeSelect, statusSelect].forEach(el => el.addEventListener('change', checkFields));
    ratingSlider.addEventListener('input', checkFields);
    posterInput.addEventListener('change', () => setTimeout(checkFields, 200));
});
</script>

<?php include 'footer.php'; ?>
