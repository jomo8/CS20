<?php

/**
 * Database handling
 */

namespace EasyReader;

use DateTimeImmutable;
use stdClass;
use mysqli;

class Database {

    private mysqli $db;
    private string $sqlDir;

    public function __construct() {
        $this->db = new mysqli(
            EASY_READER_DB_HOST,
            EASY_READER_DB_USER,
            EASY_READER_DB_PASS,
            EASY_READER_DB_NAME
        );
        $this->sqlDir = dirname( __DIR__ ) . '/sql/';
    }

    public function __destruct() {
        // Close the connection
        $this->db->close();
    }

    private function ensureTable( string $tableName, string $patchFile ) {
        $result = $this->db->query( "SHOW TABLES LIKE '$tableName';" );
        if ( $result->num_rows !== 0 ) {
            // Already created
            return;
        }
        $patchContents = file_get_contents( $this->sqlDir . $patchFile );
        $result = $this->db->query( $patchContents );
    }
    public function ensureDatabase() {
        $this->ensureTable( 'users', 'users-table.sql' );
        $this->ensureTable( 'text', 'text-table.sql' );
    }

    public function clearTables() {
        $this->db->query( 'DROP TABLE users' );
        $this->db->query( 'DROP TABLE text' );
        // On the next page view ensureDatabase() will recreate the tables
    }

    public static function setup() {
        // So that the constructor can select the database without errors when
        // it doesn't exist (on docker)
        $mysqli = new mysqli(
            EASY_READER_DB_HOST,
            EASY_READER_DB_USER,
            EASY_READER_DB_PASS
        );
        $mysqli->query(
            "CREATE DATABASE IF NOT EXISTS " . EASY_READER_DB_NAME
        );
        // close the connection
        $mysqli->close();
        $db = new Database;
        $db->ensureDatabase();
    }

    public function accountExists( string $email ): bool {
        $email = strtolower( $email );
        return $this->getAccount( $email ) !== null;
    }
    public function accountIsPremium( string $email ): bool {
        $email = strtolower( $email );
        $account = $this->getAccount( $email );
        return ( $account !== null && (bool)(int)$account->user_is_premium );
    }

    public function getAccount( string $email ): ?stdClass {
        $email = strtolower( $email );
        $query = $this->db->prepare(
            'SELECT user_id, user_pass_hash, user_is_premium ' .
            'FROM users WHERE user_email = ?'
        );
        $query->bind_param(
            's',
            ...[ $email ]
        );
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all( MYSQLI_ASSOC );
        if ( count( $rows ) === 0 ) {
            return null;
        }
        return (object)($rows[0]);
    }
    public function getAccountById( int $userId ): array {
        $query = $this->db->prepare(
            'SELECT user_email, user_is_premium FROM users WHERE user_id = ?'
        );
        $query->bind_param( 'd', ...[ $userId ] );
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all( MYSQLI_ASSOC );
        return $rows[0];
    }
    public function getAccountEmail( int $userId ): string {
        return (string)($this->getAccountById( $userId )['user_email']);
    }

    public function createAccount( string $email, string $passHash ): string {
        $email = strtolower( $email );
        $query = $this->db->prepare(
            'INSERT INTO users (user_email, user_pass_hash, user_is_premium) ' .
            'VALUES (?, ?, ?)'
        );
        $query->bind_param(
            'ssd',
            ...[ $email, $passHash, 0 ]
        );
        $query->execute();
        return (string)( $this->db->insert_id );
    }
    public function markUserPremium( string $email ): void {
        $email = strtolower( $email );
        $query = $this->db->prepare(
            'UPDATE users SET user_is_premium = ? WHERE user_email = ?'
        );
        $query->bind_param( 'ds', ...[ 1, $email ] );
        $query->execute();
    }
    public function isUserPremium( int $userId ): bool {
        return (bool)(int)($this->getAccountById( $userId )['user_is_premium']);
    }

    public function getCurrentUserText( int $userId ): ?string {
        $query = $this->db->prepare(
            'SELECT text_text FROM text WHERE text_user = ?'
        );
        $query->bind_param(
            'd',
            ...[ $userId ]
        );
        $query->execute();
        $result = $query->get_result();
        $rows = $result->fetch_all( MYSQLI_ASSOC );
        if ( count( $rows ) === 0 ) {
            return null;
        }
        return $rows[0]['text_text'];
    }

    public function setCurrentUserText( int $userId, string $text ): void {
        $currentText = $this->getCurrentUserText( $userId );
        if ( $currentText === null ) {
            // No current text
            $query = $this->db->prepare(
                'INSERT INTO text (text_user, text_text) VALUES (?, ?)'
            );
            $query->bind_param(
                'ds',
                ...[ $userId, $text ]
            );
        } else {
            // Have current text
            $query = $this->db->prepare(
                'UPDATE text SET text_text = ? WHERE text_user = ?'
            );
            $query->bind_param(
                'sd',
                ...[ $text, $userId ]
            );
        }
        $query->execute();
    }
}