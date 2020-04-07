# Pho-CLI [![Build Status](https://travis-ci.org/phonetworks/pho-cli.svg?branch=master)](https://travis-ci.org/phonetworks/pho-cli)

A command line interface for the Phở stack. Allows you build graphql files, initialize new projects, and expose a non-blocking event-driven RESTful API via HTTP or HTTPS.

## Getting Started

The preferred method of installation is to use the pho-cli PHAR which can be downloaded from the most recent Github Release. This method ensures you will not have any dependency conflict issue.

Alternatively, you may install pho-cli [through composer](https://getcomposer.org/).

```bash
git clone https://github.com/phonetworks/pho-cli/
cd pho-cli && composer install
```
## Available Commands
  
**init**
Initializes a new project. This will start a dialog where you define the app name, description as well as the template of your project based on [Phở recipes](https://github.com/pho-recipes). 

> Usage: ```pho init``` 


**build**
Compiles the schema files. This must be run in a project folder that was initialized using the command above.

> Usage: ```bin/pho.php build``` 


## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-cli/blob/master/LICENSE).


