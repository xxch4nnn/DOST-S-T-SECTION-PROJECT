# DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
## User Manual / Instruction Manual / User Guide

**Organization:** University of Southeastern Philippines — College of Information and Computing  
**Department:** Department of Science and Technology - Region XI Office  
**System Version:** 1.0  
**Date:** 2026-07-29  
**Prepared By:** Christian Jhon Ed J. Rosal (Fullstack / AIOps Lead), DOST-SEI OJT Intern  
**Subject:** End-user guide for installing, operating, and maintaining the scholarship records management system

---

## Table of Contents

1. [Title Page / Document Control](#1-title-page--document-control)
2. [Introduction](#2-introduction)
3. [Getting Started](#3-getting-started)
4. [Logging In and Out](#4-logging-in-and-out)
5. [Dashboard](#5-dashboard)
6. [Scholar Records](#6-scholar-records)
7. [Administrative Records](#7-administrative-records)
8. [Documents and Files](#8-documents-and-files)
9. [Search and Reports](#9-search-and-reports)
10. [System Administration](#10-system-administration)
11. [Troubleshooting and FAQs](#11-troubleshooting-and-faqs)
12. [Support and Escalation](#12-support-and-escalation)

---

## 1. Title Page / Document Control

| Field | Value |
|------|-------|
| System Title | DOST-SEI Davao Region Scholarship Records Management System |
| Short Name | DOSTorage V1 |
| Organization | University of Southeastern Philippines / DOST RXI Scholarship Section |
| Version | 1.0 |
| Date | 2026-07-29 |
| Author | Christian Jhon Ed J. Rosal |
| Subject | End-user instruction manual for installing, operating, and maintaining the system |

---

## 2. Introduction

### 2.1 What is DOSTorage V1?
DOSTorage V1 is an internal web system used by the DOST-SEI Scholarship Section to manage scholar records and administrative documents. Instead of keeping physical files, staff can upload, search, version, and retrieve records on a local network.

### 2.2 Who Should Use This Manual?
This manual is for:
- Scholarship Section staff who create, view, and manage records.
- Administrative staff who manage document types and archive records.
- System administrators who configure users and maintain backups.

### 2.3 Document Conventions Used
- **Button names** look like this: **Save**
- **Menu paths** look like: Dashboard > Scholars > Add Scholar
- Input text looks like: `student_id`

---

## 3. Getting Started

### 3.1 System Access
1. Open Chrome, Edge, or Firefox on a workstation connected to the DOST RXI internal network.
2. Enter the application URL provided by the administrator, for example:
   - `http://192.168.1.50` or configured hostname.
3. The login page appears.

### 3.2 Browser Requirements
- Enable JavaScript.
- Allow pop-ups only if the export feature requires them.
- PDF viewer enabled for preview.
- Cache should be left enabled for performance.

---

## 4. Logging In and Out

### 4.1 Login
1. Enter the email or username assigned by the administrator.
2. Enter the password.
3. Click **Login**.
4. If credentials are incorrect, the system shows an error message. Contact your administrator if password reset is needed.

### 4.2 Logout
1. Click the user menu in the top-right corner.
2. Click **Logout**.
3. Close the browser if using a shared workstation.

### 4.3 Session Behavior
- The session expires after a period of inactivity defined by the administrator.
- If you return to the app after expiry, you must log in again.

---

## 5. Dashboard

### 5.1 Dashboard Home
The dashboard shows:
- **Scholar counts:** total, active, cleared, on-hold, graduated.
- **Document counts:** total files uploaded, pending reviews, strike-offs.
- **Recent activity:** recently created or updated records.
- **Quick links:** shortcuts to Add Scholar, Upload Document, Search.

### 5.2 Using KPI Cards
- Click any KPI card to open the related filtered list.
- Use the date or status filter if available.

---

## 6. Scholar Records

### 6.1 Add a Scholar
1. Go to: **Scholars > Add Scholar**.
2. Fill in required fields:
   - `first_name`, `middle_name`, `last_name`, `suffix`
   - `student_id`
   - `school`, `course`, `region`
   - `scholarship_type`
   - `contact_number`, `email` if available
   - Status: Active / On-hold / Cleared / Graduated
3. Click **Save**.
4. A confirmation message appears.

### 6.2 View and Edit Scholar Details
1. Open **Scholars > Scholar List**.
2. Search by name or `student_id`.
3. Click the scholar name to open details.
4. Click **Edit** to update fields.
5. Click **Save**.

### 6.3 Manage Documents for a Scholar
1. Open a scholar record.
2. Go to the **Documents** tab.
3. Click **Upload Document**.
4. Select `file_type` from the list configured by your administrator.
5. Choose a file from your computer.
6. If the file type requires metadata, fill required metadata fields.
7. Click **Upload**.
8. Success or validation errors appear inline.

### 6.4 Duplicate Records
- If the system warns about a duplicate record, verify existing records before creating a new one.
- Contact your administrator if a merge is required.

---

## 7. Administrative Records

### 7.1 Add an Administrative Record
1. Go to: **Administrative Records > Add Record**.
2. Enter:
   - `title`
   - `reference_number`
   - `category`
   - `date_prepared`, `date_approved`
   - `prepared_by`, `received_by`
3. Save the record.

### 7.2 Attach Documents
- After saving the record, open its detail page.
- Go to the **Documents** tab.
- Upload document using the same upload workflow as scholar documents.

---

## 8. Documents and Files

### 8.1 Upload Rules
- Accepted file types: PDF, PNG, JPG, JPEG only.
- Maximum file size: 10 MB.
- Files with disallowed extensions or oversized uploads are rejected with a message.

### 8.2 Replace a Document
1. Open the document details.
2. Click **Replace Document**.
3. Select a new file.
4. Review metadata; update if required.
5. Click **Replace**.
6. A confirmation modal appears:
   - **Cancel**
   - **Keep history** — recommended; preserves old version.
   - **Overwrite**
7. Confirm action.
8. A new version row is created under history.

### 8.3 Soft Delete / Strike-Off
1. Open the document.
2. Click **Strike-Off**.
3. Enter a reason.
4. Confirm.
5. The document is marked strike-off and hidden from default views.

### 8.4 Restore a Document
1. Open **Documents > Strike-Off List** if available.
2. Select the document.
3. Click **Restore**.
4. Confirm. The document returns to normal status.

### 8.5 Download a Document
- From the document list or detail view, click the document filename or **Download**.
- The browser downloads the file from a controlled private route.

---

## 9. Search and Reports

### 9.1 Searching Documents
1. Open **Search** or **Documents > Search** from the menu.
2. Enter keywords if searching by title or metadata.
3. Use filters:
   - `file_type`
   - `file_group`
   - scholar name or ID if applicable
   - date range
4. Click **Search**.
5. Click any result to open details.

### 9.2 Dashboard Reports
- Dashboard charts are read-only visual summaries.
- Use the date or status controls if provided by the chart component.

### 9.3 Export Pack
1. From a scholar record or filtered list, select documents.
2. Click **Export Pack**.
3. The browser generates and downloads a combined PDF export.
4. If the export fails, ensure all selected files are under 10 MB and are valid PDF/image files.

---

## 10. System Administration

### 10.1 User Management
- Create users with appropriate roles.
- Assign roles and permissions via the Administrator panel.
- Inactivate accounts instead of deleting users to preserve audit history.

### 10.2 File Type and File Group Setup
1. Go to: **Administration > File Groups**.
2. Add a group such as `Scholarship Files` or `Administrative Files`.
3. Go to: **Administration > File Types**.
4. Add a file type and:
   - Assign it to a group.
   - Define allowed extensions.
   - Set maximum size.
   - Define metadata template fields.
   - Set a primary search key.

### 10.3 Backup
- Database backup: export via `mysqldump` or the provided script.
- Files backup: copy `storage/app/private` to backup destination.
- Document each backup in the system log.
- Test restore on a non-production environment periodically.

### 10.4 Audit Logs
- Audit logs capture login, create, update, replace, strike-off, restore, and delete actions.
- Logs include user, action type, record type, record id, payload summary, timestamp.
- Contact the system administrator if records appear to be missing from logs.

---

## 11. Troubleshooting and FAQs

### 11.1 Cannot Log In
- Confirm username and password.
- Ensure your account is active.
- Contact administrator to reset password or reassign role.
- Clear browser cache and cookies if login fails repeatedly.

### 11.2 Upload Fails
- Check file size; reduce it to under 10 MB.
- Check file type; only PDF, PNG, JPG, JPEG are accepted.
- Confirm metadata is complete if required by the file type template.
- Refresh the page and try again.

### 11.3 Search Returns No Results
- Broaden date range and remove unnecessary filters.
- Verify spelling of scholar names or reference numbers.
- Confirm documents have not been strike-off and filtered out by default.
- Contact administrator if a file type or group appears missing.

### 11.4 PDF Export Pack Fails
- Remove invalid or unsupported files from selection.
- Ensure documents are not corrupted and still exist in storage.
- Check browser console for messages if export shows a blank error.

### 11.5 System Is Slow
- Reduce the number of documents selected in one view.
- Avoid running exports and large searches simultaneously.
- Restart browser if memory usage is high.
- Notify administrator if server CPU or memory is saturated.

### 11.6 Lost Document Versions
- Previous versions are preserved in `document_versions`.
- Contact administrator to recover or inspect version history if UI access is unavailable.

---

## 12. Support and Escalation

### 12.1 Support Contacts
| Role | Responsibility | Contact Route |
|------|----------------|--------------|
| OJT Developer | Bug reports, feature clarification | DOSTorage GitHub repository or project meeting |
| System Administrator | User access, backups, server issues | DOST RXI IT / OJT lead |
| Scholarship Section | Record accuracy and business rules | Ma'am Bern / OJT Supervisor |

### 12.2 Incident Reporting
When reporting an issue, include:
- Workstation or user account.
- Browser and version.
- Steps to reproduce the issue.
- Error message or unexpected behavior.
- Screenshot or file name if relevant.

### 12.3 Maintenance Windows
- The administrator may schedule updates, migrations, or restarts.
- Users should save work and log out before maintenance windows.
- Notification of updates will be provided by the Scholarship Section.

---

*End of User Manual*
