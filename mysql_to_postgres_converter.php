<?php
/**
 * MySQL to PostgreSQL Converter
 * Converts MySQL dump file to PostgreSQL compatible format
 */

function convertMySQLToPostgreSQL($inputFile, $outputFile) {
    if (!file_exists($inputFile)) {
        die("Error: Input file '$inputFile' not found!\n");
    }
    
    $content = file_get_contents($inputFile);
    
    echo "Converting MySQL dump to PostgreSQL format...\n";
    
    // Remove MySQL specific comments and settings
    $content = preg_replace('/\/\*![0-9]+ .+? \*\/;?/s', '', $content);
    $content = preg_replace('/-- MySQL dump .+?\n/i', '', $content);
    $content = preg_replace('/-- Dump completed on .+?\n/i', '', $content);
    
    // Remove MySQL specific statements
    $content = preg_replace('/SET SQL_MODE = .+?;/i', '', $content);
    $content = preg_replace('/SET time_zone = .+?;/i', '', $content);
    $content = preg_replace('/SET foreign_key_checks = .+?;/i', '', $content);
    $content = preg_replace('/SET unique_checks = .+?;/i', '', $content);
    $content = preg_replace('/SET autocommit = .+?;/i', '', $content);
    
    // Convert data types
    $content = preg_replace('/\bint\((\d+)\)/i', 'INTEGER', $content);
    $content = preg_replace('/\bbigint\((\d+)\)/i', 'BIGINT', $content);
    $content = preg_replace('/\bsmallint\((\d+)\)/i', 'SMALLINT', $content);
    $content = preg_replace('/\btinyint\(1\)/i', 'BOOLEAN', $content);
    $content = preg_replace('/\btinyint\((\d+)\)/i', 'SMALLINT', $content);
    $content = preg_replace('/\bvarchar\((\d+)\)/i', 'VARCHAR($1)', $content);
    $content = preg_replace('/\btext\b/i', 'TEXT', $content);
    $content = preg_replace('/\blongtext\b/i', 'TEXT', $content);
    $content = preg_replace('/\bmediumtext\b/i', 'TEXT', $content);
    $content = preg_replace('/\bdatetime\b/i', 'TIMESTAMP', $content);
    $content = preg_replace('/\btimestamp\b/i', 'TIMESTAMP', $content);
    
    // Convert AUTO_INCREMENT to SERIAL
    $content = preg_replace('/\bAUTO_INCREMENT\b/i', '', $content);
    $content = preg_replace('/\bid\` int(?:\(\d+\))? (?:unsigned )?NOT NULL,/i', 'id SERIAL PRIMARY KEY,', $content);
    
    // Remove MySQL specific table options
    $content = preg_replace('/ENGINE=\w+/i', '', $content);
    $content = preg_replace('/DEFAULT CHARSET=\w+/i', '', $content);
    $content = preg_replace('/COLLATE=\w+/i', '', $content);
    $content = preg_replace('/AUTO_INCREMENT=\d+/i', '', $content);
    
    // Convert backticks to double quotes for identifiers
    $content = preg_replace('/`([^`]+)`/i', '"$1"', $content);
    
    // Convert MySQL specific functions
    $content = preg_replace('/NOW\(\)/i', 'CURRENT_TIMESTAMP', $content);
    
    // Fix CREATE TABLE syntax
    $content = preg_replace('/CREATE TABLE IF NOT EXISTS/i', 'CREATE TABLE IF NOT EXISTS', $content);
    
    // Remove duplicate PRIMARY KEY definitions (since SERIAL already creates one)
    $content = preg_replace('/,\s*PRIMARY KEY \("id"\)/i', '', $content);
    
    // Clean up extra commas and spaces
    $content = preg_replace('/,(\s*\))/', '$1', $content);
    $content = preg_replace('/\s+/', ' ', $content);
    $content = preg_replace('/;\s*;/', ';', $content);
    
    // Add PostgreSQL specific settings at the beginning
    $pgHeader = "-- Converted from MySQL to PostgreSQL\n";
    $pgHeader .= "SET client_encoding = 'UTF8';\n";
    $pgHeader .= "SET standard_conforming_strings = on;\n\n";
    
    $content = $pgHeader . $content;
    
    // Write converted content
    if (file_put_contents($outputFile, $content) !== false) {
        echo "✅ Conversion completed successfully!\n";
        echo "📁 Output file: $outputFile\n";
        echo "📊 File size: " . formatBytes(filesize($outputFile)) . "\n";
        return true;
    } else {
        echo "❌ Error writing output file!\n";
        return false;
    }
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

// Usage
$inputFile = 'fms_database_export.sql';  // Your MySQL export file
$outputFile = 'fms_database_postgresql.sql';  // PostgreSQL compatible file

echo "🔄 MySQL to PostgreSQL Converter\n";
echo "================================\n";
echo "Input file: $inputFile\n";
echo "Output file: $outputFile\n\n";

if (convertMySQLToPostgreSQL($inputFile, $outputFile)) {
    echo "\n✅ Ready for import to Aiven PostgreSQL!\n";
    echo "Next steps:\n";
    echo "1. Update your .env file with PostgreSQL settings\n";
    echo "2. Import the converted file to your Aiven database\n";
} else {
    echo "\n❌ Conversion failed. Please check the error messages above.\n";
}
?>
