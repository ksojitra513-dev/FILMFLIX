<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-comments" style="color: var(--primary-color); margin-right: 0.75rem;"></i>Moderation Queue</h2>
        <div style="display: flex; gap: 1rem;" id="batch-actions">
            <button onclick="approveAll()" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 0.6rem 1.25rem; border-radius: 12px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='translateY(0)'">
                <i class="fas fa-check-double"></i> Approve All
            </button>
            <button onclick="clearQueue()" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.6rem 1.25rem; border-radius: 12px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.transform='translateY(0)'">
                <i class="fas fa-trash-alt"></i> Clear Queue
            </button>
        </div>
    </div>
    
    <!-- Empty State -->
    <div id="empty-state" style="display: none; text-align: center; padding: 5rem 2rem; background: var(--glass-bg); border-radius: 24px; border: 1px solid var(--border-color); margin-top: 2rem; animation: modalIn 0.4s ease;">
        <div style="width: 100px; height: 100px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i class="fas fa-clipboard-check" style="font-size: 3rem; color: #10b981;"></i>
        </div>
        <h3 style="color: var(--text-main); margin-bottom: 0.75rem; font-size: 1.5rem;">Inbox Zero!</h3>
        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto; line-height: 1.6;">Great job! All pending comments have been moderated. Check back later for new user feedback.</p>
    </div>

    <div id="comments-list" style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: 2rem;">
        <!-- Dynamically Populated -->
    </div>
</div>

<style>
    @keyframes slideOutLeft {
        to { opacity: 0; transform: translateX(-50px); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .comment-card {
        background: var(--glass-bg);
        padding: 1.75rem;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .comment-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        transform: scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .btn-approve {
        background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 0.6rem 1.25rem; border-radius: 10px; cursor: pointer; font-size: 0.85rem; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-approve:hover { background: #10b981; color: white; }
    
    .btn-delete {
        background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.6rem 1.25rem; border-radius: 10px; cursor: pointer; font-size: 0.85rem; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-delete:hover { background: #ef4444; color: white; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let comments = [];

    const list = document.getElementById('comments-list');
    const emptyState = document.getElementById('empty-state');
    const batchActions = document.getElementById('batch-actions');

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const isError = type === 'error';
        const color = isError ? '#ef4444' : '#10b981';
        const icon = isError ? 'fa-trash-alt' : 'fa-check-circle';
        
        toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; color: #fff; font-size: 0.95rem;">${isError ? 'Removed' : 'Approved'}</span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">${msg}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function renderDOM() {
        list.innerHTML = '';
        if (comments.length === 0) {
            list.style.display = 'none';
            batchActions.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        list.style.display = 'flex';
        batchActions.style.display = 'flex';
        emptyState.style.display = 'none';

        comments.forEach((c, i) => {
            const card = document.createElement('div');
            card.className = 'comment-card';
            card.style.animation = `fadeInUp 0.4s ease forwards ${i * 0.1}s`;
            card.style.opacity = '0';
            const isFlagged = c.flagged == 1 || c.flagged === true;
            card.style.borderLeft = isFlagged ? '4px solid #ef4444' : '4px solid var(--primary-color)';
            
            card.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 1.05rem; color: var(--text-main); line-height: 1.7; font-weight: 500; margin: 0; padding-bottom: 1.25rem;">"${c.text}"</p>
                        <div style="display: flex; align-items: center; gap: 1.25rem; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid rgba(255,255,255,0.05); pt: 1rem; margin-top: 0.5rem; padding-top: 1rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary-color); background: rgba(99,102,241,0.1); padding: 0.35rem 0.75rem; border-radius: 8px;"><i class="fas fa-user-circle"></i> ${c.user}</span>
                            <span style="display: flex; align-items: center; gap: 0.5rem;"><i class="fas fa-film" style="color: var(--text-muted);"></i> ${c.movie}</span>
                            <span style="display: flex; align-items: center; gap: 0.5rem;"><i class="far fa-clock"></i> ${c.time}</span>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.75rem; min-width: 140px;">
                        ${isFlagged ? `<span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.65rem; padding: 0.35rem 0.8rem; border-radius: 6px; font-weight: 900; letter-spacing: 1.5px; margin-bottom: 0.5rem; text-transform: uppercase;">Flagged</span>` : ''}
                        <div style="display: flex; gap: 0.6rem;">
                            <button onclick="moderate(${c.id}, 'approve', this)" class="btn-approve" title="Publish Review"><i class="fas fa-check"></i></button>
                            <button onclick="moderate(${c.id}, 'delete', this)" class="btn-delete" title="Delete Review"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
            `;
            list.appendChild(card);
        });
    }

    async function loadComments() {
        try {
            const res = await fetch('api_comments.php?action=read');
            comments = await res.json();
            renderDOM();
        } catch(e) {
            showToast('Failed to load queue from database', 'error');
        }
    }

    window.moderate = async function(id, action, btn) {
        const card = btn.closest('.comment-card');
        card.style.animation = 'slideOutLeft 0.4s ease forwards';
        
        try {
            const res = await fetch(`api_comments.php?action=${action}&id=${id}`);
            const data = await res.json();
            
            if(data.success) {
                setTimeout(() => {
                    const c = comments.find(x => x.id == id);
                    comments = comments.filter(x => x.id != id);
                    renderDOM();
                    showToast(action === 'approve' ? `Review by ${c.user} published.` : `Comment deleted from queue.`, action === 'approve' ? 'success' : 'error');
                }, 400);
            } else {
                card.style.animation = ''; // Revert if failed
                showToast(data.error || 'Action failed', 'error');
            }
        } catch (e) {
            card.style.animation = '';
            showToast('Network error', 'error');
        }
    };

    window.approveAll = async function() {
        if (confirm('Approve all pending comments in the queue?')) {
            try {
                const res = await fetch('api_comments.php?action=approve_all');
                const data = await res.json();
                if(data.success) {
                    const count = data.count || comments.length;
                    comments = [];
                    renderDOM();
                    showToast(`${count} comments approved and published.`);
                }
            } catch(e) {
                showToast('Network error', 'error');
            }
        }
    };

    window.clearQueue = async function() {
        if (confirm('Permanently delete all pending comments? This cannot be undone.')) {
            try {
                const res = await fetch('api_comments.php?action=clear_queue');
                const data = await res.json();
                if(data.success) {
                    const count = data.count || comments.length;
                    comments = [];
                    renderDOM();
                    showToast(`${count} comments removed from queue.`, 'error');
                }
            } catch(e) {
                showToast('Network error', 'error');
            }
        }
    };

    loadComments();
});
</script>

<?php include 'footer.php'; ?>
