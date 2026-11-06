<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function prefix()
    {
        return config('laravolt.indonesia.table_prefix', 'indonesia_');
    }

    public function up(): void
    {
        Schema::create($this->prefix() . 'districts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('code', 7)->unique();
            $table->char('city_code', 4);
            $table->string('name', 255);
            $table->text('meta')->nullable();
            $table->timestamps();

            $table->foreign('city_code')
                  ->references('code')
                  ->on($this->prefix() . 'cities')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix() . 'districts');
    }
};
