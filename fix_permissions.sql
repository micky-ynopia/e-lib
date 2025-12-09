-- Quick Fix SQL Script for MySQL Access Denied Error
-- Run this in phpMyAdmin: http://localhost/phpmyadmin
-- Click SQL tab, paste this, click Go

-- Step 1: Use mysql database
USE mysql;

-- Step 2: Fix root user - allow from all hosts
UPDATE user SET Host='%' WHERE User='root' AND Host='localhost';

-- Step 3: Create root user for 127.0.0.1 if it doesn't exist
INSERT IGNORE INTO user (Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv, Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv, Execute_priv, Repl_slave_priv, Repl_client_priv, Create_view_priv, Show_view_priv, Create_routine_priv, Alter_routine_priv, Create_user_priv, Event_priv, Trigger_priv) 
SELECT '%', 'root', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'
FROM user WHERE User='root' LIMIT 1;

-- Step 4: Flush privileges to apply changes
FLUSH PRIVILEGES;

-- Step 5: Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS elibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Success message
SELECT 'MySQL permissions fixed and database created!' AS Result;

