# Project Siren: Data
[![Build Status](https://travis-ci.org/HaojiXu/project-siren-data.svg?branch=master)](https://travis-ci.org/HaojiXu/project-siren-data)

Data side for Project Siren - a new, innovative, and intuitive representation for novelists.

# Setup

This project utilizes [Slim Framework](https://www.slimframework.com/docs/start/installation.html) to create a RESTful API with MySQL support.
```
# Install Slim Framework & Other Libraries (See a list below)
composer install

# Change the config.php to settings of your MySQL database.
nano config.php

# You're good to go!
```

You might need to import the Siren datastructure into your database. Using phpmyadmin, you can import the .sql file into your existing MySQL database.

# API Sample
`https://yourhost/api/api.php/all_chapters`
Get all articles, unsorted

`https://yourhost/api/api.php/all_works`
Get all works, unsorted

`https://yourhost/api/api.php/chapter/{id}`
Get a specific chapter

`https://yourhost/api/api.php/work/{id}`
Get a specific work

# Acknowledgement
This project could not be made without the help of:
- Slim Framework
- Monolog

# License
MIT License.
