<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_entities', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name');
            // Multi-locale labels: swap the line above for:
            // $table->json('name');
            $table->timestamps();
        });

        Schema::create('permission_actions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 32)->unique();
            $table->string('name');
            // Multi-locale labels: swap the line above for:
            // $table->json('name');
            $table->timestamps();
        });

        Schema::create('permission_roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name');
            // Multi-locale labels: swap the line above for:
            // $table->json('name');
            $table->timestamps();
        });

        Schema::create('permission_role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_role_id')->constrained('permission_roles')->cascadeOnDelete();
            $table->primary(['user_id', 'permission_role_id']);
        });

        Schema::create('permission_role_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_role_id')->constrained('permission_roles')->cascadeOnDelete();
            $table->foreignId('permission_entity_id')->constrained('permission_entities')->cascadeOnDelete();
            $table->foreignId('permission_action_id')->constrained('permission_actions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['permission_role_id', 'permission_entity_id', 'permission_action_id'],
                'permission_role_grants_role_entity_action_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role_grants');
        Schema::dropIfExists('permission_role_user');
        Schema::dropIfExists('permission_roles');
        Schema::dropIfExists('permission_actions');
        Schema::dropIfExists('permission_entities');
    }
};
