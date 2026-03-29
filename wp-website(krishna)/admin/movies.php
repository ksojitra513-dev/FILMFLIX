<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);">Library Management</h2>
        <a href="add_movie.php" style="background: var(--primary-color); border: none; color: white; padding: 0.75rem 1.5rem; border-radius: 12px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i> Add New Content
        </a>
    </div>
    
    <!-- Stats Row -->
    <div id="library-stats" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <div onclick="filterByStat('all')" class="stat-card" style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.15); padding: 0.75rem 1.25rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-film" style="color: var(--primary-color);"></i>
            <div>
                <span id="total-count" style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: inline-block; transition: transform 0.2s;">0</span>
                <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 0.25rem;">Total</span>
            </div>
        </div>
        <div onclick="filterByStat('Published')" class="stat-card" style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.15); padding: 0.75rem 1.25rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <div>
                <span id="published-count" style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: inline-block; transition: transform 0.2s;">0</span>
                <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 0.25rem;">Published</span>
            </div>
        </div>
        <div onclick="filterByStat('Pending')" class="stat-card" style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.15); padding: 0.75rem 1.25rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-clock" style="color: #f59e0b;"></i>
            <div>
                <span id="pending-count" style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: inline-block; transition: transform 0.2s;">0</span>
                <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 0.25rem;">Pending</span>
            </div>
        </div>
        <div onclick="filterByStat('Draft')" class="stat-card" style="background: rgba(148,163,184,0.08); border: 1px solid rgba(148,163,184,0.15); padding: 0.75rem 1.25rem; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: all 0.2s ease;">
            <i class="fas fa-file-alt" style="color: #94a3b8;"></i>
            <div>
                <span id="draft-count" style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; display: inline-block; transition: transform 0.2s;">0</span>
                <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 0.25rem;">Draft</span>
            </div>
        </div>
        <style>
            .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
            .stat-card:active { transform: translateY(0); }
            @keyframes statPop { 0% { transform: scale(1); } 50% { transform: scale(1.3); color: var(--primary-color); } 100% { transform: scale(1); } }
            .stat-pop { animation: statPop 0.3s ease-out; }
        </style>
    </div>

    <!-- Search & Filter -->
    <div style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <div style="flex-grow: 1; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
            <input type="text" id="search-input" placeholder="Search by title, category, or year..." style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 0.7rem 0.7rem 2.5rem; border-radius: 10px; outline: none;">
        </div>
        <select id="type-filter" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; min-width: 140px;">
            <option value="all">All Types</option>
            <option value="Movie">Movies Only</option>
            <option value="TV Show">TV Shows Only</option>
            <option value="Documentary">Documentaries</option>
        </select>
        <select id="status-filter" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; min-width: 140px;">
            <option value="all">All Status</option>
            <option value="Published">Published</option>
            <option value="Pending">Pending</option>
            <option value="Draft">Draft</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="data-table" id="movies-table">
            <thead>
                <tr>
                    <th style="cursor: pointer;" id="sort-poster">Poster</th>
                    <th style="cursor: pointer;" id="sort-title">Title <i class="fas fa-sort" style="font-size: 0.7rem; margin-left: 0.3rem; opacity: 0.5;"></i></th>
                    <th>Category</th>
                    <th style="cursor: pointer;" id="sort-date">Year <i class="fas fa-sort" style="font-size: 0.7rem; margin-left: 0.3rem; opacity: 0.5;"></i></th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="movies-tbody">
                <!-- Populated dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="empty-state" style="display: none; text-align: center; padding: 4rem 2rem;">
        <i class="fas fa-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.25rem;"></i>
        <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">No results found</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Try adjusting your search or filter criteria.</p>
    </div>
</div>

