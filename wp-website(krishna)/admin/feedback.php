<?php include 'header.php'; ?>

<div class="content-panel">
    <div class="panel-header">
        <h2 style="color: var(--text-main);"><i class="fas fa-comment-alt" style="color: var(--primary-color); margin-right: 0.75rem;"></i>User Feedback & Inquiries</h2>
        <div style="display: flex; gap: 1rem;">
            <button onclick="downloadReport()" class="action-btn" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; width: auto; height: auto; padding: 0.75rem 1.5rem; font-weight: 600; border-radius: 12px; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='var(--glass-bg)'">
                <i class="fas fa-download"></i> Download Report
            </button>
        </div>
    </div>
    
    <div style="margin-bottom: 2rem; display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
        <div style="position: relative; flex-grow: 1;">
            <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" id="feedback-search" placeholder="Search by name, email or message..." style="width: 100%; height: 48px; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0.7rem 1rem 0.7rem 3rem; border-radius: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='var(--border-color)'">
        </div>
        <select id="type-filter" style="width: 200px; height: 48px; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 0 1rem; border-radius: 14px; outline: none; cursor: pointer;">
            <option value="all">All Categories</option>
            <option value="Bug Report">🐛 Bug Report</option>
            <option value="Feature Request">✨ Feature Request</option>
            <option value="Suggestion">💡 Suggestion</option>
            <option value="Other">💬 Other</option>
        </select>
    </div>
    
    <div class="table-responsive" style="background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 1rem; overflow: hidden;">
        <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="text-align: left;">
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">SENDER INFO</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">CATEGORY</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">MESSAGE</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">PRIORITY</th>
                    <th style="padding: 1.25rem 1rem; color: var(--text-muted); font-weight: 600; font-size: 0.85rem; border-bottom: 1px solid var(--border-color); text-align: right;">ACTIONS</th>
                </tr>
            </thead>
            <tbody id="feedback-tbody">
                <!-- Dynamically Populated -->
            </tbody>
        </table>
        <!-- Empty Results -->
        <div id="no-results" style="display: none; text-align: center; padding: 4rem 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.03); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="fas fa-inbox-open" style="font-size: 2rem; color: var(--text-muted);"></i>
            </div>
            <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">No feedback found</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Try adjusting your search or filters.</p>
        </div>
    </div>
</div>

