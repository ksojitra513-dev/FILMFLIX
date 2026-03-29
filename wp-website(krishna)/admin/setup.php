<?php
/**
 * Advanced Database Setup & Connectivity Utility
 * FilmFlix Admin Premium Suite
 */
require_once 'db.php';

$title = "Database Connectivity Suite";
$status = "ready";
$error = null;
$details = [];

// Handle Setup Request
if (isset($_POST['force_init'])) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $res = importSqlProperly($pdo, __DIR__ . '/main.sql');
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        if ($res) {
            $status = "success";
            $details[] = "Successfully synchronized " . DB_NAME . " with main.sql schema.";
        } else {
            throw new Exception("SQL import failed. Check main.sql permissions.");
        }
    } catch (Exception $e) {
        $status = "error";
        $error = $e->getMessage();
    }
}

// Check Current Health
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $table_count = count($tables);
    $user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $details[] = "Physical connection to MySQL server: Established.";
    $details[] = "Database '" . DB_NAME . "' exists and is accessible.";
    $details[] = "Current Schema contains $table_count tables.";
    $details[] = "User Registry check: $user_count active profiles identified.";
} catch (Exception $e) {
    if ($status !== 'error') $status = "warning";
    $error = $e->getMessage();
}

include 'header.php';
?>

<div class="content-panel" style="max-width: 800px; margin: 2rem auto; animation: fadeInUp 0.4s ease;">
    <div class="panel-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 2rem; margin-bottom: 2rem;">
        <div>
            <h2 style="color: var(--text-main); font-size: 1.75rem; margin-bottom: 0.5rem;"><i class="fas fa-database" style="color: var(--primary-color); margin-right: 0.75rem;"></i>Database Connectivity</h2>
            <p style="color: var(--text-muted);">Link your FilmFlix Admin dashboard with the core data warehouse.</p>
        </div>
        <div style="text-align: right;">
            <span class="status-badge" style="background: <?php echo $status === 'success' ? 'rgba(16,185,129,0.2)' : ($status === 'warning' ? 'rgba(245,158,11,0.2)' : 'rgba(239,68,68,0.2)'); ?>; color: <?php echo $status === 'success' ? '#10b981' : ($status === 'warning' ? '#f59e0b' : '#ef4444'); ?>; padding: 0.5rem 1rem; font-size: 0.8rem;">
                <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : ($status === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'); ?>"></i> <?php echo strtoupper($status); ?>
            </span>
        </div>
    </div>

    <!-- Connection Details -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
        <div style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem;">
            <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-network-wired" style="color: var(--accent);"></i> Connection Parameters
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">HOSTNAME</p>
                    <p style="color: #fff; font-weight: 600;"><?php echo DB_HOST; ?></p>
                </div>
                <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">DATABASE</p>
                    <p style="color: #fff; font-weight: 600;"><?php echo DB_NAME; ?></p>
                </div>
                <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">USER</p>
                    <p style="color: #fff; font-weight: 600;"><?php echo DB_USER; ?></p>
                </div>
                <div style="background: var(--glass-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">SOURCE PATH</p>
                    <p style="color: #fff; font-weight: 600; font-family: monospace;">admin/main.sql</p>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
        <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; color: #ef4444; margin-bottom: 0.5rem;">
                <i class="fas fa-exclamation-circle"></i>
                <h3 style="font-size: 1.1rem; margin: 0;">Synchronization Error</h3>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;"><?php echo $error; ?></p>
            <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.85rem;"><strong>Tip:</strong> Ensure MySQL is running on port 3306 and the username/password matches your local configuration.</p>
        </div>
        <?php endif; ?>

        <div style="background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.15); border-radius: 16px; padding: 1.5rem;">
            <h3 style="color: #10b981; font-size: 1.1rem; margin-bottom: 1rem;">Setup Checklist & Output</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach($details as $msg): ?>
                <li style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 0.8rem;"></i>
                    <?php echo $msg; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <form method="POST" style="margin-top: 1rem;">
            <button name="force_init" class="action-btn" style="width: 100%; height: auto; padding: 1.25rem; background: var(--primary-color); border: none; color: #fff; border-radius: 16px; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 10px 30px rgba(99,102,241,0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(99,102,241,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(99,102,241,0.3)';">
                <i class="fas fa-sync-alt"></i> Force Database Synchronization
            </button>
            <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 1rem;">Warning: This will reload the 'main.sql' schema into the active database.</p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
