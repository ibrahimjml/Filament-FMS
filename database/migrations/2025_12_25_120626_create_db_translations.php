<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('clients_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            $table->string('lang_code',5);
            $table->string('client_fname');
            $table->string('client_lname');
            $table->unique(['client_id','lang_code']);
            $table->timestamps();
        });
          Schema::create('client_type_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('type_id')->on('client_type')->onDelete('cascade');
            $table->string('lang_code',5);
            $table->string('type_name');
            $table->unique(['type_id','lang_code']);
            $table->timestamps();
        });
        Schema::create('categories_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->string('lang_code',2);
            $table->string('category_name');
            $table->unique(['category_id','lang_code']);
            $table->timestamps();
        });
          Schema::create('subcategories_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang_code',2);
            $table->unsignedBigInteger('subcategory_id');
            $table->foreign('subcategory_id')->references('subcategory_id')->on('subcategories')->onDelete('cascade');
            $table->string('sub_name');
            $table->unique(['subcategory_id','lang_code']);
            $table->timestamps();
        });
          Schema::create('income_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang_code',2);
            $table->unsignedBigInteger('income_id');
            $table->string('description');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('income_id')->references('income_id')->on('incomes')->onDelete('cascade');
            $table->unique(['income_id','lang_code']);
            $table->timestamps();
        });
          Schema::create('outcome_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang_code',2);
            $table->unsignedBigInteger('outcome_id');
            $table->string('description');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('outcome_id')->references('outcome_id')->on('outcomes')->onDelete('cascade');
            $table->unique(['outcome_id','lang_code']);
            $table->timestamps();
        });
          Schema::create('payment_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang_code',2);
            $table->unsignedBigInteger('payment_id');
            $table->string('description');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
            $table->unique(['payment_id','lang_code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_translations');
    }
};
