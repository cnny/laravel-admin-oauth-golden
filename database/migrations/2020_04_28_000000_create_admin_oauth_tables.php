<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminOauthTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users_third_pf_bind', function (Blueprint $table) {

            $table->increments('id');
            $table->string('platform', 50);
            $table->unsignedInteger('user_id');
            $table->string('third_user_id', 191);
            $table->timestamps();

            $table->unique(['platform', 'user_id', 'third_user_id']);

        });

        DB::table('admin_menu')->where('title', 'Dashboard')->update(['title' => '欢迎', 'icon' => 'fa-home']);
        DB::table('admin_menu')->where('title', 'Admin')->update(['title' => '技术运维', 'order' => 99]);
        DB::table('admin_roles')->where('id', 1)->update(['name' => '管理员']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users_third_pf_bind');
    }
}
