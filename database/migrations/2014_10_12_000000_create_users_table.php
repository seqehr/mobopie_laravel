<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('bio')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('bg')->nullable();
            $table->string('region')->nullable();
            $table->string('gender')->nullable();
            $table->string('title')->nullable();
            $table->string('birthday')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('email')->unique();
            $table->string('img');
            $table->string('status');
            $table->string('lastseen')->nullable();
            $table->string('level')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('height')->nullable();
            $table->string('language')->nullable();
            $table->string('relationship')->nullable();
            $table->string('sex')->nullable();
            $table->string('religion')->nullable();
            $table->string('personality')->nullable();
            $table->string('education')->nullable();
            $table->string('work')->nullable();
            $table->boolean('page')->default(false);
            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
