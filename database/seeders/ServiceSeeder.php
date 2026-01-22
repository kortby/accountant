<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Personal Tax Return Preparation',
                'description' => 'Comprehensive personal tax return preparation service includes thorough review of all documents, identification of deductions and credits, and filing with federal and state authorities. Our experienced accountants ensure maximum refunds while maintaining full compliance with tax laws.',
                'short_description' => 'Professional preparation of individual tax returns',
                'base_price' => 299.00,
                'duration' => '1-2 weeks',
                'is_featured' => true,
                'is_emergency' => false,
                'category' => 'Tax Preparation',
                'included_services' => [
                    'Document review and organization',
                    'Deduction optimization',
                    'Tax credit identification',
                    'Federal and state filing',
                    'Basic tax planning advice',
                ],
            ],
            [
                'name' => 'Business Tax Preparation',
                'description' => 'Complete business tax return preparation service for small to medium businesses. Includes detailed analysis of business transactions, maximizing deductions, and ensuring compliance with business tax regulations. Our specialists handle complex business structures and multiple income streams.',
                'short_description' => 'Professional business tax return preparation',
                'base_price' => 599.00,
                'duration' => '2-3 weeks',
                'is_featured' => true,
                'is_emergency' => false,
                'category' => 'Tax Preparation',
                'included_services' => [
                    'Business transaction analysis',
                    'Deduction maximization',
                    'Financial statement review',
                    'Tax liability calculation',
                    'Quarterly estimate guidance',
                ],
            ],
            [
                'name' => 'Tax Planning Consultation',
                'description' => 'Strategic tax planning service to minimize future tax liabilities. Our experts analyze your financial situation and provide detailed recommendations for tax-efficient strategies. Includes long-term planning and quarterly reviews to ensure optimal tax positioning.',
                'short_description' => 'Strategic tax planning and optimization',
                'base_price' => 399.00,
                'duration' => '2-3 hours',
                'is_featured' => true,
                'is_emergency' => false,
                'category' => 'Tax Planning',
                'included_services' => [
                    'Financial situation analysis',
                    'Tax strategy development',
                    'Investment tax planning',
                    'Retirement account optimization',
                    'Future tax liability projections',
                ],
            ],
            [
                'name' => 'IRS Audit Representation',
                'description' => 'Professional representation during IRS audits. Our experienced accountants handle all communication with the IRS, prepare required documentation, and represent your interests throughout the audit process. Includes audit strategy development and response preparation.',
                'short_description' => 'Expert representation during IRS audits',
                'base_price' => 999.00,
                'duration' => 'Varies',
                'is_featured' => false,
                'is_emergency' => true,
                'category' => 'Tax Defense',
                'included_services' => [
                    'IRS communication management',
                    'Document preparation',
                    'Audit strategy development',
                    'In-person representation',
                    'Appeal assistance if needed',
                ],
            ],
            [
                'name' => 'Small Business Accounting',
                'description' => 'Monthly accounting service for small businesses includes bookkeeping, financial statement preparation, and basic tax planning. Our comprehensive service ensures accurate financial records and helps identify business optimization opportunities.',
                'short_description' => 'Monthly small business accounting services',
                'base_price' => 299.00,
                'duration' => 'Monthly',
                'is_featured' => true,
                'is_emergency' => false,
                'category' => 'Accounting',
                'included_services' => [
                    'Monthly bookkeeping',
                    'Financial statement preparation',
                    'Expense tracking',
                    'Basic tax planning',
                    'Monthly financial review',
                ],
            ],
            [
                'name' => 'Tax Resolution Services',
                'description' => 'Professional assistance with tax debt resolution, including negotiation with the IRS for payment plans, offers in compromise, and penalty abatement. Our experts work to find the best solution for your tax debt while protecting your interests.',
                'short_description' => 'Professional tax debt resolution assistance',
                'base_price' => 799.00,
                'duration' => '1-3 months',
                'is_featured' => false,
                'is_emergency' => true,
                'category' => 'Tax Defense',
                'included_services' => [
                    'Tax debt analysis',
                    'IRS negotiation',
                    'Payment plan setup',
                    'Penalty abatement requests',
                    'Resolution strategy development',
                ],
            ],
            [
                'name' => 'Estate Tax Planning',
                'description' => 'Comprehensive estate tax planning service to minimize estate tax liability and ensure efficient wealth transfer. Includes trust planning, gift tax strategies, and coordination with estate attorneys for complete estate planning.',
                'short_description' => 'Strategic estate tax planning services',
                'base_price' => 899.00,
                'duration' => '2-4 weeks',
                'is_featured' => false,
                'is_emergency' => false,
                'category' => 'Tax Planning',
                'included_services' => [
                    'Estate tax analysis',
                    'Trust planning strategies',
                    'Gift tax planning',
                    'Wealth transfer optimization',
                    'Estate tax projection',
                ],
            ]
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'short_description' => $service['short_description'],
                'base_price' => $service['base_price'],
                'duration' => $service['duration'],
                'is_featured' => $service['is_featured'],
                'is_emergency' => $service['is_emergency'],
                'is_active' => true,
                'category' => $service['category'],
                'included_services' => $service['included_services'],
                'display_order' => array_search($service, $services) + 1,
            ]);
        }
    }
}
