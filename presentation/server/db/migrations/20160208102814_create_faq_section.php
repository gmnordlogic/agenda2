<?php

use Phinx\Migration\AbstractMigration;

class CreateFaqSection extends AbstractMigration
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
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    /*public function change()
    {

    }*/

    /**
     * Migrate up
     */
    public function up()
    {

        $sql = "CREATE TABLE IF NOT EXISTS `faqs` (
                  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
                  `section` INT NULL DEFAULT 0 COMMENT '',
                  `question` VARCHAR(255) NULL COMMENT '',
                  `answer` TEXT NULL COMMENT '',
                  `order` INT NULL DEFAULT 0 COMMENT '',
                  `created_date` TIMESTAMP NULL COMMENT '',
                  `edited_date` TIMESTAMP NULL COMMENT '',
                  `editor_id` INT NULL COMMENT '',
                  PRIMARY KEY (`id`)  COMMENT '')
                ENGINE = InnoDB;";
        $c = $this->execute( $sql );
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $sql = 'DROP TABLE IF EXISTS `faqs`;';
        $c = $this->execute( $sql );
    }
}
