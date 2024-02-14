---
title: Email, CRM and Salesforce
layout: default
nav_order: 4
---

# Email, CRM and Salesforce

Due to the nature of the Renewal SA websites, many are based around capturing user details for further communication.

There are pre-prescribed methods for how this information should be captured, and for setting up outgoing emails, mailing lists etc.

# Email

As a rule of thumb, most Renewal SA websites will not directly email 'customers' or members of the public. If using salesforce for all forms, then the only outgoing emails for the site will be password resets, user account notifications etc.

Renewal SA uses [Mailgun](https://www.mailgun.com/) as it's SMTP sending service of choice, and has a Renewal SA account. The [Mailgun WordPress plugin](https://wordpress.org/plugins/mailgun/) is provided as part of the template.

If the website is relatively small, and only sending user account related emails to admins, then it's suitable to use the 'generic' renewalsa email domain of `renewalsa.com.au`. This is a `.com.au` to avoice getting blocked by statenet filters, which will block `.sa.gov` emails coming in from outside the network.

To utilise the generic address, get the API key from Renewal SA's Mailgun account (or their IT Department), and use `websitename@renewalsa.com.au` as the "From" address in the Mailgun plugin setup.

If the website is using Gravity Forms, or notifies end consumers via email in some other manor, then authenticate and set up the site's domain on Mailgun and proceed with addresses from that domain.

# Mailing lists

Some projects still utilise Campaign Monitor for mailing lists. When implementing the subscription form here there are two options

- Direct form integration, submitting straight to Campaign Monitor
- Utilise Gravity Forms and the Campaign Monitor addon, avaiable on the Renewal SA Plugins repository

# Salesforce

New Renewal SA projects will most likely use Salesforce for communcation, CRM duties etc

When implementing Salesforce forms, it is reccomended to implement the web-to-lead form directly as HTML in the page or content module. 

Renewal SA does not reccomend proxying via API connections, Gravity Forms plugins etc as this can lead to unexpected outcomes.

{:.note}
It's highly reccomended to have a helper in your templates to quicky turn the Saleforce web-to-lead debug params on and off. Utilise a conditional statement to print out these additional hidden fields.

# Other Forms

If the website requires additional forms that aren't suitable for Saleforce, then the form plugin of choice is Gravity Forms. This is available via the Renewal SA Plugins repository, along with several add-ons relevant to services Renewal SA utilise.

```bash
# Install Gravity Forms via Composer
composer require renewalsa-plugins/gravityforms

# List available Gravity Forms addons
composer search renewalsa-plugins/gravity
```

By standardising on Gravity Forms we provide seamless admin user experience for people working in cross-project roles and when shifting to new projects.



