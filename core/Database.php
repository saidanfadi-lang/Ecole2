<?php
class DB {
    private static $pdo = null;
    public static function pdo() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Connexion à la base impossible : '.$e->getMessage().' (as-tu importé database/ecole.sql ?)');
            }
        }
        return self::$pdo;
    }
    public static function q($sql, $params = []) {
        $st = self::pdo()->prepare($sql);
        $st->execute($params);
        return $st;
    }
    public static function one($sql, $params = []) { return self::q($sql, $params)->fetch(); }
    public static function all($sql, $params = []) { return self::q($sql, $params)->fetchAll(); }
    public static function val($sql, $params = []) { return self::q($sql, $params)->fetchColumn(); }
    public static function id() { return self::pdo()->lastInsertId(); }
}
