<?php

namespace Database\Seeders;

use App\Models\FileGroup;
use App\Models\FileType;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupIds = FileGroup::query()
            ->pluck('id', 'slug')
            ->all();

        $scholarly = $groupIds['scholarly_documents'] ?? null;
        $admin = $groupIds['administrative_records'] ?? null;

        $types = [
            [
                'file_group_id' => $scholarly,
                'name' => 'Scholarship Agreement',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Amendatory Agreement',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Information Sheet',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Prospectus',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Certificate of Registration',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                    ['field_name' => 'semester', 'datatype' => 'enum', 'values' => ['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester'], 'label' => 'Semester'],
                    ['field_name' => 'year', 'datatype' => 'int', 'label' => 'Year'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Certificate of Grades',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                    ['field_name' => 'semester', 'datatype' => 'enum', 'values' => ['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester'], 'label' => 'Semester'],
                    ['field_name' => 'year', 'datatype' => 'int', 'label' => 'Year'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'PTP Documents',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Transcript of Records',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Graduate Data File',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Leave of Absence (LOA)',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Shifting or Transfer',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $scholarly,
                'name' => 'Clearance',
                'metadata_template' => [
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Memorandums',
                'metadata_template' => [
                    ['field_name' => 'series_number', 'datatype' => 'string', 'label' => 'Series Number'],
                    ['field_name' => 'subject', 'datatype' => 'string', 'label' => 'Subject'],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Annual Financial Reports',
                'metadata_template' => [
                    ['field_name' => 'report_number', 'datatype' => 'string', 'label' => 'Report Number'],
                    ['field_name' => 'project', 'datatype' => 'string', 'label' => 'Project'],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Report Date'],
                    ['field_name' => 'start_date', 'datatype' => 'date', 'label' => 'Start Date'],
                    ['field_name' => 'end_date', 'datatype' => 'date', 'label' => 'End Date'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Quarterly Financial Reports',
                'metadata_template' => [
                    ['field_name' => 'project', 'datatype' => 'string', 'label' => 'Project'],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Report Date'],
                    ['field_name' => 'start_date', 'datatype' => 'date', 'label' => 'Start Date'],
                    ['field_name' => 'end_date', 'datatype' => 'date', 'label' => 'End Date'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Payrolls',
                'metadata_template' => [
                    ['field_name' => 'payroll_number', 'datatype' => 'string', 'label' => 'Payroll Number'],
                    ['field_name' => 'cheque_number', 'datatype' => 'string', 'label' => 'Cheque Number'],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Endorsements',
                'metadata_template' => [
                    ['field_name' => 'academic_year', 'datatype' => 'text', 'label' => 'Academic Year'],
                    ['field_name' => 'semester', 'datatype' => 'enum', 'label' => 'Semester', 'values' => ['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester']],
                    ['field_name' => 'school_id', 'datatype' => 'int', 'label' => 'School', 'foreign_key' => 'schools'],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                ],
            ],
            [
                'file_group_id' => $admin,
                'name' => 'Communications',
                'metadata_template' => [
                    ['field_name' => 'title', 'datatype' => 'string', 'label' => 'Title'],
                    ['field_name' => 'communication_type', 'datatype' => 'enum', 'label' => 'Communication Type', 'values' => ['Outgoing', 'Incoming', 'Inter-office']],
                    ['field_name' => 'date_issued', 'datatype' => 'date', 'label' => 'Date Issued'],
                    ['field_name' => 'recipient_agency', 'datatype' => 'string', 'label' => 'Agency (Recipient)'],
                    ['field_name' => 'recipient_person', 'datatype' => 'string', 'label' => 'Recipient Person'],
                    ['field_name' => 'sender', 'datatype' => 'string', 'label' => 'Sender'],
                ],
            ],
        ];

        foreach ($types as $type) {
            FileType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'file_group_id' => $type['file_group_id'],
                    'metadata_template' => $type['metadata_template'],
                ]
            );
        }
    }
}
