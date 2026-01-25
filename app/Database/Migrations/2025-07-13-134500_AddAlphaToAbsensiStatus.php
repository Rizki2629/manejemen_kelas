<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlphaToAbsensiStatus extends Migration
{
    public function up()
    {
        // Add 'alpha' to the status enum in absensi table
        $sql = "ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','izin','sakit','alpha') NOT NULL";
        $this->db->query($sql);
    }

    public function down()
    {
        // Remove 'alpha' from the status enum (rollback)
        $sql = "ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','izin','sakit') NOT NULL";
        $this->db->query($sql);
    }
}
