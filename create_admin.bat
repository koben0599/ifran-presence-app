@echo off
echo Création de l'administrateur...
sqlite3 database/database.sqlite ".read insert_admin_sqlite.sql"
echo.
echo Administrateur créé avec succès!
echo Email: admin@ifran.com
echo Mot de passe: password
pause 