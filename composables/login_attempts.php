<?php
function getLoginAttempts($key) {
    if (file_exists($key)) {
        $data = json_decode(file_get_contents($key), true);
        if (!is_array($data) || !isset($data['attempts']) || !isset($data['blocked_until'])) {
            $data = ['attempts' => [], 'blocked_until' => time() - 1];
        }
    } else {
        $data = ['attempts' => [], 'blocked_until' => time() - 1];
    }
    return $data;
}

function saveLoginAttempts($key, $data) {
    createDirectory(dirname($key));
    file_put_contents($key, json_encode($data), LOCK_EX);
}

function resetLoginAttempts($key) {
    if (file_exists($key)) {
        unlink($key);
    }
}

function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function isBlocked($key, $block_time, $attempts_limit, $time_window) {
    $data = getLoginAttempts($key);
    $time = time();

    // Clean up old attempts
    $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($time, $time_window) {
        return ($time - $timestamp) <= $time_window;
    });

    // Check if current time is within the block period
    if ($time < $data['blocked_until']) {
        return true;
    } else {
        // Checking if the login attempts exceed the threshold
        if (count($data['attempts']) >= $attempts_limit) {
            $data['blocked_until'] = $time + $block_time;
            saveLoginAttempts($key, $data);
            return true;
        }
    }
    return false;
}

function logAttempt($key) {
    $data = getLoginAttempts($key);
    $data['attempts'][] = time();
    saveLoginAttempts($key, $data);
}
?>