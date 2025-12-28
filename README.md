📝 Blog CMS - Console Edition
🎯 Project Overview
A command-line Content Management System for blog management built with pure PHP OOP (no frameworks). A 5-day development project with personas, permissions matrix, business rules, and functional requirements.

👥 Personas & Permissions
Persona Role Permissions
Amina Admin Full system control + user management
Thomas Editor Manage all articles + categories + comments
Léa Author Create/edit own articles only
Marco Visitor Read-only access
📋 Business Rules Implemented
✅ RB-001: One role per user (Visitor, Author, Editor, Admin)

✅ RB-005: Article belongs to one author (1:N relationship)

✅ RB-006: Article must have ≥1 category

✅ RB-007: Article can be in multiple categories

✅ RB-008: Only original author can edit/delete (exceptions: Editors/Admins)

✅ RB-011: Article lifecycle: Draft → Published → Archived

✅ RB-012: Publication date auto-sets on publish

✅ RB-017: Unique email & username

✅ RB-018: Hashed passwords

✅ RB-019: Only Admins manage users

✅ RB-020: Cannot delete self

✅ RB-021: All entities need unique ID

✅ RB-022: Auto-created dates

🏗️ System Architecture
text
BlogCMS/
├── User (Abstract Base Class)
│ ├── Visitor (Read-only)
│ ├── Author (Create/edit own articles)
│ ├── Editor (Manage all content)
│ └── Admin (Full system control)
├── Article
│ └── Comment (Nested under articles)
└── Category
🚀 Features
🔐 Authentication System
4 pre-defined demo accounts (one for each role)

Role-based access control

Password hashing for security

📰 Article Management
Create: Authors/Editors/Admins can create articles

Edit: Authors (own), Editors/Admins (all)

Delete: Same permissions as edit

Publish/Archive: Change article status

Search: Basic title/content search

Filter: By status, author, category, date

💬 Comment System
Add comments below articles

Moderation system: pending/approved/spam statuses

Role-based visibility: Visitors see approved, Editors/Admins see all

Full management: Edit, delete, change status of any comment

🗂️ Category Management
Create and assign categories to articles

Articles require at least 1 category

Category assignment during article creation

👥 User Management (Admin Only)
List all users with article counts

Change user roles

Delete users (with article handling options)

🎮 How to Run
bash

# 1. Make sure you have PHP installed

php --version

# 2. Clone or download the project

# 3. Navigate to project directory

cd blog-cms

# 4. Run the application

php CLI.php
📝 Demo Login Credentials
The system includes 4 pre-defined accounts:

Username Role Password Description
marco Visitor pass123 Read-only access
lea Author pass123 Can create/edit own articles
thomas Editor pass123 Manage all content
amina Admin pass123 Full system control
🖥️ User Interface
The system uses a clean CLI interface with role-specific menus:

text
=== ADMIN MENU ===
User: amina

---

ARTICLES:

1. List All Articles
2. Search Articles
3. Filter Articles
4. Create New Article
5. View/Edit My Articles

COMMENTS: 6. View Articles with Comments 7. Manage Comments

SYSTEM: 8. Manage Categories 9. Manage Users 0. Logout
📊 Sample Data Included
The system comes with sample data:

4 users (one for each role)

3 categories: Tech, Sports, Food

1 published article with comments

2 comments (one approved, one pending)

🛠️ Technical Implementation
OOP Principles Applied
Inheritance: User → Author/Editor/Admin/Visitor

Encapsulation: Private properties with getters/setters

Polymorphism: Role-specific behavior through inheritance

Composition: Articles have Categories and Comments

Design Patterns
Factory Pattern: User creation based on role

Observer Pattern: Comment notification system (conceptual)

Singleton Pattern: Single instance of BlogCMS

Security Features
Password hashing with password_hash()

Input sanitization

Role-based permission checks

Session management in CLI

📁 File Structure
text
blog-cms/
├── mediapress.php # All class definitions
├── CLI.php # Main application logic
├── README.md # This file
└── blog_data.json # Data persistence (if enabled)
🔧 Extending the Project
Easy Improvements
Add file persistence - Save data between sessions

Enhanced validation - Better input checking

Export functionality - Export articles to PDF/HTML

Statistics - View usage statistics

Advanced Features
Subcategories - Hierarchical category system

Email notifications - Notify authors of comments

Image upload - Add images to articles

API endpoints - REST API for web integration

🐛 Known Limitations
Data is stored in memory (resets on restart)

Basic input validation

No pagination for large lists

Simple CLI interface (no advanced formatting)

📚 Learning Outcomes
This project demonstrates:

Object-Oriented Programming in PHP

User authentication and authorization

CLI application development

Data modeling and relationships

Business rule implementation

Comment system architecture

👨‍💻 Developer Notes
"This project was built as a learning exercise to understand PHP OOP principles, user permission systems, and content management architecture. While it has limitations, it serves as a solid foundation for a fully-featured CMS."

📄 License
Educational Project - Free to use and modify

🙏 Acknowledgments
Built as part of a 5-day intensive PHP training

Special thanks to the formateur for guidance

Inspired by real-world blog platforms
