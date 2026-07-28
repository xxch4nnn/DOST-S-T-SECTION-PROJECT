<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            // #[Fillable(['file_type_id', 'file_name', 'file_path', 'file_size', 'uploaded_at', 'updated_at', 'deleted_at', 'mime_type', 'metadata'])]
            [
                'file_name'=>'',
                'file_path'=>'',
                'file_size'=>'',
                'uploaded_at'=>'',
                'updated_at'=>'',
                'deleted_at'=>'',
                'mime_type'=>'',
                'metadata'=>''
            ]
        ];

        foreach($groups as $group){
            File::firstOrCreate($group);
        }
    }
}
