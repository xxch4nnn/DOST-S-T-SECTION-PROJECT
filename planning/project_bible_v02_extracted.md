
--- PAGE 1 ---
-​
Backend-to-Mother Stitch — Wakin Review 
Pack 
-​
To: Waken Cean C. Maclang​
From: Chan​
Date: 2026-07-28​
Purpose: Your stitch review + repo findings updated here. Reply with answers to the eight 
questions below; we lock the decisions in standup after. 
-​
 
-​
 
-​
1. Methodology check 
Tool 
What we did 
gh repo clone 
WakenMac/DOST-RXI-OJT_SQL-Files 
_backend_scratch/wakin 
Cloned your lab locally 
Read 
dost_system/database/migrations/
2026_07_20_* 
Confirmed schema vs. how it’s reflected in 
the halfway doc 
Read 
dost_system/database/seeders/* + 
dost_system/app/Models/* + 
dost_system/app/Observers/* 
Confirmed DTR claims for Jul 22–25 
Read 
dost_system/database/sample_pdfs
/ 
Confirmed PDF fixtures exist 
-​
 
-​
Your dost_system/ has the pieces the halfway doc described: 
-​
 
-​
file_groups, file_types, files with softDeletes() 
-​
FileTypeSeeder with metadata_template 
-​
FileObserver, ScholarObserver 
-​
sample_pdfs/ with real COR/memo payroll fixtures 
-​
Sanctum + AuditLog model 
-​
 
-​
The concerns below are specific to those findings, not generic theory. 
-​
 
-​
 

--- PAGE 2 ---
-​
2. Conflict 1: your files table vs. mother documents + 
document_versions 
-​
Current state in your repo (files table migration): 
-​
 
-​
file_type_id → file_types 
-​
file_name, file_path, file_size, uploaded_at, updated_at, mime_type 
-​
softDeletes('deleted_at') 
-​
json('metadata') 
-​
 
-​
Mother repo (documents table migration + behavior): 
-​
 
-​
morphs('documentable') → Scholar or AdministrativeRecord 
-​
status enum: active / struck_off 
-​
document_versions table with version_number, replaced_by_user_id 
-​
documents.uploaded_by → users 
-​
 
-​
Why we’re not shipping your files table:​
It’s not a difference in naming only. Your table is intentionally flat for your practice OJT lab. 
Mother’s schema adds two behaviors you already wanted: 
-​
 
1.​ A file can belong to a Scholar or an AdminRecord without adding a second 
column/switch pattern 
2.​ Every replace can keep history; your Jul 24 “delete a canvas → its pages remove” is 
an in-session editor action, whereas document_versions preserves what was 
committed to the DB 
-​
 
-​
Shipping your files table would drop polymorphism and version history for all other 
teammates already coding against documents. 
-​
 
-​
Halfway proposal: 
-​
 
-​
Drop files 
-​
Keep file_groups + file_types from your lab 
-​
document_types (inherits file_types shape) + metadata template seeding 
-​
Ownership: documentable_* morph, not metadata.scholar_id 
-​
Jul 24 move-between-types becomes a document_type_id update on documents, 
not a second storage table 
-​
Questions — Q1, Q2, Q3 
-​
Q1. In V1, do you want full File Group admin CRUD (/file-groups 
index/create/edit/delete), or seed-only groups where admins only edit the types under them?​
Why: Your Jul 21 practice site has full CRUD; mother has none. Full CRUD costs 
Wakin/Miguel/Rui hours, but seed-only still gives you taxonomy for search and the Encoder 
UX you designed. 
-​
 

--- PAGE 3 ---
-​
Q2. Accept documents.documentable_* morph ownership, with metadata used 
only for form fields (academic_year, semester, school, etc.) and never for scholar_id 
linkage?​
Why: In your OJT lab metadata.scholar_id was the link because there was no 
polymorphic ownership. Using morph FKs removes the “who actually owns the file” ambiguity 
and keeps search/policies clean. 
-​
 
-​
Q3. Column name on file_types: metadata_template (as in your seeder) or 
metadata? Reply with one; we’ll lock and stop schema drift. 
-​
 
-​
 
-​
3. Conflict 2: document version control 
-​
Current state in your repo:​
Your File model uses soft deletes only; no equivalent of mother’s document_versions. 
-​
 
-​
Mother repo: 
-​
 
-​
document_versions.version_number, replaced_by_user_id 
-​
documents.status (active / struck_off) 
-​
Duplicate modal: cancel / keep_history / overwrite 
-​
 
-​
Halfway proposal:​
Combine both behaviors. Your canvas pipeline does destruction in the editor; once Save is 
pressed, we write a new document_version. Version history survives even if someone later 
resets the canvas. 
-​
Questions — Q4, Q5 
-​
Q4. After a jsPDF/canvas rearrange/combine/split, should Save always create a new 
document_version, or only when the user explicitly chooses to overwrite/duplicate?​
Why: Your File model replaced the file. Mother can keep history for audit. We want your 
write path to align with the Three-way Merge modal mother already has, or you can replace 
that modal with “always history.” Pick one. 
-​
 
-​
Q5. Drop mother’s three-way duplicate modal and always keep history? Or keep it and 
let the Encoder decide each upload?​
Why: Unify encoder UX — if every save becomes a historic copy, workers never accidentally 
lose a record; if we keep the modal, they choose intentionally. Your Jul 24 client-side editor 
already gives redo-until-save behavior; the DB should match it. 
-​
 
-​
 
-​
4. Conflict 3: DomPDF vs. jsPDF 
-​
Current state in your repo:​
dost_system/config/dompdf.php + 
resources/views/pdf/compiled_images.blade.php exist, but your Jul 23–24 DTR 
shows you moved to jsPDF with sortable canvases. The DomPDF scaffolds are 
unused/deferred. 
-​
 

--- PAGE 4 ---
-​
Halfway proposal:​
Drop DomPDF from the stitch entirely. Mother already has a 
storage_path/response()->download() path for per-document download. We port your 
jsPDF + SortableJS canvas pipeline instead. 
-​
Questions — Q6, Q7, Q8 
-​
Q6. V1 canvas target:​
(a) export a scholar’s documents as one PDF only,​
(b) reorder/delete/combine pages then save (your Jul 24),​
(c) include text/annotation editing. 
-​
 
-​
We lean (b) from your DTR, but need explicit sign-off so FE does not scope-creep into 
annotation tools. 
-​
 
-​
Q7. Canvas/jsPDF ownership: you, Rui, or pair?​
Why: Save path hits your FileType/documents model and metadata rules. FE needs to 
build the sortable UI. Clear owner prevents both of you rewriting the upload screen 
independently. 
-​
 
-​
Q8. Any DomPDF path you still want kept for a specific offline report/export, or full 
drop for V1?​
Why: If a report PDF truly needs headless/batch generation without a browser, DomPDF 
re-enters as a side path. If not, dropping it avoids two PDF stacks in mother. 
-​
 
-​
 
-​
5. Your additions — accept/sequence 
-​
Your Jul 22–25 additions are accepted as primary ports, not optional extras. Below 
are the specifics we pulled from your repo + DTR. 
-​
A. FileTypeSeeder + metadata parser + searchable keys (Priority 
#1) 
-​
Confirmed from your repo: 
-​
 
-​
FileTypeSeeder has the Corey/COR/Endorsement/Memo/Payroll types 
-​
FileType model has metadata_template JSON 
-​
ScholarshipProgram, Region, Course, School seeders exist 
-​
 
-​
That’s exactly “reactive fields driven by template” + the fixtured domain values your Jul 
22 parser consumed. 
-​
 
-​
Q9. Which JSON keys inside metadata_template should be searchable from V1?​
Why: Indexing every key on local MySQL is expensive. Your DTR shows you were already 
choosing “V1 keys” on Jul 25. Name them so we scope the schema correctly. Example set: 
academic_year, semester, school_id, series_number. Confirm or change. 
-​
B. Observers (accept) 
-​
Confirmed from your repo: 
-​
 
-​
FileObserver.php, ScholarObserver.php already written in your lab 
-​
 

--- PAGE 5 ---
-​
Q10. Observers for both Document + Scholar + AdministrativeRecord, or just 
documents?​
Why: Your Jul 25 DTR says observers for “file creation, user validation, audit logs” and 
“scholar schema as pre-req to intelligent search.” Your practice observer file only covers File 
and Scholar in the model names. Confirm the full surface in mother. 
-​
 
-​
Q11. Invalid metadata vs. template: hard fail/abort save, or save with warning and 
let encoder correct later?​
Why: Jul 22 parser expects a datatype per field. If Encoder leaves semester blank on a COR, 
should we block save (data quality) or warn (don’t block intake)? This decides observer vs. 
FormRequest design. 
-​
C. Sortable canvases (clarified) 
-​
From your Jul 23–24 DTR: sortable images/canvases for reorganizing pages before 
save. This is separate from any table-column sort on scholar index. 
-​
 
-​
Q12. Confirm that when we say “sortable,” we mean page canvas SortableJS, not 
table-column sorting. Package name you used in dost_system?​
Why: Project checklist also says “sortable columns.” We need the exact package name so we 
add the right Vite dependency once, not two separate sort stacks. 
-​
 
-​
 
-​
6. Revised sequence 
Order 
PR 
What 
1 
feat/be-01-db-docs 
!SQL_Queries archive + 
SCHEMA_MAPPING.md 
2 
feat/be-02-pdf-fixtur
es 
database/sample_pdfs/
** from your repo 
3 
feat/be-03-filetype-m
etadata 
file_groups + FileType 
metadata seeders + reactive 
upload form 
4 
feat/be-04-jspdf-expo
rt 
jsPDF + page sortable 
canvases — owners per Q7 
5 
feat/be-05-observers 
FileObserver + 
ScholarObserver ports 
per Q10 
6 
feat/be-06-intelligen
t-search 
search() + indexed 
metadata keys per Q9 
7 
feat/ui-07-sortable 
Table-column sort only if still 
needed (separate from 
canvas) 

--- PAGE 6 ---
-​
 
-​
Sanctum stays deferred. Your Lesson*/sample_project/ folders stay out; mother 
is the canonical app. 
-​
 
