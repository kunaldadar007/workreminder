# Work Reminder and Chat Bot System

A comprehensive task management system with automated reminders and AI-powered chatbot assistance, developed as a B.Sc Computer Science final year project.

## 🚀 Features

### Core Features
- **User Authentication**: Secure registration, login, and logout system with password hashing
- **Task Management**: Create, read, update, and delete daily/monthly tasks
- **Smart Reminders**: Browser notifications and sound alerts for task reminders
- **AI Chatbot**: Intelligent assistant that answers questions about tasks using NLP rules
- **Dashboard**: Overview with statistics, today's tasks, and calendar preview
- **Admin Panel**: Complete user and task management interface

### Technical Features
- **Responsive Design**: Mobile, tablet, and desktop compatible
- **Glassmorphism UI**: Modern Netflix-style dark theme with glass effects
- **Real-time Notifications**: Background timer checks every 30 seconds
- **RESTful APIs**: Clean API architecture for all operations
- **Database Logging**: Comprehensive logging of user actions and chatbot interactions

## 🛠️ Technology Stack

### Frontend
- **HTML5**: Semantic markup and structure
- **CSS3**: Glassmorphism design with animations and transitions
- **JavaScript (ES6)**: Modern JavaScript with async/await and fetch API
- **Responsive Design**: Mobile-first approach with media queries

### Backend
- **PHP 7.4+**: Server-side logic and API endpoints
- **MySQL 5.7+**: Database with optimized queries and relationships
- **PDO**: Secure database operations with prepared statements
- **Sessions**: User authentication and state management

## 📁 Project Structure

```
Workreminder/
├── config/
│   ├── database.php      # Database configuration
│   └── config.php        # Application settings
├── api/
│   ├── auth.php          # Authentication API
│   ├── task.php          # Task management API
│   ├── tasks.php         # Tasks fetch API
│   ├── chatbot.php       # Chatbot API
│   └── check_reminders.php # Reminder checker API
├── admin/
│   ├── dashboard.php     # Admin dashboard
│   ├── users.php        # User management
│   └── tasks.php        # Task management (admin)
├── assets/
│   ├── css/
│   │   └── style.css    # Main stylesheet
│   ├── js/
│   │   └── app.js       # Main JavaScript application
│   └── sounds/
│       └── notification.mp3 # Notification sound
├── pages/
│   ├── index.php        # Landing page
│   ├── login.php        # Login page
│   ├── register.php     # Registration page
│   ├── dashboard.php    # User dashboard
│   └── chatbot.php      # Chatbot interface
├── database.sql         # Database schema and sample data
└── README.md           # This file
```

## 🚀 Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser with JavaScript enabled

### Step 1: Database Setup
1. Create a MySQL database named `work_reminder_db`
2. Import the provided `database.sql` file:
   ```sql
   mysql -u root -p work_reminder_db < database.sql
   ```

### Step 2: Configuration
1. Update database credentials in `config/database.php`:
   ```php
   private $host = "localhost";
   private $db_name = "work_reminder_db";
   private $username = "root";
   private $password = "your_password";
   ```

2. Update base URL in `config/config.php`:
   ```php
   define('BASE_URL', 'http://localhost/workreminder/');
   ```

### Step 3: Web Server Setup
1. Place the project files in your web server's document root
2. Ensure the following directories are writable by the web server:
   - `assets/sounds/` (for notification sounds)
3. Enable PHP extensions:
   - PDO and PDO_MySQL
   - Session support
   - JSON support

### Step 4: Access the Application
1. Open your web browser and navigate to: `http://localhost/workreminder/`
2. Register a new account or login with existing credentials
3. Default admin account:
   - Username: `admin`
   - Password: `admin123`

## 📱 Usage Guide

### For Users
1. **Registration**: Create an account with username, email, and password
2. **Login**: Access your personalized dashboard
3. **Task Management**: 
   - Add new tasks with title, description, date, time, and priority
   - View today's tasks, upcoming tasks, and completed tasks
   - Mark tasks as complete or delete them
4. **Reminders**: Enable browser notifications to receive task alerts
5. **Chatbot**: Ask questions like:
   - "What tasks do I have today?"
   - "Show me my monthly schedule"
   - "How many tasks have I completed?"

