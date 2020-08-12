<?php

/**
 * @author Andy Gerhard
 * @link https://invoku.net
 * @version 1.0.0
 * @license MIT
 */

namespace Models;

class Table implements TableInterface
{

    const WILDCARD = ['*'];

    public static function insert(string $table, array $values): int
    {
        $fields = implode('`,`', array_keys($values));
        $marks = self::createQuestionMarks(count($values));
        $db = Database::get();
        $stmt = $db->prepare("INSERT INTO `$table`(`$fields`) VALUES($marks)");
        try {
            $stmt->execute(array_values($values));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        return $stmt ? $stmt->rowCount() : 0;
    }

    public static function insertIgnore(string $table, array $values): int
    {
        $fields = implode('`, `', array_keys($values));
        $marks = self::createQuestionMarks(count($values));
        $db = Database::get();
        $stmt = $db->prepare("INSERT IGNORE INTO `$table`(`$fields`) VALUES($marks)");
        try {
            $stmt->execute(array_values($values));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        return $stmt ? $stmt->rowCount() : 0;

    }

    public static function update(string $table, array $values, array $conditions): int
    {
        $set = self::createSetClause($values);
        $where = self::createWhereClause($conditions);
        $db = Database::get();
        $stmt = $db->prepare("UPDATE `$table`".$set.$where);
        try {
        	$stmt->execute(array_merge(array_values($values), array_values($conditions)));
        } catch (\Exception $e) {
        	Log::write($e->getMessage());
        }
        return $stmt ? $stmt->rowCount() : 0;
    }

    public static function select(string $table, array $fields = Table::WILDCARD, array $conditions = [])
    {
        $fields = $fields[0] == '*' ? '*' : '`' .implode('`,`', $fields).'`';
        $where = self::createWhereClause($conditions);
        $db = Database::get();
        $stmt = $db->prepare("SELECT $fields FROM `$table`".$where." LIMIT 1");
        try {
        	$stmt->execute(array_values($conditions));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        return $stmt->fetch();
    }

    public static function selectColumn(string $table, string $field = 'id', array $conditions = [])
    {
        $where = self::createWhereClause($conditions);
        $db = Database::get();
        $stmt = $db->prepare("SELECT $field FROM `$table`".$where." LIMIT 1");
        try {
        	$stmt->execute(array_values($conditions));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        return $stmt->fetchColumn();
    }

    public static function selectAll(string $table, array $fields = Table::WILDCARD, array $conditions = []): array
    {
        $fields = $fields[0] == '*' ? '*' : '`' .implode('`,`', $fields).'`';
        $where = self::createWhereClause($conditions);
        $db = Database::get();
        $stmt = $db->prepare("SELECT $fields FROM `$table`".$where);
        try {
        	$stmt->execute(array_values($conditions));
        } catch (\Exception $e) {
        	Log::write($e->getMessage());
        }
        return $stmt->fetchAll();
    }

    public static function delete(string $table, array $conditions): int
    {
        $where = self::createWhereClause($conditions);
        $db = Database::get();
        $stmt = $db->prepare("DELETE FROM `$table`".$where);
        try {
        	$stmt->execute(array_values($conditions));
        } catch (\Exception $e) {
            Log::write($e->getMessage());
        }
        return $stmt ? $stmt->rowCount() : 0;
    }

    public static function truncate(string $table): int
    {
        $db = Database::get();
        $stmt = $db->prepare("TRUNCATE TABLE `$table`");
        return $stmt ? $stmt->rowCount() : 0;
    }

    public static function createSetClause(array $values): string
    {
        $result = '';
        if (count($values) > 0) {
            $result .= ' SET ';
            $set = [];
            foreach (array_keys($values) as $field) {
                $set[] = "`$field`=?";
            }
            $result .= implode(', ', $set);
        }
        return $result;
    }

    public static function createWhereClause(array $values): string
    {
        $result = '';
        if (count($values) > 0) {
            $result .= ' WHERE ';
            $where = [];
            foreach (array_keys($values) as $field) {
                $where[] = "`$field`=?";
            }
            $result .= implode(' AND ', $where);
        }
        return $result;
    }

    public static function createQuestionMarks(int $size): string
    {
        $values = [];
        for ($i = 0; $i < $size; $i++) {
            $values[]  = '?';
        }
        return implode(',', $values);
    }

}