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
* **Detailed Functionalities**:
  - **Project Registry & ABYIP Profiling**: Register SK projects linked directly to their approved Annual Barangay Youth Investment Program (ABYIP) reference codes and official budget categories (e.g., *Equitable Access to Education*, *Environmental Protection*, *Sports Development*, *Youth Employment & Livelihood*).
  - **Milestone & Progress Tracking**: Track physical project execution across 4 sequential stages (*Planning*, *Authorization*, *Implementation*, *Monitoring*).
  - **Financial Transaction Encoding**: Encode financial records including Requests for Obligation of Allotment (ROA), Procurement Documents, Disbursement Vouchers (DV), and Liquidation Reports with mandatory file uploads.
  - **MAR Submission Module**: Compile and submit Monthly Accomplishment Reports (Session Minutes, Attendance Records, Post-Activity Reports, Financial Reports) directly to the LYDO before monthly compliance deadlines.

### 2. 📋 Local Youth Development Office (LYDO) Dashboard (`lydo`)
* **Target Users**: Municipal LYDO Officers and Compliance Reviewers.
* **Detailed Functionalities**:
  - **Compliance & Completeness Review**: Review incoming MAR submissions and financial documents against approved ABYIP budgets and COA rules.
  - **Deficiency Routing & Notifications**: Flag incomplete or non-compliant submissions, attach detailed deficiency remarks, and return documents back to the concerned SK Barangay Council for correction.
  - **Compliance Status Tracker**: Monitor submission timeliness and compliance rates across all constituent barangays in real time.
  - **Endorsement & Certification**: Issue digital *Certifications of Review Completeness* upon endorsing compliant transactions to the SK Federation.

### 3. 🏛️ Municipal SK Federation Dashboard (`sk_fed`)
* **Target Users**: SK Federation Officers and Municipal Archivists.
* **Detailed Functionalities**:
  - **Digital SK Fund Registry**: Record LYDO-approved financial transactions into the official municipal ledger (capturing barangay name, transaction date, nature of expenditure, amount, reference number, and compliance remarks).
  - **Certification Generation**: Auto-generate digital *Certifications of Recording* for official archiving.
  - **Report Consolidation Module**: Merge barangay-level MAR submissions into a municipal-wide compliance dataset.
  - **Quarterly Monitoring Report Generation**: Aggregate compliance rates and common deficiencies into downloadable Quarterly Compliance Monitoring Reports for the Sangguniang Bayan and Office of the Mayor.

### 4. 🔍 Municipal Verification & Oversight Dashboard (`verification`)
* **Target Users**: Municipal Accountant and Office of the Mayor Representatives.
* **Detailed Functionalities**:
  - **Financial Ledger Verification**: Read-only access to view recorded transactions, disbursement details, liquidations, and procurement docs across all barangays.
  - **Executive Analytics**: Real-time aggregate metrics displaying total registered projects, total allocated budget, recorded expenditures, and funds currently in the review pipeline.
  - **Audit Trail & Compliance Transparency**: Inspect verified certifications and MAR compliance records for municipal supervisory oversight without modifying barangay-level operational data.

### 5. 🌐 Public Transparency Portal (`public` / Citizen View)
* **Target Users**: Community Members, Youth Beneficiaries, and the General Public.
* **Detailed Functionalities**:
  - **Barangay Project Explorer**: Search, filter, and view active/completed SK projects across all barangays in Ramon Magsaysay.
  - **Public Fund Ledger**: Inspect transparent summaries of project budgets, fund utilization statuses, and official digital certifications.
  - **Citizen Feedback Channel**: Submit feedback, inquiries, or concerns regarding specific SK-funded projects directly to municipal authorities.

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
