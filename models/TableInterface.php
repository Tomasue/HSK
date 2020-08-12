<?php

namespace Models;

interface TableInterface {

   public static function insert(string $table, array $values): int;
   public static function insertIgnore(string $table, array $values): int;
   public static function update(string $table, array $values, array $conditions): int;
   public static function select(string $table, array $fields = Table::WILDCARD, array $conditions = []);
   public static function selectColumn(string $table, string $field = 'id', array $conditions =[]);
   public static function selectAll(string $table, array $fields = Table::WILDCARD, array $conditions = []): array;
   public static function delete(string $table, array $conditions): int;
   public static function truncate(string $table): int;
   public static function createSetClause(array $values): string;
   public static function createWhereClause(array $values): string;
   public static function createQuestionMarks(int $size): string;

}