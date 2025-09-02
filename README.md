# Task Tracker CLI (PHP)

A simple **command-line task tracker** built in PHP.  
This tool allows you to add, update, delete, and manage tasks stored in a JSON file (`tasks.json`).

 Project Page

 [cd task-Tracker-cli](https://roadmap.sh/projects/task-tracker)

 
 Features
- Add new tasks with a description
- Update task descriptions
- Delete tasks
- Mark tasks as **in-progress** or **done**
- List tasks with optional filters (`all`, `todo`, `in-progress`, `done`)
- Data is stored in `tasks.json` (JSON format)

usage

php taskTrackercli.php add "Finish homework"
php taskTrackercli.php update [id] "New description"
php taskTrackercli.php delete [id]
php taskTrackercli.php mark-in-progress [id]
