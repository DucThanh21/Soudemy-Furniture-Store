# Soudemy-Furniture-Store
Website bán nội thất
# Soudemy - Modern Furniture E-Commerce Website

Modern furniture store built with PHP, MySQL, JavaScript, and Bootstrap.

## Features

- 🛍️ User authentication & profile management
- 📦 Product catalog with search & filters
- 🛒 Shopping cart & checkout system
- 📱 Order management & tracking
- 👨‍💼 Admin panel for managing products, orders, users
- 🎟️ Coupon & discount system
- 📱 Fully responsive design

## Demo

🔗 **Live Demo:** [Coming soon]

## Screenshots

[Add screenshots of your website here]

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (recommended for local development)

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/DucThanh21/Soudemy-Furniture-Store.git
cd Soudemy-Furniture-Store
```

### 2. Setup Database
Import the database schema:
```bash
mysql -u root -p < soudemy_demo.sql
```

Or use phpMyAdmin:
- Create a new database named `Soudemy_Demo`
- Import `soudemy_demo.sql` file

### 3. Configure Database Connection
Copy the example configuration files:
```bash
# On Windows
copy frontend\config\config.example.php frontend\config\config.php
copy frontend\config\database.example.php frontend\config\database.php

# On Linux/Mac
cp frontend/config/config.example.php frontend/config/config.php
cp frontend/config/database.example.php frontend/config/database.php
```

Edit `frontend/config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
define('DB_NAME', 'Soudemy_Demo');
define('SITE_URL', 'http://localhost/Soudemy-Furniture-Store/frontend');
```

Edit `frontend/config/database.php` with the same credentials.

### 4. Setup Upload Directories
```bash
# On Windows (Git Bash or PowerShell)
mkdir -p uploads/avatars

# On Linux/Mac
mkdir -p uploads/avatars
chmod 755 uploads
chmod 755 uploads/avatars
```

### 5. Access the Website
- **Frontend:** `http://localhost/Soudemy-Furniture-Store/frontend/`
- **Admin Panel:** `http://localhost/Soudemy-Furniture-Store/backend/manager.php`

## Default Admin Account

- **Email:** `admin@soudemy.com`
- **Password:** `admin123`

**⚠️ Important:** Change the default admin password immediately after first login!

## Project Structure

```
Soudemy-Furniture-Store/
├── frontend/                # User-facing application
│   ├── config/             # Configuration files (gitignored)
│   │   ├── config.example.php
│   │   └── database.example.php
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── images/             # Product & UI images
│   ├── includes/           # Reusable PHP components
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── login.php
│   ├── index.php           # Homepage
│   ├── shop.php            # Product listing
│   ├── product.php         # Product details
│   ├── cart.php            # Shopping cart
│   ├── checkout.php        # Checkout page
│   └── profile.php         # User profile
├── backend/                # Admin panel
│   ├── api/                # API endpoints
│   │   ├── get_orders.php
│   │   ├── order_details.php
│   │   └── coupons.php
│   ├── manager.php         # Admin dashboard
│   ├── manage_products.php # Product management
│   └── order_view.php      # Order management
├── uploads/                # User uploads (gitignored)
│   └── avatars/
├── database/               # SQL schema
│   └── soudemy_demo.sql
├── .gitignore
└── README.md
```

## Features Details

### User Features
- ✅ User registration & login
- ✅ Profile management with avatar upload
- ✅ Product browsing with categories
- ✅ Real-time search with suggestions
- ✅ Add to cart functionality
- ✅ Order placement with multiple payment methods
- ✅ Order tracking
- ✅ Coupon application

### Admin Features
- ✅ Product management (CRUD)
- ✅ Order management & status updates
- ✅ User management
- ✅ Coupon creation & management
- ✅ Sales analytics dashboard

## Technologies Used

- **Backend:** PHP 7.4+, MySQL
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Libraries:** Bootstrap 5, Font Awesome
- **Tools:** XAMPP, phpMyAdmin

## Security Notes

⚠️ **Important Security Practices:**

- Never commit `config.php` or `database.php` files
- Change default admin credentials on first login
- Set proper file permissions (755 for directories, 644 for PHP files)
- Use HTTPS in production environment
- Sanitize all user inputs to prevent SQL injection
- Use prepared statements for database queries
- Implement CSRF protection for forms

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Known Issues

- [ ] List any known bugs or limitations here

## Future Enhancements

- [ ] Payment gateway integration (PayPal, Stripe)
- [ ] Product reviews & ratings
- [ ] Wishlist functionality
- [ ] Email notifications
- [ ] Multi-language support

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

**Duc Thanh**
- GitHub: [@DucThanh21](https://github.com/DucThanh21)
- Email: your.email@example.com

**Project Link:** [https://github.com/DucThanh21/Soudemy-Furniture-Store](https://github.com/DucThanh21/Soudemy-Furniture-Store)

## Acknowledgments

- Bootstrap for the responsive framework
- Font Awesome for icons
- [Add any other credits]

---

⭐ If you find this project useful, please give it a star!
