# Web-Based SK Fund Monitoring and Transparency Management System

> **Municipality of Ramon Magsaysay, Zamboanga del Sur**  
> A digitalized review, recording, financial reporting, milestone tracking, and public transparency platform mandated under the Sangguniang Kabataan (SK) Fund Program Ordinance.

---

## 📖 Overview

The **Web-Based SK Fund Monitoring and Transparency Management System** is a centralized platform designed to automate and digitalize the management, compliance review, official recording, and public disclosure of Sangguniang Kabataan (SK) funds across constituent barangays in the Municipality of Ramon Magsaysay, Zamboanga del Sur.

Mandated under Republic Act No. 10742 (*SK Reform Act of 2015*) and the local Municipal SK Fund Ordinance, the system transitions local governance away from vulnerable paper-based logbooks and manual document routing toward a secure, traceable, role-based digital environment.

---

## 💻 Tech Stack & Architecture

- **Backend**: Native PHP 8.x (Vanilla PHP using OOP and PDO for database access)
- **Database**: MySQL / MariaDB (`watch_sk_fund`)
- **Frontend / UI**: HTML5, Tailwind CSS (via CDN), Google Fonts (*Inter*)
- **Interactivity & UI Effects**: Vanilla JavaScript, Anime.js (entrance & transition animations), SweetAlert2 (interactive notifications & modals)
- **Architecture**: Custom MVC (Model-View-Controller) pattern with a single-entry router (`index.php`) and shared UI Layout system (`views/layout.php`)

---

## 👥 User Roles & Dashboard Breakdown

The system enforces strict Separation of Duties across 5 distinct user role categories:

### 1. 🏢 SK Barangay Council Dashboard (`sk_admin`)
* **Target Users**: SK Chairpersons, SK Treasurers, and Council Members at the barangay level.
* **Dashboard Displays**:
  - **Total Project Budget**: Aggregate sum of all approved SK projects under the barangay
  - **Pending/Returned MARs**: Count of Monthly Accomplishment Reports awaiting resubmission due to deficiencies
  - **Recorded Transactions**: Count of successfully recorded financial transactions in the municipal registry
  - **Recent Projects List**: Quick view of the 5 most recently created or updated projects
* **Core Functionalities**:
  - **Project Registry & ABYIP Profiling**: Register and manage SK projects linked directly to their approved Annual Barangay Youth Investment Program (ABYIP) reference codes. Categorize by budget category (Equitable Access to Education, Environmental Protection, Climate Change Adaptation, Youth Employment & Livelihood, Health & Anti-Drug Abuse, Sports Development, or Capability Building).
  - **Milestone & Progress Tracking**: Track physical project execution across 4 sequential stages (Planning → Authorization → Implementation → Monitoring) with status updates and date markers.
  - **Financial Transaction Encoding**: Encode and upload financial records including ROA (Request for Obligation of Allotment), Disbursement Vouchers (DV), Procurement Documents, and Liquidation Reports with required file attachments for audit trail.
  - **MAR Submission Module**: Compile Monthly Accomplishment Reports with required supporting documents (Session Minutes, Attendance Records, Post-Activity Reports, Financial Statements) and submit directly to LYDO before monthly deadlines.
  - **Status Monitoring**: View transaction and report status progression from pending → reviewed → recorded to ensure timely compliance.

### 2. 📋 Local Youth Development Office (LYDO) Dashboard (`lydo`)
* **Target Users**: Municipal LYDO Officers, Compliance Reviewers, and Administrative Staff.
* **Dashboard Displays**:
  - **Pending Reviews**: Count of MARs and transactions awaiting compliance review from all barangays
  - **Reviewed MARs**: Count of Monthly Accomplishment Reports already reviewed and marked compliant or deficient
  - **Reviewed Transactions**: Count of financial transactions that have passed compliance screening
  - **Pending MARs Queue**: Filterable list of barangay-submitted MARs with submission dates and barangay names
  - **Pending Transactions Queue**: List of financial transactions waiting for completeness and rule-based validation
