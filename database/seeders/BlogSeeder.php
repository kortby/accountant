<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    public function run()
    {
        // Create an author user if not exists
        $author = User::firstOrCreate(
            ['email' => 'accountant@taxesaccountant.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => bcrypt('password'),
                'role' => 'accountant'
            ]
        );

        // Create Categories
        $categories = [
            [
                'name' => 'Tax Planning',
                'slug' => 'tax-planning',
                'description' => 'Strategic tax planning and optimization strategies',
            ],
            [
                'name' => 'Personal Finance',
                'slug' => 'personal-finance',
                'description' => 'Personal financial management and planning',
            ],
            [
                'name' => 'Business Accounting',
                'slug' => 'business-accounting',
                'description' => 'Business accounting principles and practices',
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Create Tags
        $tags = [
            ['name' => 'Tax Deductions', 'slug' => 'tax-deductions'],
            ['name' => 'Financial Planning', 'slug' => 'financial-planning'],
            ['name' => 'Small Business', 'slug' => 'small-business'],
            ['name' => 'Tax Season', 'slug' => 'tax-season'],
            ['name' => 'IRS Updates', 'slug' => 'irs-updates'],
            ['name' => 'Accounting Tips', 'slug' => 'accounting-tips'],
        ];

        foreach ($tags as $tag) {
            BlogTag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }

        // Blog Posts Data
        $posts = [
            [
                'title' => 'Top 10 Tax Deductions You Might Be Missing',
                'category' => 'tax-planning',
                'excerpt' => 'Discover commonly overlooked tax deductions that could save you money this tax season.',
                'content' => "Many taxpayers miss out on valuable deductions that could significantly reduce their tax liability. Here are the top 10 often-overlooked deductions:

1. Home Office Deductions
If you're self-employed and use part of your home regularly and exclusively for business, you may be eligible for home office deductions.

2. Professional Development Expenses
Costs related to maintaining or improving skills required in your current employment may be deductible.

3. Charitable Contributions
Don't forget about non-cash charitable contributions, including donated items and mileage for charitable work.

[Content continues...]",
                'tags' => ['tax-deductions', 'tax-season', 'accounting-tips'],
            ],
            [
                'title' => 'Essential Year-End Tax Planning Strategies',
                'category' => 'tax-planning',
                'excerpt' => 'Learn effective strategies to optimize your tax position before the year ends.',
                'content' => "Strategic tax planning before the year ends can significantly impact your tax liability. Here are essential strategies to consider:

1. Review Your Income and Deductions
Analyze your current tax situation and project your income for the rest of the year.

2. Maximize Retirement Contributions
Consider maximizing contributions to your 401(k) or IRA to reduce taxable income.

3. Harvest Tax Losses
Consider selling investments at a loss to offset capital gains.

[Content continues...]",
                'tags' => ['tax-planning', 'financial-planning', 'tax-season'],
            ],
            [
                'title' => 'Small Business Accounting: A Beginner\'s Guide',
                'category' => 'business-accounting',
                'excerpt' => 'Everything you need to know about managing your small business finances.',
                'content' => "Proper accounting is crucial for small business success. This guide covers the fundamentals:

1. Setting Up Your Accounting System
Choose between cash and accrual accounting methods and set up your chart of accounts.

2. Tracking Income and Expenses
Implement systems for recording all financial transactions accurately.

3. Managing Cash Flow
Understand and manage your cash flow to ensure business sustainability.

[Content continues...]",
                'tags' => ['small-business', 'accounting-tips', 'financial-planning'],
            ],
            [
                'title' => 'Understanding the Latest IRS Guidelines',
                'category' => 'tax-planning',
                'excerpt' => 'Stay informed about recent changes in tax regulations and IRS guidelines.',
                'content' => "The IRS regularly updates its guidelines and regulations. Here are the key changes you need to know:

1. New Tax Brackets
Understanding the adjusted tax brackets and how they affect your tax liability.

2. Standard Deduction Changes
Review the updated standard deduction amounts and determine if itemizing is beneficial.

3. Retirement Account Contribution Limits
New limits for 401(k), IRA, and other retirement accounts.

[Content continues...]",
                'tags' => ['irs-updates', 'tax-planning', 'tax-season'],
            ],
            [
                'title' => 'Personal Finance Management in Economic Uncertainty',
                'category' => 'personal-finance',
                'excerpt' => 'Strategies for managing personal finances during challenging economic times.',
                'content' => "Managing personal finances during economic uncertainty requires careful planning and strategy:

1. Emergency Fund Planning
Building and maintaining an adequate emergency fund for financial security.

2. Investment Diversification
Strategies for diversifying your investment portfolio to manage risk.

3. Debt Management
Approaches to managing and reducing debt effectively.

[Content continues...]",
                'tags' => ['financial-planning', 'personal-finance', 'accounting-tips'],
            ],
        ];

        // Create Posts
        foreach ($posts as $postData) {
            $category = BlogCategory::where('slug', $postData['category'])->first();
            $tags = BlogTag::whereIn('slug', $postData['tags'])->get();

            $post = BlogPost::create([
                'user_id' => 1,
                'category_id' => $category->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'is_featured' => rand(0, 1),
                'allow_comments' => true,
                'view_count' => rand(100, 1000),
                'reading_time' => rand(5, 15),
            ]);

            $post->tags()->attach($tags);

            // Add some comments to each post
            for ($i = 0; $i < rand(2, 5); $i++) {
                BlogComment::create([
                    'post_id' => $post->id,
                    'author_name' => fake()->name(),
                    'author_email' => fake()->email(),
                    'content' => fake()->paragraph(),
                    'is_approved' => true,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Create related posts relationships
        $allPosts = BlogPost::all();
        foreach ($allPosts as $post) {
            // Attach 2-3 related posts randomly
            $relatedPosts = $allPosts->except($post->id)->random(rand(2, 3));
            $post->relatedPosts()->attach($relatedPosts);
        }
    }
}
