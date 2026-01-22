<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Dependent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            [
                'first_name' => 'Ahmed',
                'last_name' => 'B',
                'password' => Hash::make('Algeria@2025'),
                'phone' => '(310) 848-8598',
                'role' => 'admin',
                'email' => 'boutout.ea@gmail.com'
            ]
        );

        // // Create Accountants
        // $accountants = [
        //     [
        //         'first_name' => 'Sarah',
        //         'last_name' => 'Johnson',
        //         'email' => 'sarah.johnson@taxesaccountant.com',
        //         'phone' => '(555) 234-5678',
        //         'specialties' => 'Personal Tax, Small Business',
        //     ],
        //     [
        //         'first_name' => 'Michael',
        //         'last_name' => 'Chen',
        //         'email' => 'michael.chen@taxesaccountant.com',
        //         'phone' => '(555) 345-6789',
        //         'specialties' => 'Corporate Tax, International Tax',
        //     ],
        //     [
        //         'first_name' => 'Emily',
        //         'last_name' => 'Rodriguez',
        //         'email' => 'emily.rodriguez@taxesaccountant.com',
        //         'phone' => '(555) 456-7890',
        //         'specialties' => 'Estate Planning, Investment Tax',
        //     ],
        // ];

        // foreach ($accountants as $accountantData) {
        //     User::firstOrCreate(
        //         ['email' => $accountantData['email']],
        //         [
        //             'first_name' => $accountantData['first_name'],
        //             'last_name' => $accountantData['last_name'],
        //             'password' => Hash::make('password'),
        //             'phone' => $accountantData['phone'],
        //             'role' => 'accountant',
        //         ]
        //     );
        // }

        // Create Clients with Profiles and Dependents
        $clients = [
            [
                'user' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'john.smith@example.com',
                    'phone' => '(555) 567-8901',
                ],
                'profile' => [
                    'social_security_number' => '123-45-6789',
                    'date_of_birth' => '1985-06-15',
                    'occupation' => 'Software Engineer',
                    'marital_status' => 'married',
                    'address' => '123 Main St',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'zip_code' => '78701',
                    'has_dependents' => true,
                ],
                'dependents' => [
                    [
                        'first_name' => 'Emma',
                        'last_name' => 'Smith',
                        'date_of_birth' => '2015-03-20',
                        'relationship' => 'daughter',
                        'social_security_number' => '987-65-4321',
                    ],
                    [
                        'first_name' => 'Liam',
                        'last_name' => 'Smith',
                        'date_of_birth' => '2018-07-10',
                        'relationship' => 'son',
                        'social_security_number' => '987-65-4322',
                    ],
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Robert',
                    'last_name' => 'Johnson',
                    'email' => 'robert.johnson@example.com',
                    'phone' => '(555) 789-0123',
                ],
                'profile' => [
                    'social_security_number' => '345-67-8901',
                    'date_of_birth' => '1975-12-03',
                    'occupation' => 'Teacher',
                    'marital_status' => 'married',
                    'address' => '789 Pine St',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'zip_code' => '78703',
                    'has_dependents' => true,
                ],
                'dependents' => [
                    [
                        'first_name' => 'Sophia',
                        'last_name' => 'Johnson',
                        'date_of_birth' => '2010-05-15',
                        'relationship' => 'daughter',
                        'social_security_number' => '987-65-4323',
                    ],
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Linda',
                    'last_name' => 'Brown',
                    'email' => 'linda.brown@example.com',
                    'phone' => '(555) 901-2345',
                ],
                'profile' => [
                    'social_security_number' => '567-89-0123',
                    'date_of_birth' => '1982-11-15',
                    'occupation' => 'Doctor',
                    'marital_status' => 'married',
                    'address' => '654 Maple Dr',
                    'city' => 'Austin',
                    'state' => 'TX',
                    'zip_code' => '78705',
                    'has_dependents' => true,
                ],
                'dependents' => [
                    [
                        'first_name' => 'Oliver',
                        'last_name' => 'Brown',
                        'date_of_birth' => '2017-08-30',
                        'relationship' => 'son',
                        'social_security_number' => '987-65-4324',
                    ],
                    [
                        'first_name' => 'Ava',
                        'last_name' => 'Brown',
                        'date_of_birth' => '2019-04-12',
                        'relationship' => 'daughter',
                        'social_security_number' => '987-65-4325',
                    ],
                ],
            ],
        ];

        // Create client users with their profiles and dependents
        // foreach ($clients as $clientData) {
        //     $user = User::firstOrCreate(
        //         ['email' => $clientData['user']['email']],
        //         [
        //             'first_name' => $clientData['user']['first_name'],
        //             'last_name' => $clientData['user']['last_name'],
        //             'password' => Hash::make('password'),
        //             'phone' => $clientData['user']['phone'],
        //             'role' => 'client',
        //         ]
        //     );

        //     $profile = ClientProfile::firstOrCreate(
        //         ['user_id' => $user->id],
        //         $clientData['profile']
        //     );

        //     if (isset($clientData['dependents'])) {
        //         foreach ($clientData['dependents'] as $dependentData) {
        //             Dependent::firstOrCreate(
        //                 [
        //                     'client_profile_id' => $profile->id,
        //                     'social_security_number' => $dependentData['social_security_number']
        //                 ],
        //                 [
        //                     'first_name' => $dependentData['first_name'],
        //                     'last_name' => $dependentData['last_name'],
        //                     'date_of_birth' => $dependentData['date_of_birth'],
        //                     'relationship' => $dependentData['relationship'],
        //                 ]
        //             );
        //         }
        //     }
        // }
    }
}
