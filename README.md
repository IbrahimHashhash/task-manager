# 🔷 Project Title:
Task Management System

## 🔷 Technologies Used:
**Frontend**: HTML, CSS  
**Backend**: PHP  
**Database**: MySQL

## 🔷 Purpose of the Project:
To create a web-based system that allows organizations to manage their projects, tasks, teams, and employee contributions efficiently.

## 🔷 Main Users (Roles):
- **Manager**  
- **Project Leader**  
- **Team Member**

Each role has different functionalities and permissions in the system.

## 🔷 Core Features:
### ✅ Authentication:
- Secure login system.
- Session-based role detection (Manager, Project Leader, Team Member).

### ✅ Project Management (Manager):
- Add new projects.
- Assign projects to project leaders.

### ✅ Task Management (Project Leader):
- Create tasks for assigned projects.
- Assign team members to tasks.
- Define contribution percentages (must not exceed 100%).

### ✅ Team Member Dashboard:
- View assigned tasks and their contribution.
- Submit task status updates.

## 🔷 Navigation System:
- Uses a single PHP file (`navigation.php`) that:
  - Detects role using sessions.
  - Dynamically includes role-specific pages (e.g., `create_task.php`, `assign_team_members.php`, etc.).
  - Uses query strings like `?action=create_task` to determine which component to show.

## 🔷 Data Validation & Logic Handling:
- Validates form input (e.g., total contribution should not exceed 100%).
- Ensures only appropriate roles can access specific actions.
- Feedback messages for errors and successes.

## 🔷 Design Philosophy:
- Role-based access and interfaces.
- Reusable and modular code.
- Navigation remains consistent while content dynamically changes.


![image](https://github.com/user-attachments/assets/87bc1a3b-122b-43a1-a827-3a358f5c8bfb)
![image](https://github.com/user-attachments/assets/e8249436-de1d-4ef1-babe-1df67693abe5)
![image](https://github.com/user-attachments/assets/859996e0-1554-415d-bb08-d50b1546e2b8)
![image](https://github.com/user-attachments/assets/e5dfc208-681b-4195-94eb-76abe3033686)
![image](https://github.com/user-attachments/assets/52ae9027-5d29-4922-aeae-ffc47b36a744)
![image](https://github.com/user-attachments/assets/d59123b0-f7fd-40a1-95f2-de6c10c80b86)
![image](https://github.com/user-attachments/assets/5a5c03b3-744d-4352-a6b5-4fca00c1026c)
![image](https://github.com/user-attachments/assets/9907965e-e4d1-4f1e-9642-446739c2ca92)
