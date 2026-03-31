<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables if they exist (from previous migration)
        Schema::dropIfExists('seo_trends');
        Schema::dropIfExists('seo_performance');
        Schema::dropIfExists('service_reviews');
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('seo_keywords');

        // SEO Keywords Database with full tracking
        Schema::create('seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('keyword_ar')->nullable();
            $table->string('source')->default('gsc'); // gsc, trends, serp, manual
            $table->string('intent')->nullable(); // informational, transactional, navigational, commercial
            $table->string('cluster')->nullable(); // keyword cluster group
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('position', 8, 2)->default(0);
            $table->decimal('previous_position', 8, 2)->nullable();
            $table->integer('search_volume')->nullable();
            $table->decimal('competition', 5, 2)->nullable(); // 0-1 scale
            $table->decimal('cpc', 10, 2)->nullable();
            $table->integer('trend_score')->nullable(); // 0-100
            $table->boolean('is_rising')->default(false);
            $table->boolean('is_declining')->default(false);
            $table->boolean('is_opportunity')->default(false);
            $table->boolean('is_long_tail')->default(false);
            $table->string('status')->default('active'); // active, paused, archived
            $table->string('geo')->default('US');
            $table->json('related_keywords')->nullable();
            $table->json('serp_features')->nullable(); // featured_snippet, people_also_ask, etc.
            $table->date('first_seen')->nullable();
            $table->date('last_seen')->nullable();
            $table->timestamps();

            $table->index('source');
            $table->index('intent');
            $table->index('cluster');
            $table->index('is_opportunity');
            $table->index('geo');
        });

        // Keyword History for trend tracking
        Schema::create('seo_keyword_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained('seo_keywords')->onDelete('cascade');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('position', 8, 2)->default(0);
            $table->date('date');
            $table->timestamps();

            $table->index(['keyword_id', 'date']);
        });

        // SEO Pages - Track all indexed pages
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('h1')->nullable();
            $table->json('headings')->nullable(); // h2, h3 structure
            $table->string('canonical')->nullable();
            $table->string('index_status')->default('unknown'); // indexed, not_indexed, crawled_not_indexed, excluded
            $table->string('crawl_status')->nullable();
            $table->integer('word_count')->default(0);
            $table->boolean('is_thin_content')->default(false);
            $table->boolean('has_duplicate_title')->default(false);
            $table->boolean('has_duplicate_description')->default(false);
            $table->decimal('page_speed_mobile', 5, 2)->nullable();
            $table->decimal('page_speed_desktop', 5, 2)->nullable();
            $table->decimal('lcp', 8, 2)->nullable(); // Largest Contentful Paint
            $table->decimal('fid', 8, 2)->nullable(); // First Input Delay
            $table->decimal('cls', 8, 4)->nullable(); // Cumulative Layout Shift
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->json('structured_data_types')->nullable();
            $table->json('structured_data_errors')->nullable();
            $table->boolean('in_sitemap')->default(false);
            $table->date('last_crawled')->nullable();
            $table->date('last_indexed')->nullable();
            $table->timestamps();

            $table->unique('url');
            $table->index('index_status');
            $table->index('is_thin_content');
        });

        // Technical SEO Issues
        Schema::create('seo_issues', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 404, redirect_loop, canonical, structured_data, core_web_vitals, etc.
            $table->string('severity'); // critical, warning, info
            $table->string('url')->nullable();
            $table->string('title');
            $table->text('description');
            $table->json('details')->nullable();
            $table->string('status')->default('open'); // open, in_progress, resolved, ignored
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('severity');
        });

        // Structured Data Schemas
        Schema::create('seo_structured_data', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('type'); // Product, Article, FAQ, Breadcrumb, Organization, etc.
            $table->json('schema_data');
            $table->boolean('is_valid')->default(true);
            $table->json('errors')->nullable();
            $table->json('warnings')->nullable();
            $table->boolean('auto_generated')->default(false);
            $table->timestamps();

            $table->index('type');
        });

        // Content Ideas & Suggestions
        Schema::create('seo_content_ideas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_keyword');
            $table->json('related_keywords')->nullable();
            $table->string('content_type'); // blog, service_page, faq, landing_page
            $table->string('priority')->default('medium'); // high, medium, low
            $table->string('status')->default('pending'); // pending, approved, in_progress, published, rejected
            $table->integer('estimated_traffic')->nullable();
            $table->string('source')->default('trends'); // trends, gap_analysis, competitor, manual
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
        });

        // Competitor Analysis
        Schema::create('seo_competitors', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->string('name')->nullable();
            $table->json('common_keywords')->nullable();
            $table->json('unique_keywords')->nullable();
            $table->json('keyword_gaps')->nullable();
            $table->integer('estimated_traffic')->nullable();
            $table->integer('keyword_count')->nullable();
            $table->date('last_analyzed')->nullable();
            $table->timestamps();

            $table->unique('domain');
        });

        // SEO Actions Log
        Schema::create('seo_action_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action_type'); // meta_update, sitemap_update, reindex_request, schema_inject, etc.
            $table->string('url')->nullable();
            $table->text('description');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->text('error_message')->nullable();
            $table->string('triggered_by')->default('system'); // system, manual, schedule
            $table->timestamps();

            $table->index('action_type');
            $table->index('created_at');
        });

        // Daily SEO Reports
        Schema::create('seo_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // daily, weekly, monthly
            $table->date('report_date');
            $table->json('keywords_data')->nullable();
            $table->json('indexing_data')->nullable();
            $table->json('technical_issues')->nullable();
            $table->json('content_suggestions')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->json('performance_summary')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('report_date');
        });

        // Sitemap URLs tracking
        Schema::create('seo_sitemap_urls', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('changefreq')->default('weekly');
            $table->decimal('priority', 2, 1)->default(0.5);
            $table->timestamp('lastmod')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('pending'); // pending, submitted, indexed
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique('url');
        });

        // Reindex Queue
        Schema::create('seo_reindex_queue', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('type')->default('URL_UPDATED'); // URL_UPDATED, URL_DELETED
            $table->string('priority')->default('normal'); // high, normal, low
            $table->string('status')->default('pending'); // pending, submitted, completed, failed
            $table->text('response')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_reindex_queue');
        Schema::dropIfExists('seo_sitemap_urls');
        Schema::dropIfExists('seo_reports');
        Schema::dropIfExists('seo_action_logs');
        Schema::dropIfExists('seo_competitors');
        Schema::dropIfExists('seo_content_ideas');
        Schema::dropIfExists('seo_structured_data');
        Schema::dropIfExists('seo_issues');
        Schema::dropIfExists('seo_pages');
        Schema::dropIfExists('seo_keyword_history');
        Schema::dropIfExists('seo_keywords');
    }
};
