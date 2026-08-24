# cPanel Deployment

The cPanel Git repository is:

`/home/cloudweb/repositories/masterstudy`

The live WordPress folder is:

`/home/cloudweb/public_html/masterstudy`

The tracked `.cpanel.yml` file deploys only the WordPress code that belongs in `wp-content`:

- `wp-content/themes/masterstudy`
- `wp-content/plugins/masterstudy-lms-learning-management-system`
- `wp-content/plugins/course-automation-publisher`

It does not deploy `wp-config.php`, database exports, uploads, generated videos, generated lesson images, or Dropbox source files.

## Server Setup

Before deploying code, install WordPress in:

`/home/cloudweb/public_html/masterstudy`

Use the cPanel database details created for the site, including database name:

`cloudweb_masterstudy`

Do not commit the database password to Git.

## Manual cPanel Deployment

Use this when cPanel pulls from GitHub:

1. Open cPanel Git Version Control.
2. Open the `masterstudy` repository.
3. Go to `Pull or Deploy`.
4. Click `Update from Remote`.
5. Click `Deploy HEAD Commit`.

## Direct Push Deployment

Use this when SSH access from the local machine to cPanel is configured:

```powershell
cd C:\laragon\www\lms-masterstudy\_project
git remote add cpanel ssh://cloudweb@cloudwebsolutions.agency/home/cloudweb/repositories/masterstudy
git push cpanel main
```

After the push, cPanel runs `.cpanel.yml` and copies the WordPress code into the live folder.
