<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\CompanyNote;
use App\Models\CompanyAddress;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample companies with specific data
        $companies = [
            [
                'name' => 'TechCorp Solutions',
                'industry' => 'Technology',
                'website' => 'https://techcorp-solutions.com',
                'email' => 'info@techcorp-solutions.com',
                'phone' => '+1-555-0123',
                'status' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'HealthFirst Medical',
                'industry' => 'Healthcare',
                'website' => 'https://healthfirst-medical.com',
                'email' => 'contact@healthfirst-medical.com',
                'phone' => '+1-555-0124',
                'status' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Global Finance Group',
                'industry' => 'Finance',
                'website' => 'https://globalfinance-group.com',
                'email' => 'info@globalfinance-group.com',
                'phone' => '+1-555-0125',
                'status' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'EduTech Innovations',
                'industry' => 'Education',
                'website' => 'https://edutech-innovations.com',
                'email' => 'hello@edutech-innovations.com',
                'phone' => '+1-555-0126',
                'status' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Manufacturing Plus',
                'industry' => 'Manufacturing',
                'website' => 'https://manufacturing-plus.com',
                'email' => 'sales@manufacturing-plus.com',
                'phone' => '+1-555-0127',
                'status' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }

        // Create additional random companies using factory
        Company::factory(15)->create();

        // Create related data for all companies
        $this->createRelatedData();
    }

    /**
     * Create related data for companies (addresses, contacts, notes)
     */
    private function createRelatedData(): void
    {
        Company::all()->each(function ($company) {
            // Create company addresses
            $this->createCompanyAddresses($company);

            // Create company contacts
            $this->createCompanyContacts($company);

            // Create company notes
            $this->createCompanyNotes($company);
        });
    }

    /**
     * Create addresses for a company
     */
    private function createCompanyAddresses(Company $company): void
    {
        $addressTypes = ['billing', 'shipping', 'office'];

        foreach ($addressTypes as $type) {
            CompanyAddress::create([
                'company_id' => $company->id,
                'type' => $type,
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'zip_code' => fake()->postcode(),
            ]);
        }
    }

    /**
     * Create contacts for a company
     */
    private function createCompanyContacts(Company $company): void
    {
        $contactCount = fake()->numberBetween(1, 3);

        for ($i = 0; $i < $contactCount; $i++) {
            Contact::create([
                'company_id' => $company->id,
                'name' => fake()->name(),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
                'designation' => fake()->jobTitle(),
                'is_primary' => $i === 0, // First contact is primary
            ]);
        }
    }

    /**
     * Create notes for a company
     */
    private function createCompanyNotes(Company $company): void
    {
        $noteCount = fake()->numberBetween(1, 2);

        for ($i = 0; $i < $noteCount; $i++) {
            CompanyNote::create([
                'company_id' => $company->id,
                'note' => fake()->paragraph(3),
                'created_by' => 1,
                'status' => true,
            ]);
        }
    }
}
