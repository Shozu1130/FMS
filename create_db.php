<?php
try {
    // Connect to MySQL without specifying database
    // Try different password combinations for Laragon
    $passwords = ['', 'root', 'password', '123456'];
    $pdo = null;
    
    foreach ($passwords as $password) {
        try {
            $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', $password);
            echo "Connected successfully with password: '" . ($password ?: 'empty') . "'\n";
            break;
        } catch (PDOException $e) {
            continue;
        }
    }
    
    if (!$pdo) {
        throw new Exception("Could not connect with any common passwords");
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec('CREATE DATABASE IF NOT EXISTS fms_database');
    echo "Database 'fms_database' created successfully\n";
    
    // Select the database
    $pdo->exec('USE fms_database');
    
    // Read and execute the SQL file
    $sql = file_get_contents('database/fms_mysql.sql');
    if ($sql === false) {
        throw new Exception("Could not read SQL file");
    }
    
    // Execute the SQL
    $pdo->exec($sql);
    echo "Database structure imported successfully\n";
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
