ADLS Subscriptions for CS-Cart
==============================

**ADLS Subscriptions** is an add-on for CS-Cart e-commerce platform that integrates subscriptions into your store.


Setup
-----

1. Install the add-on.
1. Add a new order status "Expired" to which suspended subscription's order status will be changed into.
1. Go to add-on's settings and choose this new status.
1. Add a plan.
1. Assign the plan to a product option. The option can have multiple variants specifying the initial period to be paid for (`position` field = number of months).`
1. Assign the product option to the desired products.
1. Run a test, buy the product.


Crons
-----
# Check subscription expiration hourly
30 * * * * php hsw.php --dispatch=adls_cycle.check.expiration

# Check subscription alerts once a day
0 15 * * * php hsw.php --dispatch=adls_cycle.check.alerts


Migration to subscription tier
------------------------------

```
TRUNCATE TABLE cscart_adlss_subscriptions;
TRUNCATE TABLE cscart_adlss_subscription_payments;
TRUNCATE TABLE cscart_adls_releases;
TRUNCATE TABLE cscart_adls_release_links;
```


Go to http://local.helostore.com/hsw.php?dispatch=adls_migrate.view and run the partially automated migration scripts.

Or run the scripts independently, one by one:

1. Update settings:
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.settings
1. Disable EDP feature in old orders items
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.products_edp
1. Remove Sidekick's product options from orders and product itself
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.sidekick_convert
1. Assign subscribable product option to all addons/products:
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.products
1. Add release data for each addon
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.releases
1. Migrate current customers to a default Subscription Plan + notify them
    http://local.helostore.com/hsw.php?dispatch=adls_migrate.subscriptions


License
-------

License is available at: https://helostore.com/legal/license-agreement/

Copyright (C) 2017 by HELOstore
