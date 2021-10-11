
## Introduction

Hi, this is a skeleton app for your assessment. It will help you to deal your task without building the app from the scratch!

How to use it?
* Implement the business logic under `src/` directory
* Write your unit tests should be located in here `test/`

## How to start?

### Platform requirements
* Ubuntu or other linux based
* Docker 20+
            
This program should also work on Windows, but that was not tested.

### Firstly, install dependencies
```bash
docker-compose run --rm composer install
```

### How to run tests?
```bash
docker-compose run --rm phpunit
```

### How to run the program?

Help command with program documentation
```bash
docker-compose run --rm cli
```

List all NBA teams
```bash
docker-compose run --rm cli teams
```

List NBA teams by keyword
```bash
docker-compose run --rm cli teams boston
```
