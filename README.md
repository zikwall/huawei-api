### Huawei API

#### Example usage API

```php
use zikwall\huawei_api\HuaweiClient;

$client = new HuaweiClient();
$client->setRedirectUri('localhost');
$client->setClientId('00000000000000');
$client->setClientSecret('f00000000000000000000000000000000000000000000000000000000');
$client->setProductId('00000000000000000000');

// or setup in initialization step

$client = new HuaweiClient([
    'credentials' => [
        // ..
    ],
    // or setup each separately
    'client_id' => '00000000000000',
    'client_secret' => 'f00000000000000000000000000000000000000000000000000000000',
]);

// or set config properties from default configuration JSON file (download from huawei dashboard)
$client->setAuthConfigFile('agconnect-services.json');
// or
$client->setAuthConfig('agconnect-services.json');

//print_r($client->fetchAccessToken());

use zikwall\huawei_api\services\HuaweiServiceSubscription;
use zikwall\huawei_api\services\HuaweiServiceOrder;

$subscriptions = new HuaweiServiceSubscription($client->getAccessToken(), $client->getRegion());

print_r($subscriptions->getSubscription('token', 'id'));

$orders = new HuaweiServiceOrder($client->getAccessToken(), $client->getRegion());

print_r($orders->verifyToken('id', 'token'));
```

### OAuth2

- [x] TODO
