# Airbnb Clone - Access Guide
 
If you are getting a **404 Not Found** error, please check the following:
 
## 1. Correct URL
Based on your `sftp.json` configuration, your files are uploaded to a folder named `AIRBNB CLONE`. 
The space in the folder name requires you to use `%20` in the URL:
 
**Try this URL:** `http://your-domain.com/AIRBNB%20CLONE/`
(Replace `your-domain.com` with your actual server IP or domain)
 
## 2. Sync Status
Make sure all files have been uploaded to the server. You should see the following files in your `public_html/AIRBNB CLONE/` folder:
- `index.php`
- `search.php`
- `details.php`
- `book.php`
- `confirmation.php`
- `db.php`
- `.htaccess`
 
## 3. Database Check
Ensure you have imported `database.sql` into the database `rsoa_rsoa278_16`. If the tables are missing, the app might redirect or fail (though it usually won't give a 404).
 
## 4. Recommended Fix
I recommend renaming your local and remote folder from `AIRBNB CLONE` to something without spaces, like `airbnb`. This makes URLs much easier to type.
 
