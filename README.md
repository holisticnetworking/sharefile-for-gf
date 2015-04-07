ShareFile for Gravity Forms
===========================
Contributors: DragonFlyEye  
Donate link: https://holisticnetworking.nt/donate/  
Tags: sharefile,hippa,upload,  
Requires at least: 4.1  
Tested up to: 4.1  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Provides an interface for WordPress websites to allow uploading to a specific directory on ShareFile.

Description
===========
For developers seeking to utilize Citrix ShareFile's highly-secure uploading, this plugin will allow users to upload files to a folder specified by the administrator of the site.  

**Note that this plugin was developed with a specific set of needs in mind. Your mileage may vary, but I'm perfectly willing to extend the feature set for those who request it.**  

You _must_ have Gravity Forms installed on your site in order for this plugin to function correctly.

Installation
============
Simply go to Plugins > Add New and search for ShareFile for Gravity Forms. Install the plugin and head over to Gravity Forms > Settings > ShareFile Settings. You'll need to get your ShareFile API key, including the Client ID and Secret, in order to proceed.

FAQ
===
**Does this plugin allow for downloading?**  
No. This plugin assumes you need secure upload abilities, file downloads must be handled separately.
**What permissions should I give the uploading user account?**
Security is different for everyone. Still, especially if you're using ShareFile for it's HIPPA compliance, the rights given to the user account you use for uploading through this plugin should be limited.

Consider creating a "drop box" account, which only has rights to upload to a specific directory, without the ability to either create new directories, nor to download files. This ensures that any potential breach of WordPress doesn't allow a hacker to download sensitive information.