<!-- ========== ADD/EDIT MODAL ========== -->
<div id="movie-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 620px; max-height: 90vh; overflow-y: auto; animation: modalIn 0.3s ease;">

        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.75rem 2rem; border-bottom: 1px solid var(--border-color); position: sticky; top: 0; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 24px 24px 0 0; z-index: 2;">
            <h2 id="modal-title" style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-plus-circle" style="color: var(--primary-color); margin-right: 0.5rem;"></i> Add New Content</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 36px; height: 36px; border-radius: 10px; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--text-muted)'" onmouseout="this.style.borderColor='var(--border-color)'"><i class="fas fa-times"></i></button>
        </div>

        <form id="movie-form" novalidate style="padding: 0;">
            <input type="hidden" id="form-movie-id">

            <!-- ====== Section 1: Basic Info ====== -->
            <div style="padding: 1.75rem 2rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(99,102,241,0.15); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 0.8rem;"></i>
                    </div>
                    <span style="color: var(--text-main); font-weight: 600; font-size: 0.9rem;">Basic Information</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.1rem;">
                    <!-- Title -->
                    <div class="form-group">
                        <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">
                            <span>Title <span style="color: var(--danger);">*</span></span>
                            <span id="title-counter" style="font-size: 0.72rem;">0 / 100</span>
                        </label>
                        <input type="text" id="form-title" maxlength="100" placeholder="e.g. Inception" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                        <p class="field-err" id="err-title" style="color: #ef4444; font-size: 0.73rem; margin-top: 0.3rem; display: none;"></p>
                    </div>

                    <!-- Type + Date -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">Type <span style="color: var(--danger);">*</span></label>
                            <select id="form-type" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                                <option value="">— Select —</option>
                                <option value="Movie">Movie</option>
                                <option value="TV Show">TV Show</option>
                                <option value="Documentary">Documentary</option>
                            </select>
                            <p class="field-err" id="err-type" style="color: #ef4444; font-size: 0.73rem; margin-top: 0.3rem; display: none;"></p>
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">Release Year <span style="color: var(--danger);">*</span></label>
                            <input type="number" id="form-year" min="1888" max="2030" placeholder="2026" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                            <p class="field-err" id="err-year" style="color: #ef4444; font-size: 0.73rem; margin-top: 0.3rem; display: none;"></p>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="form-group">
                        <label style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">
                            <span>Categories <span style="color: var(--danger);">*</span></span>
                            <span style="font-size: 0.7rem; color: var(--text-muted); opacity: 0.7;">Comma separated</span>
                        </label>
                        <input type="text" id="form-category" placeholder="e.g. Action, Sci-Fi, Thriller" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                        <div id="cat-preview" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.45rem; min-height: 0;"></div>
                        <p class="field-err" id="err-category" style="color: #ef4444; font-size: 0.73rem; margin-top: 0.3rem; display: none;"></p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div style="height: 1px; background: var(--border-color); margin: 0;"></div>

            <!-- ====== Section 2: Media & Metadata ====== -->
            <div style="padding: 1.75rem 2rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(245,158,11,0.12); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-photo-video" style="color: #f59e0b; font-size: 0.8rem;"></i>
                    </div>
                    <span style="color: var(--text-main); font-weight: 600; font-size: 0.9rem;">Media & Metadata</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.1rem;">
                    <!-- Poster URL -->
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">Poster Image URL</label>
                        <div style="display: flex; gap: 0.75rem; align-items: stretch;">
                            <div style="flex-grow: 1;">
                                <input type="url" id="form-poster" placeholder="https://image.tmdb.org/t/p/w500/..." style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                                <p class="field-err" id="err-poster" style="color: #ef4444; font-size: 0.73rem; margin-top: 0.3rem; display: none;"></p>
                            </div>
                            <div id="poster-thumb" style="width: 48px; height: 48px; border-radius: 10px; background: var(--glass-bg); border: 1px solid var(--border-color); flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <i id="poster-thumb-icon" class="fas fa-image" style="color: var(--text-muted); font-size: 0.85rem;"></i>
                                <img id="poster-thumb-img" src="" style="width: 100%; height: 100%; object-fit: cover; display: none; border-radius: 10px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div style="height: 1px; background: var(--border-color); margin: 0;"></div>

            <!-- ====== Section 3: Publishing ====== -->
            <div style="padding: 1.75rem 2rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(16,185,129,0.12); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-rocket" style="color: #10b981; font-size: 0.8rem;"></i>
                    </div>
                    <span style="color: var(--text-main); font-weight: 600; font-size: 0.9rem;">Publishing</span>
                </div>

                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.82rem;">Status</label>
                    <select id="form-status" style="width: 100%; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem; border-radius: 10px; outline: none; transition: border-color 0.2s ease; font-size: 0.9rem;">
                        <option value="Published">✅  Published</option>
                        <option value="Pending">⏳  Pending Approval</option>
                        <option value="Draft">📝  Draft</option>
                    </select>
                </div>

                <!-- Validation Summary -->
                <div id="validation-summary" style="display: none; background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); border-radius: 12px; padding: 0.85rem 1.1rem; margin-top: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;">
                        <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 0.8rem;"></i>
                        <span style="color: #ef4444; font-weight: 600; font-size: 0.82rem;">Please fix the following:</span>
                    </div>
                    <ul id="error-list" style="list-style: none; padding: 0; margin: 0; font-size: 0.78rem; color: var(--text-muted);"></ul>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div style="display: flex; gap: 1rem; padding: 1.25rem 2rem 1.75rem; border-top: 1px solid var(--border-color); background: rgba(15, 23, 42, 0.5); border-radius: 0 0 24px 24px;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.8rem; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;">Cancel</button>
                <button type="submit" id="modal-save" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.8rem; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                    <i class="fas fa-save"></i> <span id="modal-save-text">Save Content</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========== DELETE CONFIRM MODAL ========== -->
