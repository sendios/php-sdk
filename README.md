# Sendios API and PHP SDK
Sendios is email marketing company https://sendios.io <br>
Below you can find the technical documentation of integration with our platform.<br>
<b>See also integration guide in human language https://sendios.readme.io/docs </b><br>

Glossary:
- [Providing account info and credentials](https://github.com/sendios/php-sdk#providing-account-info-and-credentials)
- [Prepare your sender domain](https://github.com/sendios/php-sdk#setup-dns-records)
- [Sending email](https://github.com/sendios/php-sdk#sending-email-via-php-sdk)
- [Webhooks about emails](https://github.com/sendios/php-sdk#webhooks-about-sending-status)
- [Email validation](https://github.com/sendios/php-sdk#check-email)
- [User info](https://github.com/sendios/php-sdk#user-info)
- [User's custom fields](https://github.com/sendios/php-sdk#create-and-update-user-data)
- [Unsubscribe](https://github.com/sendios/php-sdk#unsubscribe)
- [Online on product](https://github.com/sendios/php-sdk#online)
- [Payments on product](https://github.com/sendios/php-sdk#payments)

# You can use PHP library, оr regular API via cURL

## Installing PHP SDK
```shell
composer require sendios/php-sdk
```

## Providing account info and credentials
```php
# PHP SDK
$clientId = 123;
$clientToken = 'a1s2d3f4g5h6j7k8l';
$sendios = new \Sendios\SendiosSdk($clientId, $clientToken);
```

```shell
# console cURL
curl -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 957081746b54977d51bef9fc74f4d4fd023bab13 is sha1 of clientToken (a1s2d3f4g5h6j7k8l)
```

Error with text "Authorization base64 data wrong or invalid" means that you provided wrong clientId or clientToken

## Setup DNS records
You have to setup SPF/DKIM records for sending emails from your domain.<br>
Read more at [Sender Domain Settings](https://github.com/sendios/docs/wiki/Sender-Domain-settings)


## Sending email via PHP SDK
```php
$email = 'test@example.com'; // for matching or creating user

$projectId = 1; // in your admin panel
$typeId = 1; // letter id (aka type_id)
$categoryId = $sendios->push->getCategorySystem(); // system or trigger

$letterData = [
    'some' => 'hi',
    'letter' => 'John',
    'variables' => '!',
];

$sendios->push->send($typeId, $categoryId, $projectId, $email, [], $letterData, []);
# response: {"queued":true}
```

Additionally you can add meta info for tracking
```php
// Your data, that will be sent with our webhooks
$meta = [
    'tracking_id' => 72348234,
];
```


## Sending email via cURL
```shell
curl -X POST https://api.sendios.io/v1/push/system \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'
```
```json
{
    "type_id": 1,
    "category": 1,
    "client_id": 123,
    "project_id": 1,
    "data": {
        "user": {
            "email": "test@example.com"
        },
        "some": "hi",
        "letter": "John",
        "variables": "!"
    },
    "meta": {
        "tracking_id": 72348234
    }
}
```
push/system or https://api.sendios.io/v1/push/trigger for trigger letters <br>
957081746b54977d51bef9fc74f4d4fd023bab13 is sha1 of clientToken (a1s2d3f4g5h6j7k8l)

# Requesting events
Our system can choose best time to notify user, and request sending trigger email to him.<br>
For this purpose you can build API point, that receives tris request, and sends trigger letter <br>
Your API point will receive POST request with theese parameters:
```json
{
    "project_id": 123, // Sendios
    "email": "test@example.com",
    "type_id": 1,
    "source_id": 1
}
```
Optional parameters:
```json
{
    "client_user_id": 123234, // Your userId on product
    "token": "awo7tiwafhiajwk8ehc"
}
```
You dont need to provide any response for this request. Only server 200 OK

### How you can test your API point with this cURL request:
```shell
curl -X POST https://api.yourproduct.com/sendios/requestevent \
    -d '{"project_id": 123, "email": "test@example.com", "type_id": 1}'
```

# Webhooks about sending status
You can setup webhooks in project's settings (admin panel) URL API point for receiving events than happens with letter.
For example you can use service https://webhook.site that alllow watch events online.

Example of webhooks:
- queue
- reject
- sent
- open
- click

And reason of rejecting (email not sent):<br>
<img src="https://github.com/sendios/php-sdk/blob/master/docs/webhook_example.png?raw=true" width="400px">

More about webhooks https://github.com/sendios/docs/wiki/Webhooks


# Check email
Email validation
```php
$result = $sendios->email->check('Test@Example.com');
```

Result:
```json
{
    "reason":"mx_record",
    "orig":"Test@Example.com",
    "valid":false,
    "email":"Test@Example.com",
    "vendor":"Unknown",
    "domain":"Example.com",
    "trusted":false
}
```

```shell
curl -X POST https://api.sendios.io/v1/email/check \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"email":"Test@Example.com"}'
```

# User
## Get user info
```php
$projectId = 1;
// Make GET to /user/project/PROJECT_ID/email/Test@Example.com
$user = $sendios->user->getByEmail('Test@Example.com', $projectId);
/* Returned array(
    "id":8424,
    "project_id":1,
    "email":"test@example.com",
    "name":"John",
    "gender":"m",
    "country":"UKR",
    "language":"en",
    ...
) */
```

```shell
curl -X GET https://api.sendios.io/v1/user/project/1/email/test@example.com \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

## Providing user_id on Product
We call that Client's User ID
```php
$email = 'john@gmail.com';
$projectId = 1;
$clientUserId = 1234;

$sendios->clientUser->create($email, $projectId, $clientUserId);
```

```shell
curl -X POST https://api.sendios.io/v1/clientuser/create \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'
```

```json
JSON_DATA
{
    "email": "john@gmail.com",
    "project_id": 1,
    "client_user_id": 1234 
}
```

## Create and update user data
```php
$fields = [
    'name' => 'John Dou',
    'gender' => 'm', //m or f
    'age' => 21, //int
    'photo' => 'http://moheban-ahlebeit.com/images/Face-Wallpaper/Face-Wallpaper-26.jpg',//image url
    'ak' => 'FFZxYfCfGgNDvmZRqnELYqU7',//Auth key
    'vip' => 1, //int
    'language' => 'es', //ISO 639-1
    'country' => 'esp', //ISO 3166-1 alpha-3 or ISO 3166-1 alpha-2
    'platform_id' => $sendios->user->getPlatformDesktop(),
    'list_id' => 1,
    'status' => 0, //int
    'partner_id' => 1, //int

    // Your own custom fields may be here
    // allowed only int values
    'field1' => 542, //int
    'sessions_count' => 22, //int
    'session_last' => 1498137772, //unix timestamp
];
```
By email and project ID

```php
$result = $sendios->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
// $result is a boolean status
```

```shell
curl -X PUT https://api.sendios.io/v1/userfields/project/1/emailhash/dGVzdEBleGFtcGxlLmNvbQ== \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```

```json
JSON_DATA
{
    "name": "John Dou",
    "gender": "m",
    "age": 21,
    "photo": "http://moheban-ahlebeit.com/images/Face-Wallpaper/Face-Wallpaper-26.jpg",
    "ak": "FFZxYfCfGgNDvmZRqnELYqU7",
    "vip": 1,
    "language": "es",
    "country": "esp",
    "platform_id": 1,
    "list_id": 1,
    "status": 0, 
    "partner_id": 1,
    "field1": 542,
    "sessions_count": 22,
    "session_last": 1498137772
}
```

By user

```php
$user = $sendios->user->getById(892396028);
$result = $sendios->user->setUserFieldsByUser($user, $fields);
// $result is a boolean status
```

```shell
curl -X PUT https://api.sendios.io/v1/userfields/user/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 5234 - user_id
```

```json
JSON_DATA
{
    "field1": 542,
    "field2": 22,
    "field3": 1498137772
}
```

## Get user custom fields

```php
$result = $sendios->user->getUserFieldsByEmailAndProjectId('ercling@yandex.ru', 1);
// or
$result = $sendios->user->getUserFieldsByUser($user);
/*
Returns [
    'user' => [
        'id' => 892396028,
        'project_id' => 1,
         ...
    ],
    'custom_fields' => [
        'sessions_count' => 22,
         ...
    ],
]
*/
```

```shell
curl -X GET https://api.sendios.io/v1/userfields/user/5234\
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id     
```

```shell
curl -X GET https://api.sendios.io/v1/userfields/project/1/email/test@example.com \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```


# Unsubscribe
Let us know that user don't want to reveice email notifications.<br>
If user unsubscribed in our system - we dont pass any emails to him (exept system).<br><br>

For unsub at settings page in your product - use addBySettings method:
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
$unsub = $sendios->unsub->addBySettings($user);
```
```shell
curl -X POST https://api.sendios.io/v1/unsub/8424/source/9 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

For unsub at your internal admin panel e.g. support - use addByClient method:
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
$unsub = $sendios->unsub->addByClient($user);
```
```shell
curl -X POST https://api.sendios.io/v1/unsub/8424/source/8 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```



## Subscribe back
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
// Make DELETE to /unsub/USER_ID
$unsub = $sendios->unsub->subscribe($user);
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsub/8424 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```
## Unsubscribe by admin

```php
$projectId = 123;
$result = $sendios->unsub->unsubByAdmin('test@example.com',$projectId);

/*
success result
array(1) {
  'unsub' => bool(true)
}
error result (already unsubscribed)
array(1) {
  'unsub' => bool(false)
}
*/
```

```shell
curl -X POST https://api.sendios.io/v1/unsub/admin/1/email/dGVzdEBleGFtcGxlLmNvbQ== \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```

## Check is unsubscribed
By user:
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
if($user) {
$unsub = $sendios->unsub->isUnsubByUser($user); // Returns false(if not unsubscribed) or unsub data
}
```

By email and project:
```php
$projectId = 1;
$unsub = $sendios->unsub->isUnsubByEmailAndProjectId('test@example.com', $projectId); // Returns false(if not unsubscribed) or unsub data
```

```shell
curl -X GET https://api.sendios.io/v1/unsub/isunsub/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id    
```

## Get unsubscribe reason

```php
$projectId = 123;
$result = $sendios->unsub->getUnsubscribeReason('test@example.com',$projectId);

//user does not unsubscribed
array(1) {
  'result' => bool(false)
}

//reason for the unsubscription is unknown
array(1) {
  'result' => string(7) "Unknown"
}

//success result
array(1) {
  'result' => string(5) "admin"
}
```

```shell
curl -X GET https://api.sendios.io/v1/unsub/unsubreason/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id    
```

## Get unsubscribed list

```php
<?php

$result = $sendios->unsub->getByDate('2018-06-10');

//Response example
return [
    0 => [
        'email' => "jo23lu56@gmail.com",
        'project_id' => 9,
        'client_user_id' => NULL,
        'source_id' => 9,
        'created_at' => "2018-02-27 20:31:45"
    ],
    // ...
];
```
```shell
curl -X GET https://api.sendios.io/v1/unsub/list/1262307661 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# timestamp(2010-01-01) = 1262307661    
```

## Get unsubscribed list with pagination

```php
<?php
//@var $date  string (required)
//@var $page  int (required)
//@var $pageSize  int (optional)
$date = '2018-06-10';
$page = 1;
$pageSize = 100;
$result = $sendios->unsub->getListByDate($date, $page, $pageSize);

//Response example
return [
    [
        'pages' => 6,
        'current_page' => 1,
        'size' => 100,
        'data' => [
            'email' => "jo23lu56@gmail.com",
            'project_id' => 9,
            'client_user_id' => NULL,
            'source_id' => 9,
            'created_at' => "2018-02-27 20:31:45"
        ],
        //...
    ]
];
```
```shell
curl -X GET https://api.sendios.io/v1/unsub/list/1262307661/1?page_size=1000 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# timestamp(2010-01-01) = 1262307661    
```

# Unsubscribe from types
### Get current unsubs
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
$list = $sendios->unsubTypes->getList($user);
//returns array {
//  [0] =>
//  array(3) {
//    'type_id' =>
//   int(3)
//    'unsubscribed' =>
//    bool(false)
//    'name' =>
//    string(11) "Popular now"
//  },
//  ...
//}
```

```shell
curl -X GET https://api.sendios.io/v1/unsubtypes/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id  
```

### Unsubscribe user from types 4 and 5
```php
$sendios->unsubTypes->addTypes($user, [4, 5]);
```
```shell
curl -X POST https://api.sendios.io/v1/unsubtypes/nodiff/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"type_ids": [4, 5]}'
```

### Subscribe user back to types 4 and 5
```php
$sendios->unsubTypes->removeTypes($user, [4, 5]);
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsubtypes/nodiff/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"type_ids": [4, 5]}'
```

### Subscribe user back to all types
```php
$sendios->unsubTypes->removeAll($user); 
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsubtypes/all/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

# Online
Online on product. Useful in loggics and personalization
```php
$sendios->user->setOnlineByUser($user, new \DateTime());
```
```shell
curl -X PUT https://api.sendios.io/v3/users/5234/online \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 5234 - user_id 
```
```json
JSON_DATA
{
    "timestamp": "2010-01-01T08:15:30-01:00",
    "user_id": 5234
}
```
Update online by user email
```php
$sendios->user->setOnlineByEmailAndProjectId('ercling@gmail.com', 1, new \DateTime());
```
```shell
curl -X PUT https://api.sendios.io/v3/users/project/1/email/dGVzdEBleGFtcGxlLmNvbQ==/online \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```
```json
JSON_DATA
{
    "timestamp": "2010-01-01T08:15:30-01:00",
    "project_id": 1,
    "encoded_email": "dGVzdEBleGFtcGxlLmNvbQ==" 
}
```

# Payments
```php
$startDate = 1509617696; //Payment date or subscription start date 
$expireDate = 1609617696; //Subscription end date (optional, default false)
$paymentCount = 14; //Pay count (optional, default false)
$paymentType = 1; //Pay type (optional, default false)
$amount = 20; //Pay amount (optional, default false)
$mailId = 12244; //Mail id (optional, default false)
```
By email and project ID

```php
$result = $sendios->user->cratePaymentByEmailAndProjectId('ercling@yandex.ru', 2, $startDate, $expireDate, $paymentType, $amount, $mailId);
// $result is a boolean status
```

By user

```php
$user = $sendios->user->getById(892396028);
$result = $sendios->user->createPaymentByUser($user, $startDate, $expireDate, $paymentType, $amount, $mailId);
// $result is a boolean status
```

```shell
curl -X POST https://api.sendios.io/v1/lastpayment \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'
```

```json
JSON_DATA
{
    "user_id": 1, //User id in sendios
    "start_date": "1509617696", //Payment date or subscription start date 
    "expire_date": "1609617696", //Subscription end date (optional, default false)
    "total_count": "14", //Pay count (optional, default false)
    "payment_type": "1", //Pay type (optional, default false)
    "amount": "20" //Pay amount (optional, default false)
    "mail_id": "12244" //Mail id(optional, default false)
}
```

Attempt to send incorrect data
```php
$sendios = new \Sendios(3,'GH3ir1ZDMjRkNzg4MzgzE3MjU');
$fields = [
    'language' => 'ua',
    'gender' => 'male',
    'vip' => 'yes',
];
$result = $sendios->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
if (!$result){
    var_dump($sendios->request->getLastResponse()->getData());
}

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(3) {
//    'language' =>
//    string(45) "Field language is not valid language code: ua"
//    'gender' =>
//    string(41) "Field gender must be a part of list: m, f"
//    'vip' =>
//    string(44) "Field vip does not match the required format"
//  }
//}
```

# Other

## Get response (if $result === false)
```php
$response = $sendios->request->getLastResponse()->getData();

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(1) {
//    'field_name' =>
//    string(29) "Can't find user field: field2"
//  }
//}
```

## Error handling
By default any error messages (except InvalidArgumentException in Sendios constructor) collects in error_log.
If you want the component throws exceptions just change handler mode:
```php
$sendios = new Sendios($clientId, $clientHash);
$sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
```

## Set curl options for single request
```php
$sendios = new Sendios($clientId, $clientHash);
$sendios->request->setOption(CURLOPT_TIMEOUT, 2);
```

## Set curl options for multiple requests (permanent)
```php
$sendios->request->setOption(CURLOPT_TIMEOUT_MS, 2000, true);
```

## Reset permanent curl options
```php
$sendios->request->resetPermanentOptions();
```