* **Core Functionalities**:
  - **Compliance & Completeness Review**: Validate incoming MAR submissions and transaction documents against approved ABYIP budgets, Commission on Audit (COA) rules, and municipal ordinance requirements. Check for missing attachments, correct amounts, and proper references.
  - **Deficiency Flagging & Routing**: Identify incomplete or non-compliant submissions; attach detailed deficiency remarks (budget overages, missing documents, wrong budget codes); and return to the originating SK Barangay Council for correction with deadline reminders.
  - **Barangay Compliance Status Tracker**: Monitor submission timeliness, compliance rates, and common deficiency patterns across all constituent barangays in real-time dashboards.
  - **Approval & Endorsement**: Issue digital *Certifications of Review Completeness* for compliant submissions, marking them as "reviewed" and ready for SK Federation recording.
  - **Batch Processing**: Manage high-volume MARs and transactions with queue prioritization and bulk action capabilities during month-end deadline periods.

### 3. 🏛️ Municipal SK Federation Dashboard (`sk_fed`)
* **Target Users**: SK Federation Officers, Municipal Archivists, and Central Records Administrators.
* **Dashboard Displays**:
  - **Pending Recording**: Count of LYDO-approved transactions and MARs awaiting official ledger entry
  - **Recorded MARs**: Count of successfully recorded Monthly Accomplishment Reports in the municipal archive
  - **Recorded Transactions**: Count of financial transactions officially inscribed in the SK Fund registry
  - **Queue of Reviewed Items**: List of approved transactions and MARs ready to be recorded, organized by submission date
* **Core Functionalities**:
  - **Digital SK Fund Registry**: Officially record LYDO-approved financial transactions into the municipal ledger, capturing barangay name, transaction date, nature of expenditure, amount in Philippine Pesos, unique reference number, and compliance remarks for permanent archival.
  - **Auto-Certification Generation**: Generate and issue digital *Certifications of Recording* with unique reference numbers and timestamps to confirm official municipal ledger entry for archival purposes.
  - **Report Consolidation Module**: Aggregate and merge barangay-level MAR submissions into a municipal-wide compliance dataset; consolidate spending patterns and fund utilization rates.
  - **Quarterly Monitoring Report**: Auto-generate downloadable Quarterly Compliance Monitoring Reports summarizing aggregate compliance rates, total expenditures, common deficiency patterns, and project completion percentages for submission to the Sangguniang Bayan and Office of the Mayor.
  - **Municipal Archive Management**: Maintain searchable, tamper-proof digital records of all SK transactions, certifications, and compliance documents with audit-trail timestamps.

### 4. 🔍 Municipal Verification & Oversight Dashboard (`verification`)
* **Target Users**: Municipal Accountant, Office of the Mayor Representatives, and Sangguniang Bayan Members (Read-Only Access).
* **Dashboard Displays**:
  - **Total Registered Projects**: System-wide count of all SK projects across all barangays
  - **Total Allocated Budget**: Aggregate sum of all approved project budgets
  - **Recorded Expenditures**: Total amount of transactions officially recorded in the municipal registry (✓ recorded status)
  - **Reviewed Pending Amount**: Total amount of transactions that have passed LYDO review but not yet recorded
  - **Pending Amount**: Total amount of transactions still in review pipeline awaiting LYDO clearance
  - **Complete Transaction Ledger**: Full list of all recorded transactions with barangay names, project titles, amounts, submission dates, and submitter information
* **Core Functionalities**:
  - **Financial Ledger Verification**: Read-only access to inspect recorded transactions, disbursement details, liquidation amounts, and procurement documentation across all barangays without modification rights.
  - **Executive Analytics**: Real-time executive-level aggregate metrics and charts showing total fund allocation vs. utilization, compliance rates, transaction pipeline status, and monthly trends.
  - **Audit Trail Inspection**: Inspect digital certifications, MAR compliance records, and transaction review chains to verify municipal supervisory oversight and regulatory adherence.
  - **Transparency Reporting**: Generate on-demand audit reports, compliance summaries, and fund utilization dashboards for Sangguniang Bayan oversight and public accountability.
  - **No Modification Authority**: Read-only dashboard prevents accidental or unauthorized changes to barangay-level operational data; ensures verification role remains independent.

