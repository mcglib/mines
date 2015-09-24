# mines
MINES survey integration with OCLC's hosted EZProxy.

# Features
* Installer for setting everything up quickly
* Configuration based, designed for flexibility
  * Enable/disable toggle and/or by date-range
  * Track session counts, and how often surveys are presented.
  * Force completion
  * Show thanks page, and specify auto-redirection time in seconds
  * Questions are presentable as dropdowns or radio selects
  * Questions can allow multiple choices (displayed as checkboxes)
  * Questions can allow 'other' answers as a free-text field
* Run multiple surveys at the same time
* Add new surveys with complete control over how submitted data is validated, stored, processed, and report on
* Import and export surveys as needed
* ARL report built in.

# Requirements
* Linux / Unix / Mac OS X
* Apache 2.2+
* PHP 5.3+
* MySQL 5.1+

# Installation
1. Change into a non-webroot destination directory.
```cd $non_webroot_destination```

2. Clone this repo into the directory $non_webroot_destination.
```git clone https://github.com/mcglib/mines.git```

3. Create a symlink inside the webroot as follows.
```ln -s $non_webroot_destination/mines/public mines```

4. Configure, the survey system by creating a config.php file. Start with a template. ```cp config/config.php.sample config/config.php```

5. Edit config/config.php as necessary.

6. Install the survey system as follows. ```php scripts/install.php```

7. Visit http://hostname/mines?id=s1&url=http://www.yahoo.com

8. Visiting http://hostname/mines?url=http://www.yahoo.com relies on the 'default-survey-id' value in config/config.php.

# Adding a new survey

1. Create a survey from a template. Note that s1 and s2 are survey IDs.
```cp config/surveys/s1.tsv config/surveys/s2.tsv```

2. Edit config/surveys/s2.tsv in a spreadsheet and adjust the survey to taste. Be sure to keep the IDs at the beginning of each line unique.

3. Define how this survey is processed. Start with a template. ```cp config/surveys/s1.php config/surveys/s2.php```

4. Edit config/surveys/s2.php such that survey submissions are collected, processed, stored, and reported upon, as needed.

5. Edit config/config.php such that the 'surveys' key looks as follows.
```php
'surveys' => function(){
        return array(
                // survey_id => surveys/survey_id.php
                's1' => include('surveys/s1.php'),
                
                // New survey configured here...
                's2' => include('surveys/s2.php'),
        );
},
```
6. Finally, import the survey. ```php scripts/import.php s2```

7. Visit http://hostname/mines?id=s2&url=http://www.yahoo.com

# Integration with OCLC Hosted EZproxy, using self-service
1. Copy ezproxy/expert/docs/survey.htm.sample to ezproxy/expert/docs/survey.htm.
2. Edit the hostname in ezproxy/expert/docs/survey.htm.
3. Transfer ezproxy/expert/docs/survey.htm to scp.oclc.org:expert/docs/survey.htm.
4. Transfer ezproxy/expert/survey.txt to scp.oclc.org:expert/survey.txt.
5. Append the contents of ezproxy/expert/ezproxy.cfg.sample to scp.oclc.org:expert/ezproxy.cfg
6. Prepend the contents of ezproxy/expert/shibuser.txt.sample to scp.oclc.org:expert/shibuser.txt

# Creating an ARL report
Type the following at the command line.
```
php scripts/arl-report.php s1
```

# Generating a status report
Type the following at the command line.
```
php scripts/status.php s1
```

# Automating daily email of status reports
1. Type the following at the command line.
```
crontab -e
```
2. Append the following.
```
0 0 * * * php /path/to/mines/scripts/status.php s1 | mail -s "MINES status" "email1,email2,email3"
```

