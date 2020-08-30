![Main workflow](https://github.com/InfluxOW/XSolla-Marketplace/workflows/Main%20workflow/badge.svg)
[![Maintainability](https://api.codeclimate.com/v1/badges/a8fc1f3ef1be77fe7576/maintainability)](https://codeclimate.com/github/InfluxOW/XSolla-Marketplace/maintainability)
[![codecov](https://codecov.io/gh/InfluxOW/XSolla-Marketplace/branch/master/graph/badge.svg)](https://codecov.io/gh/InfluxOW/XSolla-Marketplace)

## Xsolla Marketplace
This is a final project of Xsolla Summer 2020 Backend School.\
http://influx-marketplace.herokuapp.com

## Requirements
You can check PHP dependencies with the `composer check-platform-reqs` command.

* PHP ^7.4
* Extensions:
    * mbstring
    * curl
    * dom
    * xml
    * zip
    * sqlite
    * json
    
## Development Setup
1. Run `make setup` to setup the project.
2. Run `make seed` to seed the database with fake data.
3. Run `make test` to run tests.

## Seller steps
1. Register an account with "seller" role.
2. Check if there is a game you want to sell keys for. If no, then store it.
3. Add new keys for an existing game at one of the available distributors.
4. Wait. Check your balance after a while. You will also receive a message to your server (when I'll implement feature to customize it LULW).

## Buyer steps
1. Register an account with "buyer' role.
2. Select a game you want to buy a key for and a distributor. Send request to purchase endpoint, it'll make key reserved for you.
3. You will receive your key after payment confirmation. To confirm it, send received token and your credit card number to the specified billing provider.
4. If you won't confirm your purchase within 1 hour, key will become available for purchase to another users again.
5. If your data was correct then you'll receive an email with the key.

## Additional information
There's also separate requests to check available platforms and distributors for them.
