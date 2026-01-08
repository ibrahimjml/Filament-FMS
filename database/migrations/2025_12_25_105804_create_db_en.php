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
          Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id('invoice_setting_id');
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('company_address')->nullable();
            $table->string('logo')->nullable();
            $table->text('footer')->nullable();
            $table->json('extra')->nullable(); 
            $table->timestamps();
          });
          Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('invoice_setting_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('income_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('setting_snapshot');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('invoice_setting_id')->references('invoice_setting_id')->on('invoice_settings')->onDelete('cascade');
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('set null');
            $table->foreign('income_id')->references('income_id')->on('incomes')->onDelete('cascade');
          });    
          Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('income_id');
            $table->unsignedBigInteger('discount_id')->nullable(); 
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('payment_amount', 10, 2)->default(0.00);
            $table->string('status')->index();
            $table->boolean('is_priority')->default(false);
            $table->text('description')->nullable();
            $table->date('next_payment')->nullable()->index();
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
  
            $table->foreign('income_id')->references('income_id')->on('incomes')->onDelete('cascade');
            $table->foreign('discount_id')->references('discount_id')->on('discounts')->onDelete('set null'); 
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('set null');
  
            $table->index(['income_id','status']);
            $table->index('deleted_at');
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
        Schema::dropIfExists('invoice_settings');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('events');
    }
};