### 5. 🌐 Public Transparency Portal (`public` / Citizen View)
* **Target Users**: Community Members, Youth Beneficiaries, Non-Registered Citizens, and the General Public.
* **Portal Displays**:
  - **Barangay Project Explorer**: Searchable, filterable grid of active and completed SK projects across all barangays with project titles, descriptions, budgets, and status badges.
  - **Official Registry Ledger**: Structured table of recorded SK financial transactions organized by barangay, transaction type, amount, status, and submission date with search and filter capabilities.
  - **Key Metrics Cards**: Public-facing statistics showing total tracked projects, recorded transactions, and barangays covered.
* **Core Functionalities**:
  - **Barangay Project Explorer**: Search and filter active/completed SK projects by barangay, project type, or keyword; view project descriptions, approved budgets, and current status without requiring login.
  - **Public Fund Ledger Inspection**: Browse transparent summaries of all recorded SK financial transactions, including project association, fund amounts, transaction types, and official recording dates to enable citizen oversight.
  - **Citizen Inquiry & Feedback Channel**: Submit feedback, questions, or concerns regarding specific SK-funded projects directly to municipal authorities through integrated feedback forms (future enhancement).
  - **Accessibility & Openness**: No login required; information is formatted for public accessibility; promotes citizens' right to information under the Freedom of Information Executive Order.

---

## ⚙️ How the System Runs (Workflow Pipeline)

```
[ SK Barangay Council ] 
       │ 1. Registers Project (ABYIP) & Encodes Financial Transaction / MAR
       ▼
[ Local Youth Development Office (LYDO) ]
       │ 2. Conducts Compliance & Completeness Review
       ├───► (Deficient) ──► Returned to SK Barangay Council for Correction
       ▼ (Compliant)
[ Municipal SK Federation ]
       │ 3. Records into Official Digital SK Fund Registry & Consolidates MARs
       ▼
 ┌───────────────────────────────┴───────────────────────────────┐
 ▼                                                               ▼
[ Municipal Verification Dashboard ]             [ Public Transparency Portal ]
(Municipal Accountant & Office of Mayor)         (Citizens & Youth Beneficiaries)
```

---

## 🚀 Installation & Setup Guide

### Requirements
- **XAMPP / WAMP / LAMP** stack (PHP >= 8.0, Apache, MySQL / MariaDB)
- Web browser (Chrome, Firefox, Edge, Safari)

### 1. Clone the Repository
Clone the repository into your local server root directory (`htdocs` for XAMPP):
```bash
cd c:/xampp/htdocs
git clone https://github.com/YoursTrulyInarius/FundSystem.git FundSystem
```

### 2. Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a database named `watch_sk_fund` (or let the SQL script create it automatically).
3. Import the `database.sql` file located at the root of the project:
   ```bash
   c:/xampp/htdocs/FundSystem/database.sql
   ```

### 3. Verify Database Connection
Check `includes/config.php` and `core/Database.php` to ensure your local MySQL credentials match:
```php
// core/Database.php
private $host = 'localhost';
private $db_name = 'watch_sk_fund';
private $username = 'root';
private $password = '';
```

### 4. Default System Accounts
All pre-seeded test accounts use the default password: **`password`**

| Role | Username | User Group | Scope |
| :--- | :--- | :--- | :--- |
| **SK Barangay Admin** | `sk_admin1` | `sk_admin` | Barangay Poblacion |
| **LYDO Reviewer** | `lydo_admin` | `lydo` | Municipal LYDO Office |
| **SK Federation** | `sk_fed` | `sk_fed` | Municipal SK Federation |
| **Municipal Accountant** | `accountant` | `verification` | Municipal Accounting Office |
| **Office of the Mayor** | `mayor_office` | `verification` | Executive Office |
| **Public User** | *(No login required)* | `public` | Public Transparency Portal |

---

## 🌐 Running Locally

1. Start **Apache** and **MySQL** in your XAMPP Control Panel.
2. Open your web browser and navigate to:
   - **Public Transparency Portal**: `http://localhost/FundSystem/`
   - **Administrative Login**: `http://localhost/FundSystem/login`
