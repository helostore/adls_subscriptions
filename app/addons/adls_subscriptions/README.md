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
```
# Check subscription expiration hourly
30 * * * * php hsw.php --dispatch=adls_cycle.check.expiration

# Check subscription alerts once a day
0 15 * * * php hsw.php --dispatch=adls_cycle.check.alerts
```

Debugging
-------
```
// Skip checking if production domains are valid (for testing purposes)
define('ADLS_SKIP_STRICT_DOMAIN_VALIDATION', true);
```

License
-------

License is available at: https://helostore.com/legal/license-agreement/

Copyright (C) 2017 by HELOstore
