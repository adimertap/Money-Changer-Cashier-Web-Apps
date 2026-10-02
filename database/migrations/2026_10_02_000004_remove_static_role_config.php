<?php

use Illuminate\Database\Migrations\Migration;

class RemoveStaticRoleConfig extends Migration
{
    public function up()
    {
        // Roles are now persisted in the roles table; config remains only for migration defaults.
    }

    public function down()
    {
        // No schema change.
    }
}
