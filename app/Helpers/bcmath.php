<?php
// Minimal stub to satisfy Composer autoload. Replace with the original helper
// implementations if available, or enable ext-bcmath if the app requires full bcmath.
if (!extension_loaded('bcmath')) {
    // Provide very small fallbacks only if absolutely needed. These are
    // approximate and may not behave exactly like PHP's bcmath extension.
    if (!function_exists('bcadd')) {
        function bcadd($left, $right, $scale = 0) {
            return number_format((float)$left + (float)$right, $scale, '.', '');
        }
    }
    if (!function_exists('bcsub')) {
        function bcsub($left, $right, $scale = 0) {
            return number_format((float)$left - (float)$right, $scale, '.', '');
        }
    }
    // Add other stubs only if your application depends on them.
}