### For Administrators
1. **Admin Dashboard**: Overview of system statistics and recent activity
2. **User Management**: View, activate/deactivate, and delete users
3. **Task Management**: Monitor all tasks across the system
4. **System Logs**: View chatbot interactions and admin actions

## 🤖 Chatbot Commands

The AI chatbot understands natural language queries such as:

### Task Queries
- "What tasks do I have today?"
- "Show me tomorrow's tasks"
- "What's my monthly schedule?"
- "List my upcoming tasks"
- "How many tasks do I have?"
- "Show me completed tasks"
- "What pending tasks do I have?"
- "What are my high priority tasks?"

### General Commands
- "Help" - Shows available commands
- "Hi/Hello" - Greeting responses
- "Remind me about..." - Reminder setup

## 🔧 Configuration Options

### Notification Settings
Edit `config/config.php` to customize:
- Reminder check interval (default: 30 seconds)
- Notification sound file path
- Session lifetime

### UI Customization
Modify `assets/css/style.css` to:
- Change color scheme
- Adjust glassmorphism effects
- Customize animations and transitions

## 🛡️ Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **SQL Injection Prevention**: All database queries use prepared statements
- **XSS Protection**: User inputs are sanitized using `htmlspecialchars()`
- **Session Security**: Secure session management with timeout
- **Role-based Access**: Admin-only areas are protected

## 📊 Database Schema

### Tables
1. **users**: User accounts and authentication
2. **tasks**: Task information and scheduling
3. **chatbot_logs**: Chatbot interaction history
4. **admin_logs**: Administrative action logs

### Relationships
- Users have many tasks (one-to-many)
- Tasks belong to users (many-to-one)
- Chatbot logs belong to users
- Admin logs track administrative actions

## 🔄 API Endpoints

### Authentication
- `POST /api/auth.php` - Login, register, logout

### Task Management
- `GET /api/tasks.php` - Fetch user tasks
- `POST /api/task.php` - Create new task
- `PUT /api/task.php` - Update task
- `DELETE /api/task.php` - Delete task

### Chatbot
- `POST /api/chatbot.php` - Process chatbot queries

### Reminders
- `GET /api/check_reminders.php` - Check for pending reminders

## 🎨 UI/UX Features

### Glassmorphism Design
- Frosted glass effect with backdrop filters
- Smooth animations and transitions
- Dark theme with vibrant accent colors
- Responsive grid layouts

### User Experience
- Intuitive navigation with sidebar menu
- Real-time task updates without page refresh
- Interactive chatbot with typing indicators
- Mobile-optimized touch interactions

## 🚀 Performance Optimizations

- **Database Indexing**: Optimized queries for faster retrieval
- **Lazy Loading**: Tasks loaded on-demand
- **Caching**: Session-based caching for frequently accessed data
- **Minified Assets**: Optimized CSS and JavaScript files
- **Responsive Images**: Efficient image loading

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL server is running
   - Verify database exists and is accessible

2. **Notifications Not Working**
   - Enable browser notifications in browser settings
   - Check notification sound file exists in `assets/sounds/`
   - Ensure HTTPS is used (required for notifications in some browsers)

3. **Chatbot Not Responding**
   - Check browser console for JavaScript errors
   - Verify API endpoints are accessible
   - Ensure user is logged in

4. **Admin Panel Access Denied**
   - Verify user role is set to 'admin' in database
   - Check session is properly initialized
   - Clear browser cookies and login again

## 📈 Future Enhancements

- Email notifications for task reminders
- Task categories and labels
- File attachments for tasks
- Team collaboration features
- Mobile app (React Native)
- Advanced analytics and reporting
- Integration with calendar applications
- Voice commands for chatbot
- Task templates and recurring tasks

## 👥 Development Team

This project was developed by a team of 10 senior full-stack developers as a B.Sc Computer Science final year project.

## 📄 License

This project is for educational purposes. Feel free to use and modify for learning.

## 📞 Support

For technical support or questions about this project, please refer to the documentation or contact the development team.

---

**Note**: This is a comprehensive academic project demonstrating modern web development practices, database design, and user experience principles.
