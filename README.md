<p align="center">
  <img width="375" height="150" src="https://github.com/phonetworks/commons-php/raw/master/.github/cover-smaller.png">
</p>

# Pho-CLI [![Build Status](https://travis-ci.org/phonetworks/pho-cli.svg?branch=master)](https://travis-ci.org/phonetworks/pho-cli)

A command line interface for the Phở stack. Allows you to build GraphQL files, initialize new projects, and expose a non-blocking event-driven RESTful API via HTTP or HTTPS.

## Getting Started

Phở-CLI is based on PHP 7.2+ 

The preferred method of installation is to use the pho-cli [PHAR](https://github.com/phonetworks/pho-cli/releases/download/0.2/pho.phar) which can be downloaded from the most recent Github Release. This method ensures you will not have any dependency conflict issue.

Alternatively, you may install pho-cli [through composer](https://getcomposer.org/).

```bash
git clone https://github.com/phonetworks/pho-cli/
cd pho-cli && composer install
```

You must have [Redis](https://redis.io/) and [Neo4J](https://neo4j.com/) installed for the projects to run on your system.

## Available Commands
  
**init**
Initializes a new project. This will start a dialog where you define the app name, description as well as the template of your project based on [Phở recipes](https://github.com/pho-recipes). 

> Usage: ```pho init``` 


**build**
Compiles the schema files. This must be run in a project folder that was initialized using the command above.

> Usage: ```bin/pho.php build``` 


## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-cli/blob/master/LICENSE).


