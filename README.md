# Watch SK Fund Management System

> **⚠️ Disclaimer:** This system is currently a **Prototype for a Capstone Project**. The features, codebase, and database structure are in active development and will be modified, expanded, or changed in future iterations.

## 📖 Overview

The **Watch SK Fund Management System** is a centralized web-based platform designed to monitor, track, and enforce transparency in the financial operations and youth development programs of the Sangguniang Kabataan (SK) across all barangays within a municipality (currently tailored for Ramon Magsaysay, Zamboanga del Sur).

The system addresses the need for stringent fiscal responsibility and public transparency by providing a structured workflow for project planning, transaction approval, and public disclosure of all SK funds.

---

## 👥 System Roles and Capabilities

The platform operates on a multi-tier authentication system, ensuring strict separation of duties and checks-and-balances.

### 1. SK Admin (Barangay Level)
*The creator and executor of projects at the barangay level.*
- **Project Management:** Register new SK projects with approved budgets.
- **Transaction Entry:** Submit financial transactions (Disbursements and Liquidations) tied to specific projects, including uploading supporting documentation.
- **Milestone Tracking:** Track the physical progress of projects across 4 stages (Planning, Authorization, Implementation, Monitoring).
- **Monthly Accomplishment Reports (MAR):** Submit mandatory monthly reports (session minutes, attendance, post-activity reports, financial reports) for LYDO review.

### 2. LYDO (Local Youth Development Officer)
*The primary reviewer and compliance checker at the municipal level.*
- **Review Transactions:** Evaluates transactions submitted by SK Admins.
- **Issue Certifications:** Upon approval, the system automatically generates a **Certification of Review Completeness** issued by the LYDO.
- **Return for Compliance:** Can return incomplete or non-compliant transactions back to the SK Admin with remarks.
- **Review MARs:** Monitors and approves the monthly accomplishment reports from all barangays.

### 3. SK Federation (Municipal Level)
*The final recorder and official archivist.*
- **Record Transactions:** Once approved by LYDO, the SK Federation reviews and officially records the transaction into the municipal registry.
- **Issue Certifications:** Upon recording, the system automatically generates a **Certification of Recording** issued by the SK Federation.

### 4. Public (Transparency Portal)
*Unrestricted access for citizens to monitor their barangay's funds.*
- **Active Projects Dashboard:** View all ongoing and completed SK projects across the municipality, complete with real-time dynamic filtering by Barangay.
- **Official Registry:** A searchable, filterable ledger of all SK financial transactions.
- **Document Access:** The public can independently view project budgets, transaction statuses, and download the official Certifications issued by the LYDO and SK Federation.

---

## ⚙️ Core System Components

- **Project Milestones Module:** A 4-stage workflow tracker. Milestones are intrinsically linked to financial operations (e.g., LYDO approving a transaction automatically marks the "Implementation" milestone and shifts the project status to "Ongoing").
- **Financial Transactions Engine:** A rigid pipeline ensuring no money is officially cleared until it passes from SK Admin → LYDO (Review) → SK Fed (Record).
- **Public Transparency Engine:** A front-facing portal designed with modern UI principles (animations, snap-scrolling) to make civic engagement easy and accessible for the youth and citizens.
- **Automated Certification Generation:** Replaces manual paperwork by auto-generating verifiable digital certificates at each step of the approval chain.

---

## 💻 Tech Stack

This prototype is built using a lightweight, native stack for rapid development and straightforward deployment:
- **Backend:** PHP (Vanilla) / PDO
- **Database:** MySQL
- **Frontend / Styling:** HTML5, Tailwind CSS (via CDN)
- **Interactivity:** Vanilla JavaScript, Anime.js (for smooth scroll/load animations), SweetAlert2 (for interactive modals/alerts).
- **Architecture:** Custom MVC (Model-View-Controller) pattern with a centralized router (`index.php`).

---

## 🚀 Future Roadmap (Post-Prototype)
- Dynamic PDF generation for reports and certifications.
- Analytics dashboard and chart visualizations for municipal-wide fund distribution.
- Email/SMS notifications for workflow updates (e.g., when a transaction is returned).
- Advanced role-based granular permissions.
