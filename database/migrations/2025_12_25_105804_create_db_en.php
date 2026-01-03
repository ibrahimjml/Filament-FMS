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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('client_id');
            $table->string('client_fname');
            $table->string('client_lname');
            $table->string('client_phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_fname','client_lname']);
            $table->index('deleted_at');
        });

          Schema::create('client_type', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('type_name')->index();
            $table->timestamps();
        });

        Schema::create('client_types_relation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('set null');
            $table->foreign('type_id')->references('type_id')->on('client_type')->onDelete('set null');
            
            $table->index(['client_id','type_id']);
          });

          Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name')->index();
            $table->string('category_type')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index('deleted_at');
        });

          Schema::create('subcategories', function (Blueprint $table) {
          $table->id('subcategory_id');
          $table->string('sub_name')->index();
          $table->unsignedBigInteger('category_id');
          $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
          $table->softDeletes();
          $table->timestamps();

          $table->index('deleted_at');
        });
        Schema::create('discounts', function (Blueprint $table) {
              $table->id('discount_id');
              $table->string('name')->index();
              $table->decimal('rate', 8, 2)->nullable(); 
              $table->decimal('fixed_amount', 10, 2)->nullable(); 
              $table->string('type')->index();
              $table->timestamps();
        });
          Schema::create('incomes', function (Blueprint $table) {
            $table->id('income_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('discount_id')->nullable(); 
            $table->unsignedBigInteger('client_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2);
            $table->string('status')->index();
            $table->string('payment_type')->index();
            $table->text('description')->nullable();
            $table->date('next_payment')->nullable()->index();
            $table->date('date')->nullable();
            $table->softDeletes();

            $table->foreign('discount_id')->references('discount_id')->on('discounts')->onDelete('cascade'); 
            $table->foreign('subcategory_id')->references('subcategory_id')->on('subcategories')->onDelete('cascade');
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('set null');
            $table->timestamps();

            $table->index(['client_id','status']);
            $table->index('deleted_at');
        });
        Schema::create('outcomes', function (Blueprint $table) {
          $table->id('outcome_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subcategory_id')->references('subcategory_id')->on('subcategories')->onDelete('cascade');
      
            $table->index(['subcategory_id','date']);
            $table->index('deleted_at');
          });
        Schema::create('payments', function (Blueprint $table) {
          $table->id('payment_id');
          $table->unsignedBigInteger('income_id');
          $table->unsignedBigInteger('discount_id')->nullable(); 
          $table->decimal('payment_amount', 10, 2)->default(0.00);
          $table->string('status')->index();
          $table->text('description')->nullable();
          $table->date('next_payment')->nullable()->index();
          $table->date('paid_at')->nullable();
          $table->timestamps();
          $table->softDeletes();

          $table->foreign('discount_id')->references('discount_id')->on('discounts')->onDelete('cascade'); 
          $table->foreign('income_id')->references('income_id')->on('incomes')->onDelete('cascade');
      
          $table->index(['income_id','status']);
          $table->index('deleted_at');
        });
          Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->unsignedBigInteger('income_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('income_id')->references('income_id')->on('incomes')->onDelete('cascade');
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('set null');
          });    
            Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name',255);
            $table->string('color');
            $table->string('bg_color');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_type');
        Schema::dropIfExists('client_types_relation');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('outcomes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('events');
    }
};
