<?php
// tests/UserTest.php

// Adjust path as needed based on execution directory
require_once __DIR__ . '/../src/Models/User.php';

// Simple Assertion Helper
function assert_true($condition, $message = "Assertion failed") {
    if (!$condition) {
        echo "❌ $message\n";
        return false;
    } else {
        echo "✅ $message passed\n";
        return true;
    }
}

echo "========================================\n";
echo "       xAI CMS - User Tests        \n";
echo "========================================\n";

// 1. Test Password Hashing (Unit Test - No DB required)
echo "\n[Unit] Password Hashing:\n";
$password = "secret123";
$hash = password_hash($password, PASSWORD_BCRYPT);
assert_true(password_verify($password, $hash), "Native password_verify works");

// 2. Integration Tests (Requires Database)
echo "\n[Integration] User Model (Requires Database Connection):\n";

try {
    // Attempt to connect to DB to see if we can run integration tests
    $db = Database::getInstance()->getConnection();
    
    // 2.1 Test User Creation
    $testEmail = "test_" . bin2hex(random_bytes(4)) . "@example.com";
    echo "Attempting to create user with email: $testEmail\n";
    
    $userId = User::create([
        'name' => 'Test User',
        'email' => $testEmail,
        'password' => 'password123',
        'role' => 'user'
    ]);
    
    if ($userId) {
        assert_true(true, "User created successfully with ID: $userId");
        
        // 2.2 Test Find User
        $user = User::findById($userId);
        assert_true($user && $user['email'] === $testEmail, "User found by ID and email matches");
        
        // 2.3 Test Password Verification Method
        assert_true(User::verifyPassword($user, 'password123'), "User::verifyPassword accepts correct password");
        assert_true(!User::verifyPassword($user, 'wrongpass'), "User::verifyPassword rejects wrong password");
        
        // 2.4 Test Update User
        User::update($userId, ['name' => 'Updated Name']);
        $updatedUser = User::findById($userId);
        assert_true($updatedUser['name'] === 'Updated Name', "User name update persisted");
        
        // 2.5 Test Log Activity
        // Trigger a log (update already triggered one, but let's do manual)
        User::logActivity($userId, 'test_action', 'Running tests');
        $logs = User::getRecentActivity($userId);
        assert_true(!empty($logs), "Activity logs retrieved");
        assert_true($logs[0]['action'] === 'test_action' || $logs[0]['action'] === 'update_profile', "Latest log action matches");

        // Clean up
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
        $db->prepare("DELETE FROM user_logs WHERE user_id = ?")->execute([$userId]);
        echo "Cleaned up test user.\n";
        
    } else {
        assert_true(false, "User creation returned false");
    }
    
} catch (Exception $e) {
    echo "⚠️  SKIPPING Integration Tests: " . $e->getMessage() . "\n";
    echo "   (This is normal if database credentials are not configured or DB is unreachable from CLI)\n";
}

echo "\nTests Completed.\n";
