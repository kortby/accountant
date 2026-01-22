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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->string('featured_image_caption')->nullable();

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('structured_data')->nullable();

            // Social sharing
            $table->string('social_image')->nullable();
            $table->string('social_title')->nullable();
            $table->text('social_description')->nullable();

            // Publishing fields
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);

            // Analytics and tracking
            $table->integer('view_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('reading_time')->nullable();

            // Content organization
            $table->json('table_of_contents')->nullable();
            $table->string('template')->nullable();
            $table->json('custom_fields')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_related_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('related_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Post Tags pivot table
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('blog_tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
