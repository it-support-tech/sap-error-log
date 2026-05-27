# SAP B1 Error Log System

ລະບົບຈັດການ Error ສຳລັບ SAP Business One

## Tech Stack
- **Backend:** PHP 8.2 + PDO
- **Database:** PostgreSQL 15 (ຜ່ານ pgAdmin 4)
- **Frontend:** Tailwind CSS + Vanilla JS
- **Server:** Apache 2.4
- **Container:** Docker + Docker Compose

## ໂຄງສ້າງ Project

```
sap-error-log/
├── docker/
│   ├── Dockerfile
│   ├── apache.conf
│   └── init.sql              ← SQL schema + seed data
├── public/                   ← Document root
│   ├── css/app.css
│   ├── js/app.js
│   ├── uploads/screenshots/  ← ຮູບ error ທີ່ອັບໂຫລດ
│   ├── api/
│   │   ├── error-detail.php
│   │   └── update-status.php
│   ├── errors/
│   │   ├── module.php        ← ລາຍການ error ຕາມ module
│   │   ├── search.php        ← ຄົ້ນຫາ
│   │   └── add.php           ← ເພີ່ມ error
│   ├── index.php             ← Dashboard
│   ├── login.php
│   └── logout.php
└── src/
    ├── config/
    │   ├── Database.php       ← PDO singleton
    │   └── autoload.php
    ├── middleware/
    │   └── Auth.php           ← Session management
    ├── models/
    │   ├── Employee.php
    │   ├── Module.php
    │   └── ErrorLog.php
    └── views/
        └── components/
            ├── header.php
            └── footer.php
```

## ການ Setup

### ຕ້ອງການ
- Docker Desktop
- Docker Compose v2+

### ວິທີ Run

```bash
# Clone / ວາງ project
cd sap-error-log

# Start ທຸກ service
docker compose up -d

# ກວດ log ຖ້າມີ error
docker compose logs -f app
```

### URLs
| Service    | URL                          |
|------------|------------------------------|
| Web App    | http://localhost:8080        |
| pgAdmin 4  | http://localhost:5050        |

### pgAdmin Login
- Email: `admin@sap.local`
- Password: `admin123`

### ເຊື່ອມ pgAdmin ກັບ DB
1. ເຂົ້າ pgAdmin
2. Add New Server
3. Name: `SAP DB`
4. Host: `db`, Port: `5432`
5. Database: `sap_errors`, User: `sapuser`, Password: `sappass123`

## Modules ທີ່ມີ
1. ການຈັດການລະບົບ (Administration)
2. ການເງິນແລະບັນຊີ (Financials)
3. ການຂາຍ (Sales - A/R)
4. ການຈັດຊື້ (Purchasing - A/P)
5. ການຈັດການສິນຄ້າຄົງຄັງ (Inventory)
6. ຄຸ່ຮ່ວມທຸລະກິດ (Business Partners)
7. ການທະນາຄານ (Banking)
8. ຕັ້ງຊັບສິນ (Fixed Asset)

## Stop / Reset

```bash
# Stop
docker compose down

# Reset database
docker compose down -v
docker compose up -d
```
