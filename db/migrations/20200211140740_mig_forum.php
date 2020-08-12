<?php

use Phinx\Migration\AbstractMigration;

class MigForum extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('forum')
             ->addColumn('band_id', 'integer', ['limit' => 11])
             ->addColumn('category', 'boolean', ['limit' => 1])
             ->addColumn('title', 'string', ['limit' => 40])
             ->addColumn('description', 'string', ['limit' => 256])
             ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
             ->addForeignKey('band_id', 'band', 'id')
             ->create();
    }
}
