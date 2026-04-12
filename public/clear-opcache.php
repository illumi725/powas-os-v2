<?php
/**
 * OpCache Clearing Script
 * 
 * Use this to clear PHP OpCache on shared hosting like InfinityFree
 * IMPORTANT: DELETE THIS FILE after use for security!
 */

echo "<h2>Cache Clearing Script</h2>";

// Clear OpCache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✅ OPcache cleared successfully!<br>";
    } else {
        echo "❌ Failed to clear OPcache<br>";
    }
} else {
    echo "ℹ️ OPcache is not enabled on this server<br>";
}

// Clear realpath cache
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "✅ Realpath cache cleared!<br>";
}

// Try to clear APCu cache if available
if (function_exists('apcu_clear_cache')) {
    if (apcu_clear_cache()) {
        echo "✅ APCu cache cleared!<br>";
    }
}

echo "<br><strong>⚠️ IMPORTANT: DELETE THIS FILE IMMEDIATELY FOR SECURITY!</strong>";
?>
