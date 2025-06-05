[中文说明](https://github.com/innocms/innocms/blob/master/README.zh-cn.md)

[![LICENSE](https://img.shields.io/badge/License-OSL%203.0-green.svg)](https://github.com/innocms/innocms/blob/master/LICENSE.txt)
[![Language](https://img.shields.io/badge/Language-php-blue.svg)](https://www.php.net/)
[![Leaderboard](https://img.shields.io/badge/InnoCMS%20-orange)](https://www.innocms.com/)

## Open Source E-commerce InnoShop Has Been Released: [InnoShop](https://github.com/innocommerce/innoshop)

# InnoCMS
Lightweight Enterprise Official Website CMS

Creating an enterprise official website has never been easier! Our lightweight CMS is designed for rapid development and launch, allowing your official website to go from concept to reality in just a few steps.

Ease of use and efficiency coexist, allowing your team to get started easily and quickly master.

Join us now to experience unprecedented development speed and convenience, and let your enterprise shine on the Internet!

## Documentation
For detailed documentation, please visit: [InnoCMS Documentation Center](http://docs.innoshop.cn/en)

## Demo Site
- Frontend: https://demo.innocms.com/
- Backend: https://demo.innocms.com/panel
- Account: admin@innocms.com
- Password: 123456

### Demo Frontend Screenshot
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/front.jpg?" alt="Front">
    </a>
</p>

### Demo Backend Screenshot
<p align="center">
    <a href="https://www.innocms.com" target="_blank">
        <img src="https://www.innocms.com/images/readme/panel.jpg?" alt="Panel">
    </a>
</p>

## Installation Instructions
### 1. Choose a PHP Environment:
You can choose from integrated environments such as Xiaopi, Baota, XAMPP, etc., which are very convenient. If you prefer customization, compiling and installing PHP yourself is also great.

Version requirements: PHP: `8.2` or above, MySQL: `5.7` or `8.0`

### 2. Set the Website Root Directory:
Next, let's point your website's root directory to the current project's `public` folder and configure the pseudo-static settings.

### 3. Install Third-Party Dependencies:
Open the command-line tool, navigate to your current project directory, and then enter `composer install`. This will help us install the required third-party packages.

### 4. Create a Configuration File:
Copy a configuration file as a starting point by running the following command: `cp .env.example .env`. This way, you will have an initial configuration file.

### 5. Configure the Database:
Open the `.env` file, find the lines starting with `DB_`, and fill in your database configuration information.

### 6. Generate a System Key:
Run the following command to generate a secure key for the system: `php artisan key:generate`

### 7. Database Migration and Data Seeding:
Execute the following command to create the database structure and import the basic data: `php artisan migrate && php artisan db:seed`

### 8. Upload Resource Directory Settings:
Run the following command to create the upload resource directory: `php artisan storage:link`

### 9. Frontend Resource Compilation:
Finally, run the following command to compile the frontend's CSS and JavaScript files: `npm install && npm run prod`

### 10. Log in to the Website Backend for Configuration:

To access your website's backend, please use your website's specific backend address.

For example, if your website address is `example.com`, then the backend access address is `example.com/panel`.

When logging in, please use your administrator account and password. The initial login credentials are usually:

- Email: admin@innocms.com
- Password: 123456

After entering your login information, you can enter the backend for configuration.

We wish you a smooth installation process. If you have any questions or difficulties during installation or use, please feel free to join our QQ communication group, the group number is: 960062283

Our team and enthusiastic users will provide you with real-time help and support.

## Development Instructions
### 1. Independent Release Reminder:
Please note that the three folders under the `/innopacks` directory will be released as independent Composer packages after the v1.0 version is released.

Therefore, we recommend that you avoid directly editing the files in that directory.

### 2. Frontend Development Guide:
If you are developing the frontend for the second time, you can publish the required view files by executing the following command:

```
php artisan inno:publish-theme
```

After this operation, the system will automatically generate the corresponding template files for you in the `/resources/views/vendor` directory.

### 3. Backend Development Guide:
Similarly, backend secondary development can also obtain template files through the following command:

```
php artisan vendor:publish --provider="InnoCMS\Panel\PanelServiceProvider" --tag=views
```

After executing the command, you will find the template files required for the backend in the same directory.

### 4. Invitation to Free Play:
Now, you can boldly develop and customize in this safe environment without worrying about damaging the original code.

### 5. Error Correction Tips:
If you accidentally make a wrong modification, don't worry! Just delete those modified files, and the system will automatically restore to its original state.

### 6. Backend Development:
Our system offers a plugin development mechanism based on `hooks`, which allows you to customize your development experience flexibly without the need to touch the core code of the system. 
You can refer to the `PartnerLink` plugin located in the `/plugins` directory to understand how to develop plugins, so that you can enjoy the convenience and fun of free customization.

We advise that all secondary development features be implemented using a plugin model. This approach not only facilitates future official system upgrades but also helps to better organize your code, 
preventing the chaotic accumulation known colloquially as "code mountains."

### Please look forward to the innovation and convenience we bring!
- If you find `InnoCMS` helpful to you, please do not hesitate to give us a star.
- Every like from you is the driving force for our continuous improvement.