# Equili: Institutional Audit & Asset Intelligence

Equili is a high-performance inventory and reconciliation system designed for high-stakes environments where physical stock must be precisely aligned with digital records. Originally conceived to solve operational inefficiencies in the banking sector (specifically inspired by workflows at Consolidated Bank Ghana), Equili automates complex math, tracks usage velocity, and maintains a permanent audit trail.
View Live Project here: [https://equili.infinityfreeapp.com/]

## Technical Specifications

- **Backend:** PHP 8.x (Modular logic)
- **Database:** MySQL (Relational architecture with strict data integrity)
- **Frontend:** HTML5, Tailwind CSS, Alpine.js
- **Typography:** Montserrat (Institutional Branding), Plus Jakarta Sans (Data Interface)
- **Icons:** Lucide-JS Vector Assets
- **Security:** Prepared statements for SQL injection prevention and strict mode compliance.

## Core System Modules

### 1. Analytics & Consumption Velocity
The system moves beyond static counting. It calculates "Vault Turnover Rate" by comparing weekly issuance against total vault capacity. This allows managers to visualize depletion rates and anticipate restock needs before assets reach critical levels.

### 2. Transactional Inventory Registry
Equili utilizes "Box-to-Unit" logic. Users can provision assets using bulk packaging multipliers (e.g., 5 items per box). The engine automatically calculates total unit counts, allowing staff to manage stock in the format they physically handle it (Boxes + Loose units).

### 3. Reconciliation & Audit Terminal
A dedicated module for physical verification. It features:
- Dual-input reconciliation (Boxes + Units).
- Automatic variance calculation.
- Self-healing ledger: Optional synchronization of digital records upon audit authorization.
- Historical Archives: Collapsible month-over-month logs for long-term accountability.

### 4. Data Portability
Includes a global export engine that generates a structured CSV registry of all assets, current stock levels, and total valuations for external reporting.

## Deployment Instructions (Standard Server)

1. Clone the repository to your web directory.
2. Import `database.sql` into your MySQL server.
3. Configure `engine.php` with your database host, name, and credentials.
4. Ensure the server has PHP 8.0+ and the PDO extension enabled.

## Developer
[Your Name]
Senior Systems Architect & UI Designer
