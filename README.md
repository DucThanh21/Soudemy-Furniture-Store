# Soudemy - Modern Furniture E-Commerce Website

![Soudemy](https://img.shields.io/badge/Status-Active-success)
![PHP](https://img.shields.io/badge/PHP-7.4+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/License-MIT-green)

Modern furniture store e-commerce website built with PHP, MySQL, JavaScript, and Bootstrap. Features a beautiful, responsive UI and complete shopping functionality.

## 🌟 Features

### User Features
- 🔐 **User Authentication** - Secure login and registration system
- 👤 **Profile Management** - Update personal information, avatar, and addresses
- 🛍️ **Product Catalog** - Browse furniture with advanced search and filters
- 🛒 **Shopping Cart** - Add, update, and remove items
- 💳 **Checkout System** - Multiple payment methods (COD, Banking, MoMo)
- 📦 **Order Tracking** - View order history and status
- 🎫 **Coupon System** - Apply discount codes at checkout
- 📱 **Responsive Design** - Works perfectly on all devices

### Admin Features
- 📊 **Dashboard** - Overview of orders, products, and users
- 📦 **Product Management** - Add, edit, delete products
- 🛍️ **Order Management** - View and update order status
- 👥 **User Management** - Manage customer accounts
- 🎫 **Coupon Management** - Create and manage discount codes

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Frameworks**: Bootstrap 5
- **Icons**: Font Awesome
- **Server**: Apache (XAMPP/WAMP/LAMP)

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- XAMPP/WAMP/LAMP (recommended for local development)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/DucThanh21/Soudemy-Furniture-Store.git
cd Soudemy-Furniture-Store
```

### 2. Setup Database
- Start XAMPP/WAMP and ensure MySQL is running
- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Create a new database named `Soudemy_Demo`
- Import the SQL file:
  ```sql
  -- In phpMyAdmin, select Soudemy_Demo database
  -- Click Import tab
  -- Choose file: soudemy_demo.sql
  -- Click Go
  ```

### 3. Configure Database Connection
- Navigate to `frontend/config/` folder
- Copy example configuration files:
  ```bash
  cp frontend/config/config.example.php frontend/config/config.php
  cp frontend/config/database.example.php frontend/config/database.php
  ```
- Edit `config.php` and `database.php` with your database credentials:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'Soudemy_Demo');
  define('DB_USER', 'root');
  define('DB_PASS', ''); // Your MySQL password
  ```

### 4. Setup Upload Directories
Create necessary directories for uploads:
```bash
mkdir -p uploads/avatars
mkdir -p frontend/uploads/avatars
chmod -R 755 uploads
chmod -R 755 frontend/uploads
```

### 5. Access the Website
- **Frontend**: `http://localhost/Do_an/frontend/`
- **Admin Panel**: `http://localhost/Do_an/backend/manager.php`

## 👤 Default Admin Account

After importing the database, use these credentials to access the admin panel:

- **Email**: `admin@soudemy.com`
- **Password**: `admin123`

**⚠️ Important**: Change the default admin password immediately after first login!

## 📁 Project Structure

```
Soudemy-Furniture-Store/
├── frontend/                # User-facing application
│   ├── config/             # Configuration files (gitignored)
│   │   ├── config.example.php
│   │   └── database.example.php
│   ├── css/                # Stylesheets
│   │   ├── styles.css
│   │   ├── auth.css
│   │   └── checkout.css
│   ├── js/                 # JavaScript files
│   │   ├── main.js
│   │   ├── cart.js
│   │   └── shop.js
│   ├── images/             # Product images
│   ├── includes/           # Reusable PHP components
│   │   ├── header.php
│   │   └── footer.php
│   ├── index.php           # Homepage
│   ├── shop.php            # Product listing
│   ├── cart.php            # Shopping cart
│   ├── checkout.php        # Checkout page
│   ├── profile.php         # User profile
│   └── ...                 # Other pages
├── backend/                # Admin panel
│   ├── api/                # API endpoints
│   ├── manager.php         # Admin dashboard
│   ├── manage_products.php # Product management
│   └── order_view.php      # Order management
├── uploads/                # User uploads (gitignored)
│   └── avatars/           # User avatars
├── database/               # SQL files
├── soudemy_demo.sql       # Initial database structure
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## 🔒 Security Notes

### Important Security Measures:

1. **Configuration Files**
   - Never commit `config.php` or `database.php` to Git
   - These files are listed in `.gitignore`
   - Always use `.example` files as templates

2. **Password Security**
   - Change default admin password immediately
   - Use strong passwords (minimum 8 characters)
   - Passwords are hashed using bcrypt

3. **File Uploads**
   - Upload directories are excluded from Git
   - Validate file types and sizes
   - Store uploads outside web root when possible

4. **Database Security**
   - Use prepared statements (already implemented)
   - Don't expose database credentials
   - Regular backups recommended

5. **Production Deployment**
   - Disable error display: `display_errors = Off`
   - Enable error logging: `log_errors = On`
   - Use HTTPS for all connections
   - Set appropriate file permissions

## 🎨 Product Categories

- 🛋️ Sofa - Modern and classic sofas
- 🪑 Table - Dining and coffee tables
- 💡 Lamp - Table lamps and floor lamps
- 🛏️ Bed - Queen and king size beds
- 📚 Bookshelf - Storage solutions

## 📱 Screenshots

### Frontend
- Homepage with product carousel
- Product listing with filters
- Shopping cart
- Checkout process
- User profile

### Backend
- Admin dashboard
- Product management
- Order management

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

**DucThanh21**

- GitHub: [@DucThanh21](https://github.com/DucThanh21)
- Project Link: [https://github.com/DucThanh21/Soudemy-Furniture-Store](https://github.com/DucThanh21/Soudemy-Furniture-Store)

## 🙏 Acknowledgments

- Font Awesome for icons
- Bootstrap for responsive framework
- All contributors and supporters

## 📞 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/DucThanh21/Soudemy-Furniture-Store/issues) page
2. Create a new issue if your problem isn't already listed
3. Provide detailed information about the error

---

⭐ If you find this project useful, please consider giving it a star!