<div id="delete-modal" style="display: none; position: fixed; inset: 0; z-index: 1001; align-items: center; justify-content: center; padding: 1rem;">
    <div id="delete-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 2.5rem; width: 100%; max-width: 420px; text-align: center; animation: modalIn 0.3s ease;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(239,68,68,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i class="fas fa-trash-alt" style="font-size: 1.5rem; color: var(--danger);"></i>
        </div>
        <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">Delete Content?</h3>
        <p id="delete-msg" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">This action cannot be undone.</p>
        <div style="display: flex; gap: 1rem;">
            <button id="delete-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 600;">Cancel</button>
            <button id="delete-confirm" style="flex: 1; background: var(--danger); border: none; color: #fff; padding: 0.85rem; border-radius: 12px; cursor: pointer; font-weight: 700;"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>

<!-- ========== TOAST ========== -->
<div id="m-toast" style="position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.75rem; border-radius: 15px; font-weight: 600; font-size: 0.9rem; display: none; align-items: center; gap: 0.75rem; z-index: 9999; box-shadow: 0 10px 40px rgba(0,0,0,0.3); color: #fff;">
    <i id="m-toast-icon" class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
    <span id="m-toast-msg"></span>
</div>

<style>
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.92) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes rowIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
#movies-tbody tr {
    animation: rowIn 0.3s ease;
    transition: background 0.15s ease;
}
#movies-tbody tr:hover {
    background: rgba(99, 102, 241, 0.04);
}
.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.4rem;
    border-radius: 6px;
    transition: all 0.15s ease;
    font-size: 0.9rem;
}
.action-btn:hover {
    background: var(--glass-bg);
}
/* Validation styles */
.v-error input, .v-error select {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
.v-success input, .v-success select {
    border-color: #10b981 !important;
}
.v-error label span:first-child, .v-error > label:first-child {
    color: #ef4444 !important;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
.shake { animation: shake 0.4s ease; }
#modal-save:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ===== DATA STORE =====
    let movies = [];
    let sortField = null;
    let sortAsc = true;
    let deleteTargetId = null;

    async function loadMovies() {
        try {
            const res = await fetch('api_movies.php?action=read');
            movies = await res.json();
            render();
        } catch(e) {
            showToast('Failed to connect to database', true);
        }
    }

    // ===== ELEMENTS =====
    const tbody       = document.getElementById('movies-tbody');
    const searchInput = document.getElementById('search-input');
    const typeFilter  = document.getElementById('type-filter');
    const statusFilter = document.getElementById('status-filter');
    const emptyState  = document.getElementById('empty-state');
    const table       = document.getElementById('movies-table');

    const modal       = document.getElementById('movie-modal');
    const modalTitle  = document.getElementById('modal-title');
    const modalSaveT  = document.getElementById('modal-save-text');
    const modalClose  = document.getElementById('modal-close');
    const modalCancel = document.getElementById('modal-cancel');
    const modalOverlay = document.getElementById('modal-overlay');
    const movieForm   = document.getElementById('movie-form');
    const saveBtn     = document.getElementById('modal-save');

    const delModal    = document.getElementById('delete-modal');
    const delMsg      = document.getElementById('delete-msg');
    const delCancel   = document.getElementById('delete-cancel');
    const delConfirm  = document.getElementById('delete-confirm');
    const delOverlay  = document.getElementById('delete-overlay');

    // Form fields
    const fTitle    = document.getElementById('form-title');
    const fType     = document.getElementById('form-type');
    const fYear     = document.getElementById('form-year');
    const fCategory = document.getElementById('form-category');
    const fPoster   = document.getElementById('form-poster');
    const fStatus   = document.getElementById('form-status');

    // ==========================================
    //  VALIDATION ENGINE
    // ==========================================

    // Validation rules for each field
    const validationRules = {
        'form-title': {
            required: true,
            minLength: 2,
            maxLength: 100,
            label: 'Title',
            messages: {
                required: 'Title is required',
                minLength: 'Title must be at least 2 characters',
                maxLength: 'Title cannot exceed 100 characters',
            }
        },
        'form-type': {
            required: true,
            label: 'Type',
            messages: { required: 'Please select a content type' }
        },
        'form-year': {
            required: true,
            label: 'Release Year',
            custom: (val) => {
                const y = parseInt(val);
                if (isNaN(y)) return 'Please enter a valid year';
                if (y < 1888) return 'Movies didn\'t exist before 1888';
                if (y > new Date().getFullYear() + 5) return 'Year is too far in the future';
                return null;
            },
            messages: { required: 'Release year is required' }
        },
        'form-category': {
            required: true,
            label: 'Categories',
            custom: (val) => {
                if (!val) return null;
                const cats = val.split(',').map(c => c.trim()).filter(c => c);
                if (cats.length > 5) return 'Maximum 5 categories allowed';
                if (cats.some(c => c.length < 2)) return 'Each category must be at least 2 characters';
                return null;
            },
            messages: { required: 'At least one category is required' }
        },
        'form-poster': {
            required: false,
            label: 'Poster URL',
            custom: (val) => {
                if (!val) return null;
                try { new URL(val); } catch { return 'Please enter a valid URL (https://...)'; }
                if (!val.startsWith('http')) return 'URL must start with http:// or https://';
                return null;
            }
        }
    };

    // Validate a single field, returns error message or null
    function validateField(fieldId) {
        const el = document.getElementById(fieldId);
        const val = el.value.trim();
        const rules = validationRules[fieldId];
        if (!rules) return null;

        // Required check
        if (rules.required && !val) return rules.messages.required;

        // MinLength
        if (rules.minLength && val.length > 0 && val.length < rules.minLength) return rules.messages.minLength;

        // MaxLength
        if (rules.maxLength && val.length > rules.maxLength) return rules.messages.maxLength;

        // Custom validator
        if (rules.custom) {
            const err = rules.custom(val);
            if (err) return err;
        }

        return null;
    }

    // Show/hide inline error for a field
    function showFieldState(fieldId, error) {
        const errEl = document.getElementById('err-' + fieldId.replace('form-', ''));
        const group = document.getElementById(fieldId).closest('.form-group');
        if (!group) return;

        if (error) {
            group.classList.add('v-error');
            group.classList.remove('v-success');
            if (errEl) { errEl.textContent = error; errEl.style.display = 'block'; }
        } else {
            group.classList.remove('v-error');
            const val = document.getElementById(fieldId).value.trim();
            if (val) group.classList.add('v-success');
            else group.classList.remove('v-success');
            if (errEl) { errEl.style.display = 'none'; }
        }
    }

    // Validate all fields, returns { valid, errors[] }
    function validateAll() {
        const errors = [];
        Object.keys(validationRules).forEach(fieldId => {
            const err = validateField(fieldId);
            showFieldState(fieldId, err);
            if (err) errors.push({ field: validationRules[fieldId].label, message: err });
        });
        return { valid: errors.length === 0, errors };
    }

    // Show/hide validation summary
    function showValidationSummary(errors) {
        const summary = document.getElementById('validation-summary');
        const list = document.getElementById('error-list');
        if (errors.length === 0) {
            summary.style.display = 'none';
            return;
        }
        list.innerHTML = errors.map(e =>
            `<li style="padding: 0.2rem 0; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-circle" style="font-size: 0.3rem; color: #ef4444;"></i>
                <strong>${e.field}:</strong> ${e.message}
            </li>`
        ).join('');
        summary.style.display = 'block';
    }

    // Clear all validation states
    function clearValidation() {
        document.querySelectorAll('.form-group').forEach(g => {
            g.classList.remove('v-error', 'v-success');
        });
        document.querySelectorAll('.field-err').forEach(e => e.style.display = 'none');
        document.getElementById('validation-summary').style.display = 'none';
        document.getElementById('title-counter').textContent = '0 / 100';
        document.getElementById('title-counter').style.color = '';
        document.getElementById('cat-preview').innerHTML = '';
        document.getElementById('poster-thumb-img').style.display = 'none';
        document.getElementById('poster-thumb-icon').style.display = '';
    }

    // ==========================================
    //  REAL-TIME VALIDATION LISTENERS
    // ==========================================

    // Title: char counter + live validation on input
    fTitle.addEventListener('input', () => {
        const len = fTitle.value.length;
        const counter = document.getElementById('title-counter');
        counter.textContent = `${len} / 100`;
        counter.style.color = len > 90 ? '#ef4444' : len > 70 ? '#f59e0b' : '';

        if (fTitle.value.trim().length >= 2) {
            showFieldState('form-title', null);
        }
    });
    fTitle.addEventListener('blur', () => {
        const err = validateField('form-title');
        showFieldState('form-title', err);
    });

    // Type: validate on change
    fType.addEventListener('change', () => {
        const err = validateField('form-type');
        showFieldState('form-type', err);
    });

    // Date: validate on change + blur
    fDate.addEventListener('change', () => {
        const err = validateField('form-date');
        showFieldState('form-date', err);
    });

    // Category: live tag preview + validate on input
    fCategory.addEventListener('input', () => {
        const cats = fCategory.value.split(',').map(c => c.trim()).filter(c => c);
        const preview = document.getElementById('cat-preview');
        preview.innerHTML = cats.map(c =>
            `<span style="background: rgba(99,102,241,0.15); color: var(--primary-color); padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600;">${c}</span>`
        ).join('');
        if (cats.length > 0) showFieldState('form-category', validateField('form-category'));
    });
    fCategory.addEventListener('blur', () => {
        showFieldState('form-category', validateField('form-category'));
    });

    // Poster URL: live thumbnail preview
    let posterDebounce;
    fPoster.addEventListener('input', () => {
        clearTimeout(posterDebounce);
        posterDebounce = setTimeout(() => {
            const val = fPoster.value.trim();
            const thumb = document.getElementById('poster-thumb-img');
            const icon = document.getElementById('poster-thumb-icon');
            if (val) {
                const err = validateField('form-poster');
                showFieldState('form-poster', err);
                if (!err) {
                    thumb.src = val;
                    thumb.style.display = 'block';
                    icon.style.display = 'none';
                    thumb.onerror = () => {
                        thumb.style.display = 'none';
                        icon.style.display = '';
                        icon.className = 'fas fa-exclamation-triangle';
                        icon.style.color = '#f59e0b';
                    };
                }
            } else {
                thumb.style.display = 'none';
                icon.style.display = '';
                icon.className = 'fas fa-image';
                icon.style.color = '';
                showFieldState('form-poster', null);
            }
        }, 400);
    });
    fPoster.addEventListener('blur', () => {
        showFieldState('form-poster', validateField('form-poster'));
    });


    // ===== RENDER TABLE =====
    function render() {
        let filtered = [...movies];

        const q = searchInput.value.toLowerCase().trim();
        if (q) {
            filtered = filtered.filter(m =>
                m.title.toLowerCase().includes(q) ||
                m.category.toLowerCase().includes(q) ||
                m.year.toString().includes(q)
            );
        }

        const tf = typeFilter.value;
        if (tf !== 'all') filtered = filtered.filter(m => m.type === tf);

        const sf = statusFilter.value;
        if (sf !== 'all') filtered = filtered.filter(m => m.status === sf);

        if (sortField) {
            filtered.sort((a, b) => {
                let va = a[sortField === 'date' ? 'year' : sortField], vb = b[sortField === 'date' ? 'year' : sortField];
                if (sortField === 'date') { va = parseInt(va); vb = parseInt(vb); }
                if (va < vb) return sortAsc ? -1 : 1;
                if (va > vb) return sortAsc ? 1 : -1;
                return 0;
            });
        }

        tbody.innerHTML = '';
        if (filtered.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = '';
            emptyState.style.display = 'none';
            filtered.forEach((m, i) => {
                const tr = document.createElement('tr');
                tr.style.animationDelay = `${i * 0.04}s`;
                const posterSrc = (m.poster_url && !m.poster_url.startsWith('http')) ? 'uploads/' + m.poster_url : m.poster_url;
                tr.innerHTML = `
                    <td><img src="${posterSrc}" style="width: 42px; height: 58px; border-radius: 8px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/42x58/1e293b/6366f1?text=N/A'"></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">${m.title}</div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem;">${m.type}</div>
                    </td>
                    <td style="color: var(--text-muted);">${m.category}</td>
                    <td style="color: var(--text-muted); font-family: monospace;">${m.year}</td>
                    <td>${statusBadge(m.status, m.id)}</td>
                    <td style="white-space: nowrap;">
                        <button class="action-btn" onclick="editMovie(${m.id})" title="Edit" style="color: var(--primary-color);"><i class="fas fa-pen"></i></button>
                        <button class="action-btn" onclick="deleteMovie(${m.id})" title="Delete" style="color: var(--danger);"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        updateStats();
    }

    function statusBadge(status, id) {
        const colors = {
            Published: { bg: 'rgba(16,185,129,0.2)', color: '#10b981' },
            Pending:   { bg: 'rgba(245,158,11,0.2)', color: '#f59e0b' },
            Draft:     { bg: 'rgba(148,163,184,0.2)', color: '#94a3b8' },
        };
        const c = colors[status] || colors.Draft;
        return `<span class="status-badge" style="background: ${c.bg}; color: ${c.color}; cursor: pointer;" onclick="cycleStatus(${id})" title="Click to change status">${status.toUpperCase()}</span>`;
    }

    function updateStats() {
        const counts = {
            'total-count': movies.length,
            'published-count': movies.filter(m => m.status === 'Published').length,
            'pending-count': movies.filter(m => m.status === 'Pending').length,
            'draft-count': movies.filter(m => m.status === 'Draft').length
        };

        for (const [id, val] of Object.entries(counts)) {
            const el = document.getElementById(id);
            if (el && parseInt(el.textContent) !== val) {
                el.textContent = val;
                el.classList.remove('stat-pop');
                void el.offsetWidth; // trigger reflow
                el.classList.add('stat-pop');
            }
        }
    }

    // ===== CLICK TO FILTER =====
    window.filterByStat = function(status) {
        statusFilter.value = status;
        render();
        // Optional: flash the dropdown to show it changed
        statusFilter.style.borderColor = 'var(--primary-color)';
        setTimeout(() => statusFilter.style.borderColor = 'var(--border-color)', 500);
    };

    // ===== CYCLE STATUS =====
    window.cycleStatus = async function(id) {
        const m = movies.find(x => x.id === id);
        if (!m) return;
        
        try {
            const res = await fetch('api_movies.php?action=cycle_status', {
                method: 'POST',
                body: new URLSearchParams({ id: id })
            });
            const data = await res.json();
            if (data.success) {
                m.status = data.new_status;
                render();
                showToast(`"${m.title}" status → ${m.status}`);
            }
        } catch(e) {
            showToast('Failed to update status', true);
        }
    };

    // ===== SEARCH & FILTER =====
    searchInput.addEventListener('input', render);
    typeFilter.addEventListener('change', render);
    statusFilter.addEventListener('change', render);

    // ===== SORT =====
    document.getElementById('sort-title').addEventListener('click', () => toggleSort('title'));
    document.getElementById('sort-date').addEventListener('click', () => toggleSort('date'));

    function toggleSort(field) {
        if (sortField === field) sortAsc = !sortAsc;
        else { sortField = field; sortAsc = true; }
        render();
    }


    // ===== MODAL: OPEN EDIT =====
    window.editMovie = function(id) {
        const m = movies.find(x => x.id == id);
        if (!m) return;
        clearValidation();
        document.getElementById('form-movie-id').value = m.id;
        fTitle.value = m.title;
        fType.value = m.type;
        fDate.value = m.release_date;
        fCategory.value = m.category;
        fPoster.value = m.poster_url;
        fStatus.value = m.status;

        // Update char counter
        document.getElementById('title-counter').textContent = `${m.title.length} / 100`;

        // Update category preview
        const cats = m.category.split(',').map(c => c.trim()).filter(c => c);
        document.getElementById('cat-preview').innerHTML = cats.map(c =>
            `<span style="background: rgba(99,102,241,0.15); color: var(--primary-color); padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600;">${c}</span>`
        ).join('');

        // Update poster thumb
        if (m.poster_url) {
            const thumb = document.getElementById('poster-thumb-img');
            thumb.src = m.poster_url;
            thumb.style.display = 'block';
            document.getElementById('poster-thumb-icon').style.display = 'none';
        }

        modalTitle.innerHTML = '<i class="fas fa-pen" style="color: #f59e0b; margin-right: 0.5rem;"></i> Edit Content';
        modalSaveT.textContent = 'Update Content';
        openModal(modal);
    };

    // ===== MODAL: SAVE (with validation) =====
    movieForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Run full validation
        const { valid, errors } = validateAll();
        showValidationSummary(errors);

        if (!valid) {
            movieForm.classList.add('shake');
            setTimeout(() => movieForm.classList.remove('shake'), 500);
            showToast('Please fix the errors before saving.', true);
            return;
        }

        const editId = document.getElementById('form-movie-id').value;
        const payload = {
            id: editId,
            title: fTitle.value.trim(),
            type: fType.value,
            year: fYear.value,
            category: fCategory.value.trim(),
            poster_url: fPoster.value.trim() || 'https://via.placeholder.com/200x300/1e293b/6366f1?text=No+Poster',
            status: fStatus.value,
        };

        saveBtn.disabled = true;
        modalSaveT.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            const action = editId ? 'update' : 'create';
            const res = await fetch(`api_movies.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                showToast(editId ? `"${payload.title}" updated successfully!` : `"${payload.title}" added to library!`);
                closeModal(modal);
                loadMovies();
            } else {
                showToast(data.error || 'Failed to save', true);
            }
        } catch(e) {
            showToast('Network error while saving', true);
        } finally {
            saveBtn.disabled = false;
            modalSaveT.textContent = editId ? 'Update Content' : 'Save Content';
        }
    });

    // ===== DELETE =====
    window.deleteMovie = function(id) {
        const m = movies.find(x => x.id == id);
        if (!m) return;
        deleteTargetId = id;
        delMsg.innerHTML = `Are you sure you want to delete <strong style="color: var(--text-main);">"${m.title}"</strong>?`;
        openModal(delModal);
    };

    delConfirm.addEventListener('click', async () => {
        if (deleteTargetId !== null) {
            try {
                const res = await fetch(`api_movies.php?action=delete&id=${deleteTargetId}`);
                const data = await res.json();
                if (data.success) {
                    showToast(`Item removed from library.`, true);
                    closeModal(delModal);
                    loadMovies();
                } else {
                    showToast(data.error || 'Failed to delete', true);
                }
            } catch(e) {
                showToast('Network error while deleting', true);
            }
            deleteTargetId = null;
        }
    });

    // ===== MODAL HELPERS =====
    function openModal(el) { el.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    function closeModal(el) { el.style.display = 'none'; document.body.style.overflow = ''; }

    modalClose.addEventListener('click', () => { clearValidation(); closeModal(modal); });
    modalCancel.addEventListener('click', () => { clearValidation(); closeModal(modal); });
    modalOverlay.addEventListener('click', () => { clearValidation(); closeModal(modal); });
    delCancel.addEventListener('click', () => closeModal(delModal));
    delOverlay.addEventListener('click', () => closeModal(delModal));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            clearValidation();
            closeModal(modal);
            closeModal(delModal);
        }
    });

    // ===== TOAST =====
    function showToast(msg, isError = false) {
        const t = document.getElementById('m-toast');
        const icon = document.getElementById('m-toast-icon');
        document.getElementById('m-toast-msg').textContent = msg;
        t.style.background = isError ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #10b981, #059669)';
        icon.className = isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        t.style.display = 'flex';
        t.style.animation = 'none';
        void t.offsetWidth;
        t.style.animation = 'modalIn 0.3s ease';
        setTimeout(() => { t.style.display = 'none'; }, 3000);
    }

    // ===== INITIAL RENDER =====
    loadMovies();
});
</script>

<?php include 'footer.php'; ?>
