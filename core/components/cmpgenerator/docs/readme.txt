--------------------
CMPGenerator
--------------------
Version: 1.1.0 pl
Date: March 14, 2012
Author: Joshua Gulledge (jgulledge19@hotmail.com)
License: GNU GPLv2 (or later at your option)

Description
    CMPGenerator is intended to be used by PHP developers that want to create custom 
    database tables to be used via a snippet, plugin or CMP. CMPGenerator will create 
    the xpdo scheme files and xpdo classes for your custom database tables with just a 
    click of a button.  This allows you to quickly start using xpdo in your custom projects. 

Install
 - Install via the package manager

How to use
1. Create your database tables(s) via your preferred method - phpmyadmin, SQLYog, ect..
    Note your auto increment primary key should be named id
2. Now create a new Package
    A. Choose a unique name, it is a good idea to create a prefix for your packages.  
        Example you could use your initials like First Middle Last: fmlMyCustomPackage
        Also make sure you only use alpanumberic values 
    B. List the tables that you just created via a comma separated list
    C. Put in the prefix for the table if any.  It is best practice to use the same prefix as your MODX install does.
    D. Select if you want to build the schema.  If you don't do this you can't use your tables.
    E. Select build Package and this will generate all nessicary files.
3. Once the files are created and if you are using tables that have a relationship you will want to manually add that 
    code in the file:  core/components/YOUR-CMP/model/YOUR-CMP/YOUR-CMP.mysql.custom.schema.xml
    See Docs for more info: http://rtfm.modx.com/display/revolution20/Using+Custom+Database+Tables+in+your+3rd+Party+Components
    Once you have updated this file to show the relastionships you can now regenerate the package.  Set the Build Scheme to No
    and set Build Package to Yes and save.


Example of schema and foreign DB:
- http://devtrench.com/posts/first-impressions-of-xpdo-wordpress-to-modx-migration-tool
- http://devtrench.com/posts/wordpress-to-modx-migration-part-2-schema-relationships-and-comments
- http://devtrench.com/posts/wordpress-to-modx-migration-part-3-templates-categories-and-postmeta


