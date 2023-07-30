<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployees extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => array(
                "type" => "INT",
                "constraint" => 5,
                "auto_increment" => true,
                "unsigned" => true
            ),
            "name" => array(
                "type" => "VARCHAR",
                "constraint" => 120,
                "null" => false,
            ),
            "email" => array(
                "type" => "VARCHAR",
                "constraint" => 120,
                "unique" => true,
                "null" => false
            ),
            "profile_image" => array(
                "type" => "VARCHAR",
                "constraint" => "250",
                "null" => true
            )
        ]);
        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("employees");
    }

    public function down()
    {
        $this->forge->dropTable("employees");
    }
}
