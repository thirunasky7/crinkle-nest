<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Key/value site settings (contact, social, theme, SEO, checkout rules ...)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('text');
            $table->string('group')->default('general');
            $table->string('label');
            $table->string('help')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Navigation links (header, footer columns, footer bottom bar)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('location')->index();
            $table->string('label');
            $table->text('url');
            $table->string('match')->nullable();
            $table->boolean('new_tab')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Editable content sections for every page (hero, banners, CTAs ...)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('page')->index();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('eyebrow')->nullable();
            $table->string('eyebrow_icon')->nullable();
            $table->text('title')->nullable();
            $table->string('title_highlight')->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('body')->nullable();
            $table->text('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('image2')->nullable();
            $table->text('image3')->nullable();
            $table->string('button_text')->nullable();
            $table->text('button_link')->nullable();
            $table->string('button2_text')->nullable();
            $table->text('button2_link')->nullable();
            $table->json('extra')->nullable();
            $table->string('items_label')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Repeatable items inside a section (chips, pillars, steps, cards ...)
        Schema::create('section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->text('link')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('eyebrow')->nullable();
            $table->string('eyebrow_icon')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('card_description')->nullable();
            $table->text('image')->nullable();
            $table->string('badge')->nullable();
            $table->string('price_label')->nullable();
            $table->text('link')->nullable();
            $table->boolean('show_on_home')->default(false);
            $table->boolean('show_in_collections')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->string('detail_line')->nullable();
            $table->text('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('price_label')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_style')->default('secondary');
            $table->string('tag')->nullable();
            $table->string('tag_note')->nullable();
            $table->string('image_tag')->nullable();
            $table->string('reco_note')->nullable();
            $table->string('icon')->nullable();
            $table->string('whatsapp_message')->nullable();
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_upsell')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // "Fresh From Our Oven" shorts / reels
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('thumbnail')->nullable();
            $table->text('video_file')->nullable();
            $table->text('video_url')->nullable();
            $table->string('duration')->nullable();
            $table->string('views_label')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('avatar')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // CMS pages: Terms, Privacy Policy, Refund Policy ...
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_highlight')->nullable();
            $table->string('status_chip')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('directory_label')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('eyebrow')->nullable();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('nav_title')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('inquiry_type')->nullable();
            $table->date('event_date')->nullable();
            $table->string('guests')->nullable();
            $table->text('message');
            $table->boolean('whatsapp_copy')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['contact_messages', 'page_sections', 'pages', 'faqs', 'testimonials', 'videos', 'products', 'categories', 'section_items', 'sections', 'menus', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
