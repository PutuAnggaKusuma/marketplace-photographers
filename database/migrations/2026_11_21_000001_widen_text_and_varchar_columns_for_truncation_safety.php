<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Testimonials: Widen deskripsi_review to TEXT to support detailed client reviews (up to 1,000 chars)
        if (Schema::hasTable('testimonials')) {
            DB::statement("ALTER TABLE `testimonials` MODIFY `deskripsi_review` TEXT NOT NULL");
        }

        // 2. Forum Comments: Widen comment to TEXT to support detailed replies (up to 1,000 chars)
        if (Schema::hasTable('forum_comments')) {
            DB::statement("ALTER TABLE `forum_comments` MODIFY `comment` TEXT NOT NULL");
        }

        // 3. Photographer Availability: Widen keterangan to TEXT to support detailed schedule block notes (up to 500 chars)
        if (Schema::hasTable('photographer_availability')) {
            DB::statement("ALTER TABLE `photographer_availability` MODIFY `keterangan` TEXT NULL");
        }

        // 4. Contest Submissions: Widen media_link to TEXT to support long cloud drive URLs (up to 500 chars)
        if (Schema::hasTable('contest_submissions')) {
            DB::statement("ALTER TABLE `contest_submissions` MODIFY `media_link` TEXT NULL");
        }

        // 5. Photo Contests: Widen judul_lomba and penyelenggara to VARCHAR(255)
        if (Schema::hasTable('photo_contests')) {
            DB::statement("ALTER TABLE `photo_contests` MODIFY `judul_lomba` VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE `photo_contests` MODIFY `penyelenggara` VARCHAR(255) NOT NULL");
        }

        // 6. Role Photographers: Widen link_sosmed to VARCHAR(255)
        if (Schema::hasTable('role_photographers')) {
            DB::statement("ALTER TABLE `role_photographers` MODIFY `link_sosmed` VARCHAR(255) NULL");
        }

        // 7. Users and Roles: Widen nama to VARCHAR(255)
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `users` MODIFY `nama` VARCHAR(255) NOT NULL");
        }
        if (Schema::hasTable('role_admins')) {
            DB::statement("ALTER TABLE `role_admins` MODIFY `nama` VARCHAR(255) NOT NULL");
        }
        if (Schema::hasTable('role_clients')) {
            DB::statement("ALTER TABLE `role_clients` MODIFY `nama` VARCHAR(255) NOT NULL");
        }
        if (Schema::hasTable('role_photographers')) {
            DB::statement("ALTER TABLE `role_photographers` MODIFY `nama` VARCHAR(255) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('testimonials')) {
            DB::statement("ALTER TABLE `testimonials` MODIFY `deskripsi_review` VARCHAR(255) NOT NULL");
        }
        if (Schema::hasTable('forum_comments')) {
            DB::statement("ALTER TABLE `forum_comments` MODIFY `comment` VARCHAR(255) NOT NULL");
        }
        if (Schema::hasTable('photographer_availability')) {
            DB::statement("ALTER TABLE `photographer_availability` MODIFY `keterangan` VARCHAR(255) NULL");
        }
        if (Schema::hasTable('contest_submissions')) {
            DB::statement("ALTER TABLE `contest_submissions` MODIFY `media_link` VARCHAR(255) NULL");
        }
        if (Schema::hasTable('photo_contests')) {
            DB::statement("ALTER TABLE `photo_contests` MODIFY `judul_lomba` VARCHAR(100) NOT NULL");
            DB::statement("ALTER TABLE `photo_contests` MODIFY `penyelenggara` VARCHAR(100) NOT NULL");
        }
        if (Schema::hasTable('role_photographers')) {
            DB::statement("ALTER TABLE `role_photographers` MODIFY `link_sosmed` VARCHAR(100) NULL");
        }
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `users` MODIFY `nama` VARCHAR(100) NOT NULL");
        }
        if (Schema::hasTable('role_admins')) {
            DB::statement("ALTER TABLE `role_admins` MODIFY `nama` VARCHAR(100) NOT NULL");
        }
        if (Schema::hasTable('role_clients')) {
            DB::statement("ALTER TABLE `role_clients` MODIFY `nama` VARCHAR(100) NOT NULL");
        }
        if (Schema::hasTable('role_photographers')) {
            DB::statement("ALTER TABLE `role_photographers` MODIFY `nama` VARCHAR(100) NOT NULL");
        }
    }
};