-​
 
-​
7. Open points that need your answer 
# 
Question 
Your answer 
Q1 
File Group CRUD or 
seed-only? 
We’ll keep file CRUD, but 
only implement it after our 
OJT. 
Q2 
documents.documentabl
e_* morph ownership OK? 
Yes, its okay. 
Q3 
metadata_template vs. 
metadata on 
file_types? 
we will use the 
metadata_templates for our 
file types as important 
form-related data is saved 
here, whereas the metadata 
only contains the data itself. 
 
Example: 
metadata_template for 
Scholarship Agreement file 
type: 
(POV: A file_type’s 
metadata_template) 
 
[ 
  { 
    "label": "Scholar ID", 
    "datatype": "int", 
    "field_name": "scholar_id" 
  } 
] 
 
metadata for a file thats a 
Scholarship Agreement: 
(POV: A File’s metadata 
when its file_type_id 
equivalent to that of a 
Scholarship Agreement) 
 
{ 
  "scholar_id": 1 
} 
 

--- PAGE 7 ---
# 
Question 
Your answer 
 
 
Q4 
Save → new 
document_version, or 
overwrite? 
new (no pure deletes) 
Q5 
Drop three-way duplicate 
modal, always keep history? 
Lets keep the three-way 
duplicate to keep the version 
histories in any case they’d 
like to restore particular 
versions. Similar to GDrive 
:> 
 
