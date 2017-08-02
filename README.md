## Smtp App for Magento2

[![Total Downloads](https://poser.pugx.org/magepal/magento2-gmailsmtpapp/downloads)](https://packagist.org/packages/magepal/magento2-gmailsmtpapp)
[![Latest Stable Version](https://poser.pugx.org/magepal/magento2-gmailsmtpapp/v/stable)](https://packagist.org/packages/magepal/magento2-gmailsmtpapp)

Configure Magento 2 to send all transactional email using Google App, Gmail, Amazon Simple Email Service (SES), Microsoft Office365 and other SMTP server. 

All you need is either a (i) free Gmail account, (ii) paid Google Apps account or any other SMTP service (i.e Amazon Simple Email Service / Amazon SES, Microsoft Office365 )

### Benefits
Since Google's SMTP server does not use Port 25, you'll reduce the probability that an ISP might block your email or flag it as SPAM. Also all your emails sent from Magento will be searchable and backed-up in your email account on Google's servers. 

### SMTP Service Providers
 * Gmail
 * Google App
 * Amazon Simple Email Service (SES)
 * Microsoft Office365
 * SparkPost
 * Mandrill
 * MailGun
 * SendGrid
 * Elastic Email
 * Hotmail
 * Postmark
 * O2 Mail
 * Zoho
 * Mailjet
 * Mail.com
 * Your Company SMTP Server
 * and many other SMTP servers


#### 1 - Installation  Gmail Smtp App
##### Manual Installation
Install Gmail Smtp App for Magento2
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/MagePal/GmailSmtpApp
 * Copy the content from the unzip folder
 * Flush cache


##### Using Composer (from Magento Root Dir run)

```
composer require magepal/magento2-gmailsmtpapp
```

#### 2 -  Enable Gmail Smtp App
```
 php -f bin/magento module:enable --clear-static-content MagePal_GmailSmtpApp
 php -f bin/magento setup:upgrade
 php -f bin/magento setup:static-content:deploy
 php -f bin/magento cache:flush
```

#### 3 - Config Gmail Smtp App
Log into your Magento Admin, then goto Stores -> Configuration -> Advanced -> System -> Gmail/Google Apps SMTP Pro and enter your email credentials

## Preview
![image](https://user-images.githubusercontent.com/1415141/28881062-ecfa0cf0-7774-11e7-9af7-52824e5f9568.png)

