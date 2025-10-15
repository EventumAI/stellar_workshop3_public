# 🧰 Preparation Setup

Before we begin the workshop, we need to install and run two CMS applications — one based on CakePHP 3 and another on CakePHP 5.
During the session, we’ll be learning how to migrate components between these two environments.

⸻

## 🪄 Step 1. Run the CakePHP 3 Application
1. Open your terminal and navigate to the project folder: `cd quickapps-cakephp3`
2. Start the Docker environment: `docker compose up --build`  
3. The setup scripts inside the container will automatically:
   * install all required dependencies;  
   * load the initial database dump.

## ⚙️ Step 2. Run the CakePHP 5 Application
1. In a new terminal window, go to the CakePHP 5 project: `cd quickapps-cakephp5`
2. Run the same command: `docker compose up --build`

## 🌐 Step 3. Verify Everything Works
After both applications start successfully:  
* Visit http://localhost:8080/ → you should see the CakePHP 3 CMS home page.
* Visit http://localhost:8090/ → you should see the CakePHP 5 CMS home page.

## 🧩 Troubleshooting
If something doesn’t work or you encounter an error — don’t worry!  
Message me directly in the Teams chat — I usually respond quickly and will help you resolve any setup issue.