In this case, we will have the 
following logic between 
documents and the 
document versions: 
 
●​ The documents and 
documents table 
serve as the same 
tables to store 
information on the 
file’s information, 
path, and dates for 
CRUD 
●​ The documents table 
serves as the 
repository for all of 
the files’ recent 
versions 
●​ The document 
versions serve as the 
repository for the 
recent and older 
versions of their 
respective files. 
●​ The documents table 
will have the created 
at, updated at, and 
deleted at columns. 
●​ The document 
versions table will 
have an updated at 
column 

--- PAGE 8 ---
# 
Question 
Your answer 
Q6 
jsPDF V1 = (b) 
reorder/delete/combine? 
We can cover both a and b. 
Pages are sortable, pages 
can also be added between 
pages even if they are PDFs, 
however this functionality 
depends heavily on jsPDF 
and Sortable from vite to 
function. 
Q7 
Canvas/jsPDF owner: you, 
Rui, pair? 
Rui will handle the sortable 
UI, more such that the CSS 
data will be the only need, 
as Sortable JS will handle 
the order or the sorting logic. 
Q8 
DomPDF side-path keep or 
full drop? 
Nope, we will drop DomPDF 
since we won’t be using it to 
convert images to PDFs, 
non-functional. 
Q9 
Searchable metadata keys 
(V1)? 
For this, we’ll include 
searchable metadata keys, 
but we will utilize only the 
primary keys for each 
metadata type.​
​
So we will only allow one to 
be the primary key.​
​
I will leave this to you na 
chan, since understandable 
na ang which ang primary 
key for each file sa 
metadata. (e.g., scholar_id, 
series_number, 
report_number, 
payroll_number) 
Q10 
Observers on which models? Okay, I intend to add 
observers in all tables that 
we will hold CRUD on for the 
purpose of the Audit Logs.​
​
This focuses on user CRUD 
and logins, and CRUD for 
the following: 
●​ scholarshipPrograms 

