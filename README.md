# ovh-cdn-ssl-renew
A simple PHP script that create/upload your OVH dedicated SSl certificats automatically, using OVH's apis
Actually it is designed to work with letsencrypt certificats, but can easily be modified to use any certs.

# Dependencies

PHP-cli 5.4+ with openssl module

[OVH php API](https://github.com/ovh/php-ovh)

# Usage

## Create your OVH API credentials for the project
Go to https://api.ovh.com/createToken/index.cgi?GET=/me

and set a new key using the following parameters :
```
Access :
     GET     /*
     POST    /*
     DELETE  /*
     POST    /*
Validity: unlimited
```
## Configure this script
Copy the config.sample.php and rename it as conf.php, and fill it using the credentials given at the previous step.

## Test it
Make the script executable :

`chmod +x cdn-certs.php`

Go:

`./cdn-certs.php`

If all goes good, you must now create a daily cronjob, to be sure your cert will be updated on time.
Simply put this script in the /etc/crond.daily directory or create a symlink to it

# Disclaimer
This script is provided as-is, you must understand what it does before using it.
