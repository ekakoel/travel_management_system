# Travel Management System

See [docs/project-understanding-rules.md](docs/project-understanding-rules.md) for the mandatory project understanding rules that must be followed before any AI agent or developer makes changes in this repository.
See [docs/blade-asset-rules.md](docs/blade-asset-rules.md) for the mandatory Blade, CSS, and JavaScript separation rules used in this repository.
See [docs/frontend-ui-standards.md](docs/frontend-ui-standards.md) for the mandatory frontend UI consistency rules used in this repository.
See [docs/frontend-roadmap.md](docs/frontend-roadmap.md) for the mandatory frontend roadmap and change log that must be updated on every frontend change.
See [docs/frontend-roadmap-entry-template.md](docs/frontend-roadmap-entry-template.md) for the ready-to-copy roadmap entry template used for frontend work.

This repository contains the source code for a comprehensive Travel Management System, a web application designed to streamline the management of tours, bookings, and customer relations for a travel agency.

The application features a robust admin panel for managing tours, orders, users, and other site content, alongside a customer-facing interface for browsing and booking tour packages.

## Mandatory Working Rule

Before making any code change, adjustment, refactor, or debugging step in this project, every AI agent and developer must first understand the relevant system flow thoroughly, especially:

- route structure
- route groups and middleware layers
- auth, approval, and profile completeness flow
- controller to view flow
- redirect and canonical URL behavior

Do not implement changes based on a single file in isolation. Read [docs/project-understanding-rules.md](docs/project-understanding-rules.md) first and treat it as mandatory.

## ✨ Features

- **Tour & Travel Package Management:** Easily create, update, and manage tour packages with detailed information, including itineraries, pricing, and availability.
- **Customer Booking System:** A seamless booking process for customers to browse tours, make reservations, and manage their bookings.
- **Admin Dashboard:** A powerful dashboard for administrators to get an overview of the business, including recent bookings, revenue, and user activity.
- **User Authentication & Role Management:** Secure user authentication with role-based access control (e.g., admin, staff, customer).
- **PDF Invoice & Contract Generation:** Automatically generate PDF invoices and contracts for bookings.
- **Payment Confirmation:** A system for customers to confirm their payments and for admins to verify them.
- **Content Management:** Manage website content such as pages, blog posts, and testimonials.
- **Real-time Notifications:** Instant notifications for new bookings, payments, and other important events.

## 🛠️ Tech Stack

- **Backend:** PHP 8.1 / Laravel 10
- **Frontend:** Vue.js 2, Vue Router, Vuex
- **Admin UI:** AdminLTE 3, Bootstrap 5
- **Database:** MySQL / MariaDB
- **API Authentication:** Laravel Passport
- **Key Libraries:**
  - `barryvdh/laravel-dompdf` for PDF generation.
  - `maatwebsite/excel` for Excel data import/export.
  - `livewire/livewire` for dynamic interfaces.
  - `jenssegers/agent` for user-agent detection.
  - `pusher/pusher-php-server` for real-time notifications.

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & NPM
- A database server (e.g., MySQL, MariaDB)

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/ekakoel/travel_management_system.git
    cd travel_management_system
    ```

2.  **Install backend dependencies:**
    ```bash
    composer install
    ```

3.  **Set up your environment file:**
    ```bash
    cp .env.example .env
    ```
    *After copying, open the `.env` file and configure your database connection details (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).*

4.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```

6.  **Install Laravel Passport for API authentication:**
    ```bash
    php artisan passport:install
    ```

7.  **Install frontend dependencies:**
    ```bash
    npm install
    ```

8.  **Compile frontend assets:**
    - For development:
      ```bash
      npm run dev
      ```
    - For production:
      ```bash
      npm run build
      ```

## 🏃 Usage

1.  **Start the Laravel development server:**
    ```bash
    php artisan serve
    ```
2.  Your application should now be running at `http://127.0.0.1:8000`.

## WhatsApp API

This project can proxy and control a local WhatsApp bot server. Configure these in `.env`:
```env
WHATSAPP_BOT_URL=http://127.0.0.1:3000
WA_API_KEY=your-secret-key
```

All WhatsApp endpoints are under `POST /api/whatsapp/*` and protected by `X-API-KEY`.

Example send:
```bash
curl -X POST http://127.0.0.1:8000/api/whatsapp/send \
  -H "X-API-KEY: your-secret-key" \
  -H "Content-Type: application/json" \
  -d "{\"phone\":\"08123456789\",\"message\":\"Hello\"}"
```

Other endpoints:
```text
GET  /api/whatsapp/status
GET  /api/whatsapp/qr
POST /api/whatsapp/connect
POST /api/whatsapp/disconnect
POST /api/whatsapp/reload
POST /api/whatsapp/restart
POST /api/whatsapp/reset
POST /api/whatsapp/send-driver
POST /api/whatsapp/send-operator
POST /api/whatsapp/send-both
```

## ✅ Testing

To run the PHPUnit test suite, execute the following command:
```bash
./vendor/bin/phpunit
```

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
