<a href="http://www.magepal.com" title="Magento Extensions" ><img src="https://image.ibb.co/dHBkYH/Magepal_logo.png" width="100" align="right" title="Magento Custom Modules" /></a>

# Magento 2 SMTP Extension - Gmail, G Suite, Amazon SES, Office360, Mailgun, SendGrid, Mandrill and other SMTP servers.

[![Total Downloads](https://poser.pugx.org/magepal/magento2-gmailsmtpapp/downloads)](https://packagist.org/packages/magepal/magento2-gmailsmtpapp)
[![Latest Stable Version](https://poser.pugx.org/magepal/magento2-gmailsmtpapp/v/stable)](https://packagist.org/packages/magepal/magento2-gmailsmtpapp)
[![GitHub stars](https://img.shields.io/github/stars/magepal/magento2-gmail-smtp-app.svg)](https://github.com/magepal/magento2-gmail-smtp-app/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/magepal/magento2-gmail-smtp-app.svg)](https://github.com/magepal/magento2-gmail-smtp-app/network)

##### For Magento 2.0.x, 2.1.x, 2.2.x and 2.3.x

Configure Magento 2 to send all transactional email using Google App, Gmail, Amazon Simple Email Service (SES), Microsoft Office365 or other SMTP server. 

Sending transactional emails to customers is a vital part of running an e-commerce store. Our free custom Magento extension integrates with all major email service provider and third-party SMTP server to reliably deliver messages in customers' inbox.

#### What is SMTP - Simple Mail Transfer Protocol
SMTP or Simple Mail Transfer Protocol allows you to send emails from your Magento 2 store through a specific third-party mail SMTP server. For example, if you want to use your Gmail, Amazon, Microsoft or any other mail server account to send email from your Magento web store, all you need is to configure that mail server settings in our extension in Magento without having to do any complex server configuration.

![Magento SMTP Email Extension](https://image.ibb.co/ecWinc/Mage_Pal_Magento_2_SMTP_Extension.gif)

#### Why use a Custom SMTP Server with Magento

By default, most hosting companies mail servers are configured to send email from unauthorized senders which prevent emails from reliable delivered to recipients. Therefore, most Magento store owners struggle to limit the number of transactional emails that end up in clients' junk mail. Take full control of your email sending settings in Magento 2 and reduce sending email to your valuable customers' junk mail folder. Emails are delivered instantaneously to their intended recipients without delays or get trap in the spam folder.

Out of the box, Magento 2 doesn't provide the ability to specify your custom SMTP settings for outgoing emails using an external SMTP server. Using this extension bridges the gap and allows your Magento store to connect to your preferred email provider securely and easily.

All you need is either a (i) free Gmail account, (ii) paid Google Apps account or any other SMTP service (i.e Amazon Simple Email Service / Amazon SES, Microsoft Office365). Learn more about our [custom SMTP](https://www.magepal.com/magento2/extensions/custom-smtp.html?utm_source=Custom%20SMTP&utm_medium=GitHub%20Learn%20More) extension.

### Benefits of using Gmail SMTP
Since Google's, Gmail and G Suite SMTP server does not use Port 25, you'll reduce the probability that an ISP might block your email or flag it as SPAM. Also all your emails sent from Magento will be searchable and backed-up in your email account on Google's servers. 

### Features
* Send email through virtually any external SMTP server from your Magento store
* Easily configure Magento 2 SMTP settings from within Magento2 store admin
* Complete control of custom SMTP server settings: Hostname, Port, Username, Password, ...
* Self test option, which lets you verify your email credentials are correct before saving 
* Support Multi-store, configurable different email providers/accounts per store
* Support secure SMTP servers: TLS / SSL, Plain-text, username/password, CRAM-MD5 authentication
* Customize email headers: From / Reply-To / Return-Path
* Disable/enable module from admin
* Developer Friendly
* Integrate with any third-party SMTP server

### SMTP Service Providers
 * Gmail
 * Google App
 * G Suite
 * Amazon Simple Email Service (SES)
 * Microsoft Office365
 * Outlook
 * SparkPost
 * Mandrill
 * MailGun
 * SendGrid
 * Elastic Email
 * Hotmail
 * AOL Mail
 * Yahoo Mail
 * AT&T
 * Verizon
 * Postmark
 * O2 Mail
 * Zoho
 * Mailjet
 * Mail.com
 * Your Company SMTP Server
 * and many other SMTP servers


## How to Install Magento SMTP Extension

#### Step 1

##### Using Composer (recommended)

```
composer require magepal/magento2-gmailsmtpapp
```

##### SMTP Manual Installation  (not recommended)
Install Gmail Smtp App for Magento2
 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/MagePal/GmailSmtpApp
 * Copy the content from the unzip folder
 * Flush cache

#### Step 2 -  Enable Magento 2 SMTP Extension
```
 php -f bin/magento module:enable --clear-static-content MagePal_GmailSmtpApp
 php -f bin/magento setup:upgrade
 php -f bin/magento setup:static-content:deploy
 php -f bin/magento cache:flush
```

#### Step 3 - How to Configure Magento Custom SMTP Setting
Log into your Magento Admin, then goto Stores -> Configuration -> MagePal -> SMTP Configuration and enter your email credentials

Contribution
---
Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


Support
---
If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/magepal/magento2-gmailsmtpapp/issues). For fast Premium Support visit our [Custom SMTP Extension](https://www.magepal.com/magento2/extensions/custom-smtp.html?utm_source=Custom%20SMTP&utm_medium=GitHub%20Premium%20Support) product page for detail.

Need help setting up or want to customize this extension to meet your business needs? Please email support@magepal.com and if we like your idea we will add this feature for free or at a discounted rate.

Other Extensions
---
[Custom SMTP](https://www.magepal.com/magento2/extensions/custom-smtp.html) | [Google Tag Manager](https://www.magepal.com/magento2/extensions/google-tag-manager.html) | [Enhanced E-commerce](https://www.magepal.com/magento2/extensions/enhanced-ecommerce-for-google-tag-manager.html) | [Google Universal Analytics](https://www.magepal.com/magento2/extensions/google-universal-analytics-enhanced-ecommerce.html) | [Reindex](https://www.magepal.com/magento2/extensions/reindex.html) | [Custom Shipping Method](https://www.magepal.com/magento2/extensions/custom-shipping-rates-for-magento-2.html) | [Preview Order Confirmation](https://www.magepal.com/magento2/extensions/preview-order-confirmation-page-for-magento-2.html) | [Guest to Customer](https://www.magepal.com/magento2/extensions/guest-to-customer.html) | [Admin Form Fields Manager](https://www.magepal.com/magento2/extensions/admin-form-fields-manager-for-magento-2.html) | [Customer Dashboard Links Manager](https://www.magepal.com/magento2/extensions/customer-dashboard-links-manager-for-magento-2.html) | [Lazy Loader](https://www.magepal.com/magento2/extensions/lazy-load.html) | [Order Confirmation Page Miscellaneous Scripts](https://www.magepal.com/magento2/extensions/order-confirmation-miscellaneous-scripts-for-magento-2.html)

© MagePal LLC. | [www.magepal.com](https://www.magepal.com)
