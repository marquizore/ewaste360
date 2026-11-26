<?php
/**
 * PATRÓN DE DISEÑO SINGLETON:
 * Garantiza una única instancia de conexión PDO a MySQL.
 * Compatible con:
 *  - Google Cloud Run + Cloud SQL (DB_SOCKET=/cloudsql/PROYECTO:REGION:INSTANCIA)
 *  - Servidores externos TCP (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS)
 *  - XAMPP local (localhost / root / sin contraseña)
 */
class Database {
    private static ?PDO $conexion = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$conexion === null) {
            $socket = getenv('DB_SOCKET');
            $host   = getenv('DB_HOST') ?: 'localhost';
            $port   = getenv('DB_PORT') ?: '3306';
            $db     = getenv('DB_NAME') ?: 'ewaste360';
            $user   = getenv('DB_USER') ?: 'root';
            $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

            if (!empty($socket)) {
                $dsn = "mysql:unix_socket=$socket;dbname=$db;charset=utf8mb4";
            } else {
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            }

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$conexion = new PDO($dsn, $user, $pass, $options);
        }
        return self::$conexion;
    }
}
