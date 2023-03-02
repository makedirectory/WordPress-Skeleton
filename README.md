**This project is no longer actively maintained.**

# Deploying WordPress on Elastic Beanstalk
Use the EB CLI to create an Elastic Beanstalk environment with an attached RDS DB and EFS file system to provide WordPress with a MySQL database and shared storage for uploaded files.

NOTE: Amazon EFS is not available in all AWS regions. Check the [Region Table](https://aws.amazon.com/about-aws/global-infrastructure/regional-product-services/) to see if your region is supported.

You can also run the database outside of the environment to decouple compute and database resources. See the Elastic Beanstalk Developer Guide for a tutorial with instructions that use an external DB instance: [Deploying a High-Availability WordPress Website with an External Amazon RDS Database to Elastic Beanstalk](https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/php-hawordpress-tutorial.html).

These instructions were tested with WordPress 5.5.1

This was created by combining the following repos and update the deploy script
- [markjaquith/WordPress-Skeleton](https://github.com/markjaquith/WordPress-Skeleton)
- [aws-samples/eb-php-wordpress](https://github.com/aws-samples/eb-php-wordpress)

## Assumptions

* WordPress as a Git submodule in `/wp/`
* Custom content directory in `/content/` (cleaner, and also because it can't be in `/wp/`)
* `wp-config.php` in the root (because it can't be in `/wp/`)
* All writable directories are symlinked to similarly named locations under `/shared/`.


## Install the EB CLI

The EB CLI integrates with Git and simplifies the process of creating environments, deploying code changes, and connecting to the instances in your environment with SSH. You will perform all of these activites when installing and configuring WordPress.

If you have pip, use it to install the EB CLI.

```Shell
$ pip install --user --upgrade awsebcli
```

Add the local install location to your OS's path variable.

###### Linux
```Shell
$ export PATH=~/.local/bin:$PATH
```
###### OS-X
```Shell
$ export PATH=~/Library/Python/3.4/bin:$PATH
```
###### Windows
Add `%USERPROFILE%\AppData\Roaming\Python\Scripts` to your PATH variable. Search for **Edit environment variables for your account** in the Start menu.

If you don't have pip, follow the instructions [here](http://docs.aws.amazon.com/elasticbeanstalk/latest/dg/eb-cli3-install.html).

## Set up your project directory

1. Clone the repo `git clone https://github.com/makedirectory/WordPress-EB-Skeleton`

2. Modify 06-wordpress.config
  - Set Username
  - Set Password (or comment out to generate random password)
  - Set WP email
  - Set WP Host (Domain)
  - Set WP Title

## Create an Elastic Beanstalk environment

1. Configure a local EB CLI repository with the PHP platform. Choose a [supported region](http://docs.aws.amazon.com/general/latest/gr/rande.html#elasticbeanstalk_region) that is close to you.

        `~/WordPress-EB-Skeleton$ eb init --platform "PHP 7.3 running on 64bit Amazon Linux" --region us-east-1`

2. Configure SSH. Create a key that Elastic Beanstalk will assign to the EC2 instances in your environment to allow you to connect to them later. You can also choose an existing key pair if you have the private key locally.

        ```
        Do you want to set up SSH for your instances?
        (y/n): y

        Select a keypair.
        1) [ Create new KeyPair ]
        (default is 1): 1

        Type a keypair name.
        (Default is aws-eb): WordPress-EB-Skeleton
        ```

3. Create an Elastic Beanstalk environment with a MySQL database.

        ```
        ~/wordpress-beanstalk$ eb create WordPress-EB-Skeleton --database --elb-type application
        Enter an RDS DB username (default is "ebroot"):
        Enter an RDS DB master password:
        Retype password to confirm:
        Environment details for: WordPress-EB-Skeleton
          Application name: WordPress-EB-Skeleton
          Region: us-east-1
          Deployed Version: Sample Application
          Environment ID: e-nrx24yzgmw
          Platform: 64bit Amazon Linux 2016.09 v2.2.0 running PHP 7.0
          Tier: WebServer-Standard
          CNAME: UNKNOWN
          Updated: 2016-11-01 12:20:27.730000+00:00
        Printing Status:
        INFO: createEnvironment is starting.
        ```

This will create and deploy the application from the current git head. You can create a new environment with a RDS instance via `eb create WordPress-EB-Skeleton2 --database --elb-type application`.

## Networking configuration
Modify the configuration files in the .ebextensions folder with the IDs of your [default VPC and subnets](https://console.aws.amazon.com/vpc/home#subnets:filter=default), and [your public IP address](https://www.google.com/search?q=what+is+my+ip).

 - `.ebextensions/01-efs-create.config` creates an EFS file system and mount points in each Availability Zone / subnet in your VPC. Identify your default VPC and subnet IDs in the [VPC console](https://console.aws.amazon.com/vpc/home#subnets:filter=default). If you have not used the console before, use the region selector to select the same region that you chose for your environment.

## Re-Deploy WordPress to your environment
Deploy the project code to your Elastic Beanstalk environment.

First, confirm that your environment is `Ready` with `eb status`. Environment creation takes about 15 minutes due to the RDS DB instance provisioning time.

    ```
    ~/wordpress-beanstalk$ eb status
    ~/wordpress-beanstalk$ eb deploy
    ```

## After Create/Deploy

Open your site in a browser.

    ```
    ~/wordpress-beanstalk$ eb open
    ```

A standard installation was performed during deploy via [wp-cli](https://wp-cli.org/). The `wp-config.php` file is already present in the source code and configured to read database connection information from the environment, so you should land on the WordPress homepage on your first visit.

## Updating keys and salts

The WordPress configuration file `wp-config.php` also reads values for keys and salts from environment properties. Currently, these properties are all set to `test` by the `06-wordpress.config` configuration file in the `.ebextensions` folder

The hash salt can be any value but shouldn't be stored in source control. Use `eb setenv` to set these properties directly on the environment.

    AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_KEY, AUTH_SALT, SECURE_AUTH_SALT, NONCE_SALT

    ```
    ~/wordpress-beanstalk$ eb setenv AUTH_KEY=29dl39gksao SECURE_AUTH_KEY=ah24h3drfh LOGGED_IN_KEY=xmf7v0k27d5fj3 ...
    ...
    ```

Setting the properties on the environment directly by using the EB CLI or console overrides the values in `06-wordpress.config`.

## Backup

Now that you've gone through all the trouble of installing your site, you will want to back up the data in RDS and EFS that your site depends on. See the following topics for instructions.

 - [DB Instance Backups](http://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/Overview.BackingUpAndRestoringAmazonRDSInstances.html)
 - [Back Up an EFS File System](http://docs.aws.amazon.com/efs/latest/ug/efs-backup.html)

## Questions & Answers

**Q:** Why the `/shared/` symlink stuff for uploads?  
**A:** For local development, create `/shared/` (it is ignored by Git), and have the files live there. For production, have your deploy script symlink `/shared/` to some outside-the-repo location (like an NFS shared directory or something). This gives you separation between Git-managed code and uploaded files.

**Q:** What version of WordPress does this track?  
**A:** The latest stable release. It should automatically update within 6 hours of a new WordPress stable release. Open an issue if that doesn't happen.

**Q:** What's the deal with `local-config.php`?  
**A:** It is for local development, which might have different MySQL credentials or do things like enable query saving or debug mode. This file is ignored by Git, so it doesn't accidentally get checked in. If the file does not exist (which it shouldn't, in production), then WordPress will use the DB credentials defined in `wp-config.php`.

**Q:** What is `memcached.php`?  
**A:** This is for people using memcached as an object cache backend. It should be something like: `<?php return array( "server01:11211", "server02:11211" ); ?>`. Programattic generation of this file is recommended.

**Q:** Does this support WordPress in multisite mode?  
**A:** Yes, as of WordPress v3.5 which was released in December, 2012.
