<?php
// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'filmflix');

try {
    // 1. Initial connection without DB selected to ensure instance exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 2. Re-connect with DB selected
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true); // Allow multi-query for import

    // 3. Helper for robust SQL import
    function importSqlProperly($pdo, $sqlPath) {
        if (!file_exists($sqlPath)) return false;
        
        $sql = file_get_contents($sqlPath);
        if (empty(trim($sql))) return false;

        try {
            // Disable constraints temporarily for clean import
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            
            // Execute the entire SQL at once using multi-query support
            $pdo->exec($sql);
            
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return true;
        } catch (PDOException $e) {
            // If multi-query fails, fallback to line-by-line (slower but safer)
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $sql = preg_replace('/--.*?\n/m', '', $sql);
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($queries as $query) {
                if ($query) {
                    try { $pdo->exec($query); } catch (PDOException $ex) { /* skip individual errors if necessary */ }
                }
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return true;
        }
    }

    // 4. Force import if the database is essentially empty or missing data
    $tablesExist = $pdo->query("SHOW TABLES LIKE 'actors'")->rowCount() > 0;
    $hasData = $tablesExist ? $pdo->query("SELECT COUNT(*) FROM actors")->fetchColumn() > 0 : false;
             
    if (!$tablesExist || !$hasData) {
        importSqlProperly($pdo, __DIR__ . '/main.sql');
    }

} catch (PDOException $e) {
    die("<h3>Database Error</h3><p>Could not connect properly: " . $e->getMessage() . "</p>");
}
?>
