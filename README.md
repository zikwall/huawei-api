### Huawei API

#### Example usage

```php
use zikwall\huawei_api\HuaweiClient;

$client = new HuaweiClient();
$client->setRedirectUri('localhost');
$client->setClientId('00000000000000');
$client->setClientSecret('f00000000000000000000000000000000000000000000000000000000');
$client->setProductId('00000000000000000000');

// or set from `agconnect-services.json`
// $client->setAuthConfig('agconnect-services.json');

print_r($client->fetchAccessToken());
```