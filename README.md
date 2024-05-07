# Project Installation Guide

Follow these steps to set up and run the project locally:

1. **Clone the Repository**: 

2. **Install Dependencies**: 

3. **Create Database**: 
- Open phpMyAdmin on your local server.
- Create a new database named `task_management`.

4. **Configure Environment Variables**:
- Copy the `.env.example` file to `.env`.
- Update the `DB_DATABASE` variable in the `.env` file with `task_management`.

5. **Run Migrations**: 

6. **Seed Database**: 
This will populate the `users` table with default data.

7. **Import Postman Collection**: 
- Sent on linkedin.
- Import the collection into Postman.

8. **Set Postman Environment Variable**:
- In Postman, create a new environment or select an existing one.
- Add a variable named `tm_token`.

9. **Start Local Server**: 
This will start the local server.

10. **Login with Test Credentials**:
- Use the following credentials to log in as the first user:
  - Email: test+1@example.com
  - Password: password
