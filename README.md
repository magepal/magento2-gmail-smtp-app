## Gmail Smtp App for Magento2
Configure Magento 2 to send all transactional email using Google App, Gmail, Amazon Simple Email Service (SES) and other SMTP server. 

All you need is either a (i) free Gmail account, (ii) paid Google Apps account or any other SMTP service (i.e Amazon Simple Email Service / Amazon SES )

###Benefits
Since Google's SMTP server does not use Port 25, you'll reduce the probability that an ISP might block your email or flag it as SPAM. Also all your emails sent from Magento will be searchable and backed-up in your email account on Google's servers. 

####1 - Installation  Gmail Smtp App
##### Manual Installation
Install Gmail Smtp App for Magento2
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/MagePal/GmailSmtpApp
 * Copy the content from the unzip folder


#####Using Composer

```
composer config repositories.magepal-gmailsmtpapp git git@github.com:magepal/magento2-gmailsmtpapp.git
composer require magepal/magento2-gmailsmtpapp
```

####2 -  Enable Gmail Smtp App
 * php -f bin/magento module:enable --clear-static-content MagePal_GmailSmtpApp
 * php -f bin/magento setup:upgrade

####3 - Config Gmail Smtp App
Log into your Magento Admin, then goto Stores -> Configuration -> Advanced -> System -> Gmail/Google Apps SMTP Pro and enter your email credentials

## Preview
![image](https://cloud.githubusercontent.com/assets/1415141/18802388/7302402a-81b6-11e6-8c19-7a7f01be8743.png)
