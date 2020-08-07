# Magento CLI Order Updater

## Compatibility

This module has been built and tested against Magento 2.4.0, it will likely work with previous versions but this is untested.

## Installation

The module can be installed via composer with the following commands.

```
composer config repositories.martinshaw-cli-order-updater git "https://github.com/shawesome/Magento-CLI-Order-Updater.git"
composer require shawesome/magento-cli-order-updater
```

Ensure your magento cache is flushed and your DI is recompiled (depending on your configuration).

## Usage

The command can be run with the following from the Magento install's root directory.

```
bin/magento martinshaw:orderupdate <identifier>
```

Where `<identifier>` is either an order's entity ID or a customer email address.identifier

Assuming there are matches, you'll then see a prompt showing a list of orders.

```
Found 2 matching order(s). 
+-----------+--------------+----------------+-------------+
| Entity ID | Increment ID | Customer Email | Grand Total |
+-----------+--------------+----------------+-------------+
| 1         | 000000001    | martin@foo.com | 36.3900     |
| 2         | 000000002    | martin@foo.com | 39.6400     |
+-----------+--------------+----------------+-------------+
```

You will be prompted for a new email address to update the listed orders.

