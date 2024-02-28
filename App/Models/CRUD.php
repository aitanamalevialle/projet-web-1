<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class CRUD extends \Core\Model
{
    
    public static function getAll($table)
    {
        $db = static::getDB();
        $stmt = $db->query("SELECT * FROM `$table`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($table, $data)
    {
        $db = static::getDB();
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        return $db->lastInsertId();
    }

    public static function getById($table, $id)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM `$table` WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($table, $id)
    {
        $db = static::getDB();

        $stmt = $db->prepare("DELETE FROM `$table` WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true; 
        } else {
            return false; 
        }
    }

    public static function update($table, $data, $id)
    {
        $db = static::getDB();
    
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "`$key` = :$key, ";
        }
        $setClause = rtrim($setClause, ', ');
    
        $sql = "UPDATE `$table` SET $setClause WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        return $stmt->execute();
    }

    public static function getAllForUser($table, $utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM $table WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

?>