<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Asset Categories
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Manufacturers
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_url')->nullable();
            $table->timestamps();
        });

        // Asset Models
        Schema::create('asset_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('manufacturer_id')->constrained('manufacturers');
            $table->string('model_number')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_consumable')->default(false);
            $table->timestamps();
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Asset Statuses
        Schema::create('asset_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#cccccc');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Locations
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();
        });

        // Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Assets table
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('model_id')->constrained('asset_models');
            $table->foreignId('status_id')->constrained('asset_statuses');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('location_id')->constrained('locations');
            $table->decimal('purchase_cost', 10, 2);
            $table->date('purchase_date');
            $table->date('warranty_expiry')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->string('assigned_type')->nullable(); // 'user' or 'department'
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('floor')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Maintenance Records
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('details');
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('status'); // Scheduled, In Progress, Completed, Cancelled
            $table->timestamps();
        });

        // Tickets/Complaints
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('asset_id')->nullable()->constrained('assets');
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->string('priority'); // Low, Medium, High, Critical
            $table->string('status'); // Open, In Progress, On Hold, Resolved, Closed
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Ticket Comments
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment');
            $table->timestamps();
        });

        // Asset History
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('user_id')->constrained('users');
            $table->string('action'); // Checkout, Checkin, Update, Maintenance, etc.
            $table->text('details')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
        });

        // Consumables
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->integer('quantity');
            $table->integer('min_quantity')->default(0);
            $table->decimal('purchase_cost', 10, 2);
            $table->date('purchase_date');
            $table->timestamps();
        });

        // Consumable Assignments
        Schema::create('consumable_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained('consumables');
            $table->integer('quantity');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('floor')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumable_assignments');
        Schema::dropIfExists('consumables');
        Schema::dropIfExists('asset_histories');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('asset_statuses');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('asset_models');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('asset_categories');
    }
};
