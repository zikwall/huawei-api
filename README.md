### Huawei API

#### Example usage

```php
use zikwall\huawei_api\HuaweiClient;

$client = new HuaweiClient();
$client->setRedirectUri('localhost');
$client->setClientId('00000000000000');
$client->setClientSecret('f00000000000000000000000000000000000000000000000000000000');
$client->setProductId('00000000000000000000');

//print_r($client->fetchAccessToken());

use zikwall\huawei_api\services\HuaweiServiceSubscription;
use zikwall\huawei_api\services\HuaweiServiceOrder;

$subscriptions = new HuaweiServiceSubscription($client->getAccessToken(), $client->getRegion());

print_r($subscriptions->getSubscription('token', 'id'));

$orders = new HuaweiServiceOrder($client->getAccessToken(), $client->getRegion());

print_r($orders->verifyToken('id', 'token'));
```
