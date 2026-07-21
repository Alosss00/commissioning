<?php
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "OPcache successfully reset!";
    } else {
        echo "Failed to reset OPcache.";
    }
} else {
    echo "OPcache is not enabled or function opcache_reset() is disabled.";
}
