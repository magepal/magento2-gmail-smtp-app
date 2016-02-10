## Gmail Smtp App for Magento2
Configure Magento to use Google's SMTP server to send all transactional email. 

All you need is either a (i) free Gmail account or (ii) paid Google Apps account..

###Benefits
Since Google's SMTP server does not use Port 25, you'll reduce the probability that an ISP might block your email or flag it as SPAM. Also all your emails sent from Magento will be searchable and backed-up in your email account on Google's servers. 




### Usage
#### Manual Installation
Install Gmail Smtp App for Magento2
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/MagePal/GmailSmtpApp
 * Copy the content from the unzip folder

 * php -f bin/magento module:enable --clear-static-content MagePal_GmailSmtpApp
 * php -f bin/magento setup:upgrade

#### Using Composer

``composer config repositories.magepal-gmailsmtpapp git git@github.com:magepal/magento2-gmailsmtpapp.git``
``composer require magepal/magento2-gmailsmtpapp:master``

Log into your Magetno Admin, then goto Store -> System -> Gmail/Google Apps SMTP Pro and enter your email credentials
