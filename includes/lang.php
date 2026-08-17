<?php
// lang.php
// Purpose: Language bootstrapper + translation helper (t()).
// This file selects a language, loads the dictionary, and exposes t('key.path').

// Ensure session is available for storing language preference
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Supported language codes
$supportedLangs = ['en', 'el'];

// If ?lang= is provided and valid, store it in session
$langParam = $_GET['lang'] ?? null;
if ($langParam && in_array($langParam, $supportedLangs, true)) {
    $_SESSION['lang'] = $langParam;

    // Sync to database if user is logged in
    if (!empty($_SESSION['user_id'])) {
        try {
            // Use existing $pdo from config.php if available, otherwise skip DB update
            if (isset($GLOBALS['pdo'])) {
                $stmt = $GLOBALS['pdo']->prepare("UPDATE users SET lang = ? WHERE id = ?");
                $stmt->execute([$langParam, $_SESSION['user_id']]);
            }
        } catch (Exception $e) {
            // Silently fail - language change will still work in session
            error_log('Failed to sync language to database: ' . $e->getMessage());
        }
    }
}

// Read current language from session
$currentLang = $_SESSION['lang'] ?? null;

// Fallback: infer language if session is empty
// (legacy support: if URL contains /gr/, treat it as Greek)
if (!$currentLang) {
    $currentLang = str_contains($_SERVER['REQUEST_URI'] ?? '', '/gr/') ? 'el' : 'en';
    $_SESSION['lang'] = $currentLang;
}

// Load the language dictionary file
$langFile = PROJECT_ROOT . 'lang/' . $currentLang . '.php';
$translations = file_exists($langFile) ? require $langFile : [];

// Expose in globals for easy access
$GLOBALS['translations'] = $translations;
$GLOBALS['currentLang'] = $currentLang;

// Translation helper
// Usage: t('section.key') -> returns translated string
// If missing, returns $default or the key path itself
function t(string $key, string $default = ''): string
{
    $translations = $GLOBALS['translations'] ?? [];
    $value = $translations;

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default !== '' ? $default : $key;
        }
        $value = $value[$segment];
    }

    return is_string($value) ? $value : ($default !== '' ? $default : $key);
}