--- PAGE 9 ---
# 
Question 
Your answer 
●​ scholarshipProgramT
ypes 
●​ Regions 
●​ Documents 
●​ File Types 
●​ File Groups 
●​ Files 
●​ Migrations 
●​ Scholars 
●​ (Others not 
mentioned that can 
be applied to the 
audit logs) 
Q11 
Invalid metadata: hard fail or 
warn? 
For data integrity, we must 
abort the process and 
prevent the file to be saved 
to the system. 
 
For context, the metadata 
must be aligned to the 
metadata template so that 
the information linked to the 
file will be displayed properly 
in the front-end; moreover, it 
will aid in properly searching 
for the file. ​
​
Thus, having a file with an 
inappropriate metadata 
saved become problematic 
both in the front end and 
backend logic. 
 
Thus, we must abort it in the 
Livewire logic or Controller 
before it is even saved to the 
database and local storage. 
Q12 
Sortable = page canvas + 
package name? 
I am not literate in Vite 
imports, but what functioned 
for me is to use the 
SortableJS as a JavaScript 
import while downloading its 
package from Vite. 
 

--- PAGE 10 ---
# 
Question 
Your answer 
For clarity as well, 
SortableJS shall be used as 
a functional gimmick to set 
the order of the pages easily 
by the user by allowing them 
to drag the page to the 
specific order suited to them, 
rather than pressing arrows 
to change the file’s page 
once it is converted to a 
PDF. 
-​
 
-​
 
-​
8. What happens after we get your answers 
1.​ We lock BACKEND_STITCH_PLAN.md with your answers committed. 
2.​ We open PRs 1–3 before standup review so the team has something concrete to 
inspect. 
3.​ You, Rui, and Miguel are reviewers on every backend PR (CODEOWNERS). 
4.​ Mid-stitch, we add PR-C slice tasks to the shared work plan. 
-​
 
-​
Reply in Slack #dostorage-pm or comment on this file with Q1–Q12. 
-​
 
 
