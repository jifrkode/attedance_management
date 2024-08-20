<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/xxxx_xx_xx_add_two_factor_columns_to_users_table.php

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false);
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('two_factor_enabled');
        $table->dropColumn('two_factor_secret');
        $table->dropColumn('two_factor_recovery_codes');
    });
}
}
