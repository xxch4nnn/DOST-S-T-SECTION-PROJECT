# Sample PDFs (fixtures)

Private-repo fixtures ported from Wakin lab (`dost_system/database/sample_pdfs`).

- Resolve paths via `App\Support\SamplePdfFixture` (never hard-code absolute Windows paths).
- Optional seed: `php artisan db:seed --class=DocumentFixtureSeeder` (requires a Scholar + FileType; skips if missing).
- Do not introduce a flat `files` table seeder.
