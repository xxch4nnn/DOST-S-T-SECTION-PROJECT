<?php

namespace Database\Seeders;

use App\Models\FileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // #[Fillable(['file_group_id', 'name', 'metadata_template'])]
    
        $groups = [
            [
                'file_group_id'=>1,
                'name'=>'Scholarship Agreement',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Amendatory Agreement',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
                        ],
            [
                'file_group_id'=>1,
                'name'=>'Information Sheet',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Prospectus',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Certificate of Registration',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ],
                        [
                            'field_name'=>'semester',
                            'datatype'=>'enum',
                            'values'=>['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester'],
                            'label'=>'Semester'
                        ],
                        [
                            'field_name'=>'year',
                            'datatype'=>'int',
                            'label'=>'Year'
                        ]
                    ]
            ],
            [
                'file_group_id'=>1,
                'name'=>'Certificate of Grades',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ],
                        [
                            'field_name'=>'semester',
                            'datatype'=>'enum',
                            'values'=>['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester'],
                            'label'=>'Semester'
                        ],
                        [
                            'field_name'=>'year',
                            'datatype'=>'int',
                            'label'=>'Year'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'PTP Documents',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Transcript of Records',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Graduate Data File',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Leave of Absence (LOA)',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Shifting or Transfer',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>1,
                'name'=>'Clearance',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'scholar_id',
                            'datatype'=>'int',
                            'label'=>'Scholar ID'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>2,
                'name'=>'Memorandums',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'series_number',
                            'datatype'=>'string',
                            'label'=>'Series Number'
                        ],
                        [
                            'field_name'=>'subject',
                            'datatype'=>'string',
                            'label'=>'Subject'
                        ],
                        [
                            'field_name'=>'date_issued',
                            'datatype'=>'date',
                            'label'=>'Date Issued'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>2,
                'name'=>'Annual Financial Reports',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'report_number',
                            'datatype'=>'string',
                            'label'=>'Report Number'
                        ],
                        [
                            'field_name'=>'project',
                            'datatype'=>'string',
                            'label'=>'Project'
                        ],
                        [
                            'field_name'=>'report_date',
                            'datatype'=>'date',
                            'label'=>'Report Date'
                        ],
                        [
                            'field_name'=>'start_date',
                            'datatype'=>'date',
                            'label'=>'Start Date'
                        ],
                        [
                            'field_name'=>'end_date',
                            'datatype'=>'date',
                            'label'=>'End Date'
                        ]
                    ]
                
            ],     
            [
                'file_group_id'=>2,
                'name'=>'Quarterly Financial Reports',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'project',
                            'datatype'=>'string',
                            'label'=>'Project'
                        ],
                        [
                            'field_name'=>'report_date',
                            'datatype'=>'date',
                            'label'=>'Report Date'
                        ],
                        [
                            'field_name'=>'start_date',
                            'datatype'=>'date',
                            'label'=>'Start Date'
                        ],
                        [
                            'field_name'=>'end_date',
                            'datatype'=>'date',
                            'label'=>'End Date'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>2,
                'name'=>'Payrolls',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'payroll_number',
                            'datatype'=>'string',
                            'label'=>'Payroll Number'
                        ],
                        [
                            'field_name'=>'cheque_number',
                            'datatype'=>'string',
                            'label'=>'Cheque Number'
                        ],
                        [
                            'field_name'=>'date',
                            'datatype'=>'date',
                            'label'=>'Date'
                        ]
                    ]
                
            ],  
            [
                'file_group_id'=>2,
                'name'=>'Endorsements',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'academic_year',
                            'datatype'=>'text',
                            'label'=>'Academic Year'
                        ],
                        [
                            'field_name'=>'semester',
                            'datatype'=>'enum',
                            'label'=>'Semester',
                            'values'=>['1st Semester', '2nd Semester', '3rd Semester', 'Off-Semester']
                        ],
                        [
                            'field_name'=>'school_id',
                            'datatype'=>'int',
                            'label'=>'School',
                            'foreign_key'=>'schools'
                        ]
                    ]
                
            ],
            [
                'file_group_id'=>2,
                'name'=>'Communications',
                'metadata_template'=>
                    [
                        [
                            'field_name'=>'title',
                            'datatype'=>'string',
                            'label'=>'Title'
                        ],
                        [
                            'field_name'=>'communication_type',
                            'datatype'=>'enum',
                            'label'=>'Communication Type',
                            'values'=>['Ongoing', 'Incoming', 'Inter-office']
                        ],
                        [
                            'field_name'=>'year',
                            'datatype'=>'int',
                            'label'=>'Year'
                        ],
                        [
                            'field_name'=>'recepient',
                            'datatype'=>'string',
                            'label'=>'Recepient'
                        ]
                    ]
                
            ]
        ];

        foreach($groups as $group){
            FileType::firstOrCreate($group);
        }
    }
}