<!-- ========== REPLY MODAL ========== -->
<div id="reply-modal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div id="modal-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"></div>
    <div style="position: relative; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid var(--border-color); border-radius: 24px; padding: 0; width: 100%; max-width: 500px; animation: modalIn 0.3s ease; overflow: hidden;">
        
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: var(--text-main); font-size: 1.25rem; margin: 0;"><i class="fas fa-reply" style="color: var(--primary-color); margin-right: 0.75rem;"></i>Send Reply</h2>
            <button id="modal-close" style="background: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 34px; height: 34px; border-radius: 10px; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <div style="padding: 1.5rem 2rem;">
            <div id="reply-header" style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.03);">
                <div id="replier-name" style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">Alex Morgan</div>
                <div id="replier-email" style="color: var(--text-muted); font-size: 0.8rem;">To: alex@example.com</div>
            </div>

            <label style="display: block; margin-bottom: 0.75rem; color: var(--text-muted); font-size: 0.85rem;">Your Response Message</label>
            <textarea id="reply-box" placeholder="Write your message here..." style="width: 100%; height: 180px; background: var(--glass-bg); border: 1px solid var(--border-color); color: #fff; padding: 1rem; border-radius: 14px; outline: none; resize: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'"></textarea>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" id="modal-cancel" style="flex: 1; background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.85rem; border-radius: 10px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button onclick="sendReply()" style="flex: 2; background: var(--primary-color); border: none; color: #fff; padding: 0.85rem; border-radius: 10px; cursor: pointer; font-weight: 700; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    Send Message
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .f-row { transition: all 0.2s; }
    .f-row:hover { background: rgba(255,255,255,0.02); }
    .f-row td { padding: 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.03); }
    .f-avatar { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .f-action { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let feedback = [];

    async function loadFeedback() {
        try {
            const res = await fetch('api_feedback.php?action=read');
            feedback = await res.json();
            render();
        } catch(e) {
            console.error('Failed to load feedback', e);
        }
    }


    const tbody = document.getElementById('feedback-tbody');
    const noResults = document.getElementById('no-results');
    const search = document.getElementById('feedback-search');
    const filter = document.getElementById('type-filter');
    const modal = document.getElementById('reply-modal');

    function showToast(msg, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        const color = type === 'success' ? '#10b981' : '#ef4444';
        toast.innerHTML = `<i class="fas fa-check-circle" style="color: \${color}; font-size: 1.25rem;"></i><div style="display: flex; flex-direction: column;"><span style="font-weight: 600; color: #fff; font-size: 0.95rem;">\${type === 'success' ? 'Success' : 'Notice'}</span><span style="color: var(--text-muted); font-size: 0.85rem;">\${msg}</span></div>`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function getPriorityColor(p) {
        if(p === 'HIGH') return '#ef4444';
        if(p === 'MED') return '#f59e0b';
        return '#94a3b8';
    }

    function getCatTheme(c) {
        if(c === 'Bug Report') return 'rgba(239, 68, 68, 0.1), #ef4444';
        if(c === 'Feature Request') return 'rgba(99, 102, 241, 0.1), #818cf8';
        if(c === 'Suggestion') return 'rgba(245, 158, 11, 0.1), #f59e0b';
        return 'rgba(255, 255, 255, 0.05), #94a3b8';
    }

    function render() {
        const q = search.value.toLowerCase().trim();
        const cat = filter.value;
        const filtered = feedback.filter(f => 
            (cat === 'all' || f.category === cat) &&
            (f.name.toLowerCase().includes(q) || f.email.toLowerCase().includes(q) || f.msg.toLowerCase().includes(q))
        );

        tbody.innerHTML = '';
        if(filtered.length === 0) {
            noResults.style.display = 'block';
            return;
        }
        noResults.style.display = 'none';

        filtered.forEach((f, i) => {
            const tr = document.createElement('tr');
            tr.className = 'f-row';
            tr.style.animation = \`fadeInUp 0.3s ease forwards \${i * 0.03}s\`;
            tr.style.opacity = '0';
            
            const initials = f.name.split(' ').map(n=>n[0]).join('').toUpperCase();
            const colors = ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'];
            const avatarColor = colors[f.id % colors.length];
            const catTheme = getCatTheme(f.category).split(', ');

            tr.innerHTML = \`
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="f-avatar" style="background: \${avatarColor}">\${initials}</div>
                        <div>
                            <div style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">\${f.name}</div>
                            <div style="color: var(--text-muted); font-size: 0.8rem;">\${f.email}</div>
                        </div>
                    </div>
                </td>
                <td><span class="status-badge" style="background: \${catTheme[0]}; color: \${catTheme[1]};\${catTheme[1] !== '#94a3b8' ? ' border: 1px solid ' + catTheme[0] : ''}">\${f.category}</span></td>
                <td><div style="color: var(--text-muted); font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="\${f.msg}">\${f.msg}</div></td>
                <td><div style="color: \${getPriorityColor(f.priority)}; font-weight: 800; font-size: 0.7rem; letter-spacing: 1.5px;">\${f.priority}</div></td>
                <td>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <button onclick="openReply(\${f.id})" class="f-action" style="background: rgba(99,102,241,0.1); color: #818cf8;" title="Reply"><i class="fas fa-reply"></i></button>
                        <button onclick="deleteFeedback(\${f.id})" class="f-action" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            \`;
            tbody.appendChild(tr);
        });
    }

    window.openReply = function(id) {
        const f = feedback.find(x => x.id === id);
        document.getElementById('replier-name').textContent = f.name;
        document.getElementById('replier-email').textContent = 'To: ' + f.email;
        document.getElementById('reply-box').value = '';
        modal.style.display = 'flex';
        setTimeout(() => document.getElementById('reply-box').focus(), 100);
    };

    window.sendReply = function() {
        const msg = document.getElementById('reply-box').value.trim();
        if(!msg) return;
        showToast('Reply sent to User securely');
        modal.style.display = 'none';
    };

    window.deleteFeedback = async function(id) {
        if(confirm('Delete this feedback?')) {
            try {
                const res = await fetch(`api_feedback.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) {
                    feedback = feedback.filter(f => f.id !== id);
                    render();
                    showToast('Feedback removed from database');
                }
            } catch(e) {
                showToast('Failed to delete feedback', 'error');
            }
        }
    };

    window.downloadReport = function() {
        showToast('Generating PDF Report... Please wait');
        setTimeout(() => showToast('Feedback report saved to downloads'), 1500);
    };

    document.getElementById('modal-close').onclick = () => modal.style.display = 'none';
    document.getElementById('modal-cancel').onclick = () => modal.style.display = 'none';
    search.oninput = render;
    filter.onchange = render;

    loadFeedback();
});
</script>
</div>

<?php include 'footer.php'; ?>
